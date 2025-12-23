<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use App\Traits\HasPermissionChecks;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use App\Models\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\OrderService;

class OrderController extends Controller
{
    use HasPermissionChecks;
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorizeAction('order.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "order_number");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);
        $delivery_id = $request->input('delivery_id', null);
        $status = $request->input('status', null);
        $payment_status = $request->input('payment_status', null);
        $payment_method_id = $request->input('payment_method_id', null);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');


        $filters = [];
        if (!empty($delivery_id)) {
            $filters[] = [
                'id' => 'delivery_id',
                'value' => $delivery_id
            ];
        }
        if (!empty($status)) {
            $filters[] = [
                'id' => 'status',
                'value' => $status
            ];
        }
        
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

        if (!empty($startDate) || !empty($endDate)) {
            $filters[] = ['id' => 'date_range', 'value' => [$startDate, $endDate]];
        }


        $query = Order::with('user', 'delivery', 'address', 'orderPayment.paymentMethod')
            ->where('status', '!=', OrderStatus::IN_CART)
            ->when($search, function ($query, $search) {
                $isPostgres = DB::getDriverName() === 'pgsql';
                $likeOperator = $isPostgres ? 'ilike' : 'like';

                $query->where(function ($q) use ($search, $likeOperator) {
                    $q->Where("id", $likeOperator, "%{$search}%")
                    ->orWhere("delivery_id", $likeOperator, "%{$search}%")
                     ->orWhere("order_number", $likeOperator, "%{$search}%")
                    ->orWhere("status", $likeOperator, "%{$search}%")
                    ->orWhereHas("user", function ($uq) use ($search, $likeOperator) {
                        $uq->where("first_name", $likeOperator, "%{$search}%")
                        ->orWhere("last_name", $likeOperator, "%{$search}%")
                            ->orWhere("id", $likeOperator, "%{$search}%");
                    })
                     ->orWhereHas("delivery", function ($uq) use ($search, $likeOperator) {
                        $uq->where("name", $likeOperator, "%{$search}%")
                            ->orWhere("id", $likeOperator, "%{$search}%");
                    });
                });
            })


        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
        })

         ->when($delivery_id, function ($query, $delivery_id) {
                if (is_array($delivery_id) && !empty($delivery_id)) {
                    $query->whereIn('delivery_id', $delivery_id);
                }
            })
        ->when($status, function ($query, $status) {
            if (is_array($status) && !empty($status)) {
                $query->whereIn('status', $status);                }
        })

        ->when($payment_method_id, function ($query, $payment_method_id) {
            if (is_array($payment_method_id)) {
                $query->whereHas('orderPayment', function ($q) use ($payment_method_id) {
                    $q->whereIn('payment_method_id', $payment_method_id);
                });
            } else {
                $query->whereHas('orderPayment', function ($q) use ($payment_method_id) {
                    $q->where('payment_method_id', $payment_method_id);
                });
            }
        })
    
        ->when($payment_status, function ($query, $payment_status) {
            if (is_array($payment_status) && !empty($payment_status)) {
                $query->whereHas('orderPayment', function ($q) use ($payment_status) {
                    $q->whereIn('payment_status', $payment_status);
                });
            } elseif (is_string($payment_status)) {
                $query->whereHas('orderPayment', function ($q) use ($payment_status) {
                    $q->where('payment_status', $payment_status);
                });
            }
        });
        $items = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

        $deliveries = Delivery::all()->map(function ($delivery) {
            return [
                'id' => $delivery->id,
                'name' => $delivery->name,
            ];
        });

        $paymentMethods = PaymentMethod::all()->map(function ($method) {
            return [
                'id' => $method->id,
                'name' => $method->name,
            ];
        });

        return Inertia::render("Order/Index", [
            "data" => [
                "data" => $items->items(),
                "current_page" => $items->currentPage(),
                "per_page" => $items->perPage(),
                "last_page" => $items->lastPage(),
                "total" => $items->total(),
                "search" => $search,
                "sort_field" => $sortField,
                "sort_direction" => $sortDirection,
                "filter" => $filters
            ],
            'deliveries' => $deliveries,
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

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeAction('order.edit');

        $validatedData = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in([
                    OrderStatus::PENDING,
                    OrderStatus::CONFIRMED,
                    OrderStatus::PROCESSING,
                    OrderStatus::SHIPPED,
                    OrderStatus::DELIVERED,
                    OrderStatus::CANCELLED
                ])
            ],
            'payment_status' => [
                'nullable', // Made optional
                'string',
                Rule::in([
                    PaymentStatus::Pending,
                    PaymentStatus::Approved,
                    PaymentStatus::Declined,
                    PaymentStatus::Refunded,
                    PaymentStatus::Cancelled
                ])
            ],
        ]);

        try {
            $this->orderService->updateOrderStatus($order, $validatedData);

            return back()->with('success', value: trans('order.messages.updated_successfully'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => trans('order.messages.update_failed')
            ]);
        }
    }


    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeAction('order.edit');

        $validatedData = $request->validate([
            'payment_status' => [
                'required',
                'string',
                Rule::in([PaymentStatus::Pending, PaymentStatus::Approved, PaymentStatus::Declined, PaymentStatus::Refunded, PaymentStatus::Cancelled])
            ],
        ]);

        try {
            DB::beginTransaction();

            if (!$order->orderPayment) {
                return back()->withErrors([
                    'error' => trans('order.messages.no_payment_record')
                ]);
            }

            $order->orderPayment->update([
                'payment_status' => $validatedData['payment_status'],
                'paid_at' => $validatedData['payment_status'] === PaymentStatus::Approved && !$order->orderPayment->paid_at
                    ? now()
                    : $order->orderPayment->paid_at,
            ]);

            DB::commit();

            return back()->with('success', trans('order.messages.payment_status_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => trans('order.messages.update_failed')
            ]);
        }
    }

    public function downloadInvoice(Order $order)
    {
        $order = Order::with('user', 'delivery', 'address', 'orderPayment.paymentMethod', 'orderItems' ,'orderItems.modification')->find($order->id);
        
        $pdf = Pdf::loadView('invoices.show', ['order' => $order]);

        return $pdf->stream("BetaMoreLimited-{$order->order_number}.pdf");
    }
}
