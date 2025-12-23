<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use App\Models\ItemModification;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Inertia\Inertia;
use Carbon\Carbon;

class ItemReportController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "item_id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);
        $currentPage = (int) $request->input("page", 1);

        // Handle date filtering
        $startDate = $request->input("start_date");
        $endDate = $request->input("end_date");

        // If no date range is provided, default to last 30 days
        if (!$startDate || !$endDate) {
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays(30)->startOfDay();
        } else {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id') // Add this join
            ->join('item_modifications', 'order_items.item_modification_id', '=', 'item_modifications.id')
            ->join('items', 'item_modifications.item_id', '=', 'items.id')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', OrderStatus::IN_CART) // Now this will work because of join
            ->select(
                'item_modifications.id as modification_id',
                'item_modifications.modification_name',
                'item_modifications.unit',
                'item_modifications.unit_price',
                'items.name_en as item_name_en',
                'items.name_km as item_name_km',
                'items.id as id',
                'items.item_id as item_id',
            )
            ->selectRaw('COUNT(DISTINCT order_items.order_id) as order_count')
            ->selectRaw('SUM(order_items.quantity) as total_quantity_ordered')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->groupBy(
                'item_modifications.id',
                'item_modifications.modification_name',
                'item_modifications.unit',
                'item_modifications.unit_price',
                'items.name_en',
                'items.name_km',
                'items.id',
                'items.item_id'
            );


        // Search logic
        if (!empty($search)) {
            $operator = DB::getDriverName() === "pgsql" ? "ilike" : "like";
            $query->having(function ($q) use ($search, $operator) {
                $q->where('item_modifications.modification_name', $operator, "%{$search}%")
                  ->orWhere('items.name_en', $operator, "%{$search}%")
                  ->orWhere('items.name_km', $operator, "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy($sortField, $sortDirection);

        // Manual pagination
        $results = $query->get();
        $total = $results->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = $results->slice($offset, $perPage)->values();

        return Inertia::render('Report/Index', [
            "data" => [
                "data" => $paginatedItems,
                "current_page" => $currentPage,
                "per_page" => $perPage,
                "last_page" => (int) ceil($total / $perPage),
                "total" => $total,
                "search" => $search,
                "sort_field" => $sortField,
                "sort_direction" => $sortDirection,
                "start_date" => $startDate->toISOString(),
                "end_date" => $endDate->toISOString(),
            ],
        ]);
    }
}
