<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use App\Traits\HasPermissionChecks;
use Illuminate\Support\Facades\DB;
use App\Models\OrderPayment;
use App\Models\Order;
use App\Models\PaymentMethod;
use Carbon\Carbon;

class PaymentController extends Controller
{
    use HasPermissionChecks;

    public function index(Request $request): Response
    {
        $this->authorizeAction('payment.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "created_at");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);
        $payment_status = $request->input('payment_status');
        $payment_method_id = $request->input('payment_method_id');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filters = [];

        if (!empty($payment_status)) {
            $filters[] = [
                'id' => 'payment_status',
                'value' => $payment_status
            ];
        }

        if (!empty($payment_method_id)) {
            $filters[] = [
                'id' => 'payment_method_id',
                'value' => $payment_method_id
            ];
        }

        $query = OrderPayment::with('paymentMethod', 'order.user')
            ->when($search, function ($query, $search) {
                $operator = DB::getDriverName() === "pgsql" ? "ilike" : "like";
                $query->where(function ($q) use ($search, $operator) {
                    $q->where("notes", $operator, "%{$search}%")
                    ->orWhere("amount", $operator, "%{$search}%");
                });
            })
            ->when($payment_status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->when($payment_method_id, function ($query, $id) {
                $query->where('payment_method_id', $id);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            })  ->whereHas('order', function ($q) {
        $q->where('status', '!=', OrderStatus::IN_CART);
    });

        $items = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

        $paymentMethods = PaymentMethod::all()->map(function ($method) {
            return [
                'id' => $method->id,
                'name' => $method->name,
            ];
        });

        return Inertia::render("Payment/Index", [
            "data" => [
                "data" => $items->items(),
                "current_page" => $items->currentPage(),
                "per_page" => $items->perPage(),
                "last_page" => $items->lastPage(),
                "total" => $items->total(),
                "search" => $search,
                "sort_field" => $sortField,
                "sort_direction" => $sortDirection,
                "start_date" => $startDate,
                "end_date" => $endDate,
                "filter" => $filters,
            ],
            "payment_methods" => $paymentMethods,
        ]);
    }

    public function Show(Order $order): Response
    {
        $this->authorizeAction('order.view');
        
        $order = Order::with('user', 'delivery', 'address', 'orderPayment.paymentMethod', 'orderItems' ,'orderItems.modification')->find($order->id);
        return Inertia::render("Order/Show", [
            'order' => $order,
        ]);
    }
}
