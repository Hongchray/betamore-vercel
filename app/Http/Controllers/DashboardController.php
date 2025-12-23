<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use App\Traits\HasPermissionChecks;
use Illuminate\Support\Facades\DB;
use App\Enums\UserType;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Enums\OrderStatus;
class DashboardController extends Controller
{
    use HasPermissionChecks;

    public function index(Request $request): Response
    {
        $this->authorizeAction('dashboard.view');

        // Get date range from request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // If neither date is provided, default to the last 30 days
        if (!$startDate && !$endDate) {
            $startDate = now()->subDays(29)->startOfDay(); // 30 days including today
            $endDate = now()->endOfDay();
        }

        // Parse date strings to Carbon instances if necessary
        if ($startDate && !($startDate instanceof Carbon)) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        if ($endDate && !($endDate instanceof Carbon)) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $orderCount = Order::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->where('status', '!=', OrderStatus::IN_CART)
        ->count();

        $totalCustomer = User::where('type', UserType::CUSTOMER)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })->count();

        // Pass date range to revenue statistics
        $revenueStats = $this->getRevenueStatistics($startDate, $endDate);
        $topUsers = $this->getTopUsers($startDate, $endDate);
        $monthlyData = $this->getMonthlyData($startDate, $endDate);

        return Inertia::render('Dashboard', [
            'data' => [
                'total_orders' => $orderCount,
                'total_customer' => $totalCustomer,
                'revenue' => $revenueStats,
                'top_users' => $topUsers,
                'monthly_data' => $monthlyData,
            ],
        ]);
    }

    private function getRevenueStatistics($startDate = null, $endDate = null): array
    {
        $now = now();

        if (!$startDate && !$endDate) {
            $startDate = $now->copy()->subDays(29)->startOfDay(); // inclusive of today
            $endDate = $now->copy()->endOfDay();
        }

        // Ensure Carbon instances
        if ($startDate && !($startDate instanceof Carbon)) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }

        if ($endDate && !($endDate instanceof Carbon)) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        return [
            'total_revenue' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', '!=', OrderStatus::IN_CART)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->sum('order_payments.amount'),

            'paid_revenue' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', '!=', OrderStatus::IN_CART)
                ->whereIn('order_payments.payment_status', ['paid', 'completed'])
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->sum('order_payments.amount'),

            'pending_revenue' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', '!=', OrderStatus::IN_CART)
                ->where('order_payments.payment_status', 'pending')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->sum('order_payments.amount'),

            'completed_revenue' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', 'delivered')
                ->where('order_payments.payment_status', 'paid')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->sum('order_payments.amount'),

            'monthly_revenue' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', '!=', OrderStatus::IN_CART)
                ->whereMonth('orders.created_at', $now->month)
                ->whereYear('orders.created_at', $now->year)
                ->sum('order_payments.amount'),

            'daily_revenue' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', '!=', OrderStatus::IN_CART)
                ->whereDate('orders.created_at', $now->toDateString())
                ->sum('order_payments.amount'),

            'last_30_days_revenue' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', '!=', OrderStatus::IN_CART)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->sum('order_payments.amount'),

            'revenue_by_payment_status' => Order::join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->where('orders.status', '!=', OrderStatus::IN_CART)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select('order_payments.payment_status')
                ->selectRaw('SUM(order_payments.amount) as total')
                ->groupBy('order_payments.payment_status')
                ->get()
                ->pluck('total', 'payment_status'),
        ];
    }


    private function getTopUsers($startDate = null, $endDate = null): array
    {
        $now = now();

        // Default to last 30 days if no date range is provided
        if (!$startDate && !$endDate) {
            $startDate = $now->copy()->subDays(29)->startOfDay();
            $endDate = $now->copy()->endOfDay();
        }

        // Ensure both are Carbon instances
        if ($startDate && !($startDate instanceof Carbon)) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        if ($endDate && !($endDate instanceof Carbon)) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        return User::select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.image',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('COALESCE(SUM(order_payments.amount), 0) as total_spent'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            ])
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->leftJoin('order_payments', 'orders.id', '=', 'order_payments.order_id')
            ->where('users.type', UserType::CUSTOMER)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy([
                'users.id', 
                'users.first_name',
                'users.last_name',
                'users.email', 
                'users.phone', 
                'users.image'
            ])
            ->orderByDesc('total_orders')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'image' => $user->image,
                    'total_orders' => (int) $user->total_orders,
                    'total_spent' => (float) $user->total_spent,
                    'last_order_date' => $user->last_order_date ? Carbon::parse($user->last_order_date)->format('Y-m-d H:i:s') : null,
                    'formatted_total_spent' => number_format($user->total_spent, 2),
                    'last_order_human' => $user->last_order_date ? Carbon::parse($user->last_order_date)->diffForHumans() : null,
                ];
            })
            ->toArray();
    }


    private function getMonthlyData($startDate = null, $endDate = null): array
    {
        $currentYear = now()->year;
        $driver = DB::getDriverName();

        // Determine month extraction SQL based on driver
        $monthExpression = $driver === 'pgsql'
            ? 'EXTRACT(MONTH FROM created_at)::int'
            : 'MONTH(created_at)';

        // Customers
        $monthlyCustomers = User::where('type', UserType::CUSTOMER)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            }, function ($query) use ($currentYear) {
                return $query->whereYear('created_at', $currentYear);
            })
            ->selectRaw("$monthExpression as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Orders
        $monthlyOrders = Order::where('status', '!=', OrderStatus::IN_CART)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            }, function ($query) use ($currentYear) {
                return $query->whereYear('created_at', $currentYear);
            })
            ->selectRaw("$monthExpression as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months
        $customerData = [];
        $orderData = [];
        for ($month = 1; $month <= 12; $month++) {
            $customerData[] = $monthlyCustomers[$month] ?? 0;
            $orderData[] = $monthlyOrders[$month] ?? 0;
        }

        return [
            'customers' => $customerData,
            'orders' => $orderData,
            'year' => $currentYear,
        ];
    }

}
