<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\IdGeneratorService;
use App\Traits\HasPermissionChecks;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;

class PromotionController extends Controller
{
    use HasPermissionChecks;

    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }
    public function index(Request $request): Response
    {
        $this->authorizeAction('promotion.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "promotion_id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);
        $status = $request->input('status', null);

        $filters = [];

         if (!empty($status)) {
            $filters[] = [
                'id' => 'status',
                'value' => $status
            ];
        }
        $query = Promotion::with('items')->when($search, function ($query, $search) {
                if (DB::getDriverName() === "pgsql") {
                    $query->where(function ($q) use ($search) {
                        $q->where("name", "ilike", "%{$search}%")
                        ->orWhere("promotion_id", "ilike", "%{$search}%")
                         ->orWhere("type", "ilike", "%{$search}%")
                          ->orWhere("end_date", "ilike", "%{$search}%")
                           ->orWhere("start_date", "ilike", "%{$search}%")
                        ->orWhere("description", "ilike", "%{$search}%");
                    });
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where("name", "LIKE", "%{$search}%")
                        ->orWhere("promotion_id", "LIKE", "%{$search}%")
                        ->orWhere("type", "LIKE", "%{$search}%")
                        ->orWhere("end_date", "LIKE", "%{$search}%")
                        ->orWhere("start_date", "LIKE", "%{$search}%")
                        ->orWhere("description", "LIKE", "%{$search}%");
                    });
                }
            })->when($status, function ($query, $status) {
            if (is_array($status) && !empty($status)) {
                $query->whereIn('status', $status);                
            }
        });

        $items = $query->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage);

        return Inertia::render("Promotion/Index", [
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

    public function show(Promotion $promotion)
    {
        $this->authorizeAction('promotion.view');

        $promotion->load('items');

        return Inertia::render('Promotions/Show', [
            'promotion' => $promotion,
        ]);
    }

    public function create()
    {
        $this->authorizeAction('promotion.create');

        $items = Item::with(['firstImage:id,item_id,image'])
            ->select('id', 'item_id', 'name_en')
            ->orderBy('item_id', 'desc')
            ->get();

        return Inertia::render('Promotion/Form', [
            'items' => $items,
        ]);
    }


    public function store(StorePromotionRequest $request)
    {
        $this->authorizeAction('promotion.create');
        $data = $request->validated();
        
        // Debug: Check what data you're receiving
        Log::info('Received data', $data);
        
        $customId = $this->idGenerator->generateId('promotion', 'promotions', 'promotion_id', 10);

        // Create promotion with all fields including dates
        $promotion = Promotion::create([
            'promotion_id' => $customId,
            'banner' => $data['banner'],
            'name_en' => $data['name_en'],
            'name_km' => $data['name_km'],
            'description_en' => $data['description_en'] ?? null,
            'description_km' => $data['description_km'] ?? null,
            'type' => $data['type'],
            'discount_value' => $data['discount_value'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],

        ]);

        // Debug: Check the created promotion
        Log::info('Created promotion', ['id' => $promotion->id]);
        Log::info('Custom promotion_id', ['promotion_id' => $promotion->promotion_id]);

        // Use 'items' consistently (matching the frontend and validation)
        $itemIds = $data['items'] ?? [];
        
        // Debug: Check what item IDs you're trying to sync
        Log::info('Item IDs to sync', ['item_ids' => $itemIds]);

        if (!empty($itemIds)) {
            // Make sure $itemIds is an array
            if (!is_array($itemIds)) {
                $itemIds = [$itemIds];
            }
            
            // Debug: Verify items exist
            $existingItems = Item::whereIn('id', $itemIds)->get();
            Log::info('Found items', ['found_item_ids' => $existingItems->pluck('id')->toArray()]);
            
            // Sync the relationship
            $result = $promotion->items()->sync($itemIds);
            
            // Debug: Check sync result
            Log::info('Sync result', $result);
            
            // Debug: Check if relationships were created
            $promotion->refresh();
            Log::info('Promotion items after sync', ['synced_item_ids' => $promotion->items->pluck('id')->toArray()]);
        }

        return redirect()
            ->route('promotions.index')
            ->with('success', __('Promotion created successfully.'));
    }



    public function edit(Promotion $promotion)
    {
        $this->authorizeAction('promotion.edit');
        $promotion->load('items');

       $items = Item::with(['firstImage:id,item_id,image'])
            ->select('id', 'item_id', 'name_en')
            ->orderBy('item_id', 'desc')
            ->get();

        
        return Inertia::render('Promotion/Form', [
            'promotion' => [
                'id' => $promotion->id,
                'name_en' => $promotion->name_en,
                'name_km' => $promotion->name_km,
                'banner' => $promotion->banner,
                'description_en' => $promotion->description_en,
                'description_km' => $promotion->description_km,
                'type' => $promotion->type,
                'discount_value' => $promotion->discount_value,
                'start_date' => $promotion->start_date ? $promotion->start_date->format('Y-m-d') : '',
                'end_date' => $promotion->end_date ? $promotion->end_date->format('Y-m-d') : '',
               
                'items' => $promotion->items->pluck('id')->toArray(),
            ],
            'items' => $items,
        ]);
    }



   public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $this->authorizeAction('promotion.edit');
        try {
            $data = $request->validated();

            Log::info('Updating promotion', ['promotion_id' => $promotion->id, 'data' => $data]);

            $promotion->update([
                'name_en' => $data['name_en'],
                'name_km' => $data['name_km'],
                'description_en' => $data['description_en'] ?? null,
                'description_km' => $data['description_km'] ?? null,
                'banner' => $data['banner'],
                'type' => $data['type'],
                'discount_value' => $data['discount_value'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                
            ]);

            // Sync items
            if (isset($data['items'])) {
                Log::info('Syncing items', ['item_ids' => $data['items']]);
                $result = $promotion->items()->sync($data['items']);
                Log::info('Update sync result', $result);
            } else {
                Log::info('No items provided, detaching all items');
                $promotion->items()->sync([]);
            }

            return redirect()->route('promotions.index')
                ->with('success', 'Promotion updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to update promotion', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'promotion_id' => $promotion->id,
            ]);

            return back()->withErrors(['error' => 'Failed to update promotion. Please try again later.']);
        }
    }

    public function destroy(Promotion $promotion)
    {
        $this->authorizeAction('promotion.delete');
        $promotion->delete();

        return redirect()->route('promotions.index')
            ->with('success', 'Promotion deleted successfully.');
    }
}
