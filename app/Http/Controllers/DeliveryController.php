<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\IdGeneratorService;
use App\Traits\HasPermissionChecks;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Promotion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Company;

class DeliveryController extends Controller
{


     use HasPermissionChecks;

    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function index(Request $request): Response
    {
        $this->authorizeAction('delivery.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "delivery_id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);
        $is_active = $request->input('is_active', null);

        $filters = [];

         if (!empty($is_active)) {
            $filters[] = [
                'id' => 'is_active',
                'value' => $is_active
            ];
        }

        $query = Delivery::when($search, function ($query, $search) {
            if (DB::getDriverName() === "pgsql") {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "ilike", "%{$search}%")
                    ->orWhere("shipping_fee", "ilike", "%{$search}%")
                    ->orWhere("description", "ilike", "%{$search}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "LIKE", "%{$search}%")
                    ->orWhere("shipping_fee", "LIKE", "%{$search}%")
                    ->orWhere("description", "LIKE", "%{$search}%");
                });
            }
        })
        ->when($is_active, function ($query, $is_active) {
            if (is_array($is_active) && !empty($is_active)) {
                $query->whereIn('is_active', $is_active);
            } else {
                $query->where('is_active', $is_active);
            }
        });

        $items = $query->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage);

        return Inertia::render("Delivery/Index", [
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
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAction('delivery.create');
        return Inertia::render('Delivery/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreDeliveryRequest $request)
    {
        $this->authorizeAction('delivery.create');
        $validated = $request->validated();

        $deliveryId = $this->idGenerator->generateId('delivery', 'deliveries', 'delivery_id', 10);

        $delivery = Delivery::create([
            'delivery_id' => $deliveryId,
            'name' => $validated['name'],
            'image' => $validated['image'] ?? null, // you might want to process image upload here
            'shipping_fee' => $validated['shipping_fee'],
            'is_active' => $validated['is_active'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('deliveries.index')
            ->with('success', __('delivery.messages.created_successfully'));
    }



    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        $this->authorizeAction('delivery.view');

        $delivery->load('orders');

        $ordersCount = $delivery->orders()->count();
        $ordersSum = $delivery->orders()->sum('delivery_fee'); // adjust column name accordingly

        return Inertia::render('Delivery/Show', [
            'delivery' => $delivery,
            'ordersCount' => $ordersCount,
            'ordersSum' => $ordersSum,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        $this->authorizeAction('delivery.edit');

        return Inertia::render('Delivery/Form', [
            'delivery' => $delivery,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryRequest $request, Delivery $delivery)
    {
        $this->authorizeAction('delivery.edit');

        $validated = $request->validated();

        $delivery->update([
            'delivery_id' => $validated['delivery_id'] ?? $delivery->delivery_id,
            'name' => $validated['name'],
            'image' => $validated['image'] ?? $delivery->image,
            'shipping_fee' => $validated['shipping_fee'],
            'is_active' => $validated['is_active'],
            'description' => $validated['description'] ?? $delivery->description,
        ]);

        return redirect()->route('deliveries.index')
            ->with('success', __('delivery.messages.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $this->authorizeAction('delivery.delete');

        try {
            $delivery->delete();

            return redirect()
                ->route('deliveries.index') // or your desired route
                ->with('success', trans('delivery.actions.delete_success'));
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', trans('delivery.actions.delete_failed'));
        }
    }


}
