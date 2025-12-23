<?php

namespace App\Http\Controllers;

use App\Traits\HasPermissionChecks;
use App\Services\IdGeneratorService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Company;
use App\Models\ItemModification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Requests\StoreItemRequest;
use App\Models\ItemImage;
class ItemController extends Controller
{
    use HasPermissionChecks;

    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorizeAction('item.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "item_id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);
        $company_id = $request->input('company_id', null);

        $filters = [];
        if (!empty($company_id)) {
            $filters[] = [
                'id' => 'company_id',
                'value' => $company_id
            ];
        }

        $query = Item::with('company', 'images')
            ->when($search, function ($query, $search) {
                if (DB::getDriverName() === "pgsql") {
                    $query->where(function ($q) use ($search) {
                        $q->where("name_en", "ilike", "%{$search}%")
                        ->orWhere("name_km", "ilike", "%{$search}%")
                        ->orWhere("item_id", "ilike", "%{$search}%")
                        ->orWhere("description_en", "ilike", "%{$search}%")
                        ->orWhere("description_km", "ilike", "%{$search}%");
                    });
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where("name_en", "LIKE", "%{$search}%")
                        ->orWhere("name_km", "LIKE", "%{$search}%")
                        ->orWhere("item_id", "LIKE", "%{$search}%")
                        ->orWhere("description_en", "LIKE", "%{$search}%")
                        ->orWhere("description_km", "LIKE", "%{$search}%");
                    });
                }
            })
            ->when($company_id, function ($query, $company_id) {
                if (is_array($company_id) && !empty($company_id)) {
                    $query->whereIn('company_id', $company_id);
                }
            });

        // Ensure valid sort field and direction
        $validSortFields = ['id', 'created_at', 'item_id', 'name_en', 'name_km'];
        $sortField = in_array($sortField, $validSortFields) ? $sortField : 'item_id';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? $sortDirection : 'desc';

        $items = $query->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage);

        $companies = Company::all()->map(function ($company) {
            return [
                'value' => $company->id,
                'label' => $company->name_en . ' - ' . $company->name_km,
            ];
        });
        
        return Inertia::render("Item/Index", [
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
            "companies" => $companies,
        ]);
    }

    public function create(): Response
    {
       $companies = Company::all()->map(function ($role) {
           return [
                'value' => $role->id,
                'label' => $role->name_en . ' - ' . $role->name_km, // Concatenate with space-hyphen-space
            ];

        });

     
        
        return Inertia::render('Item/Form', [
            'companies' => $companies,
        ]);
    }


    public function store(StoreItemRequest $request)
    {
        // validated() gives you only validated data
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $itemId = $this->idGenerator->generateId('item', 'items', 'item_id', 10);

            $item = Item::create([
                'item_id' => $itemId,
                'name_en' => $data['name_en'],
                'name_km' => $data['name_km'],
                'description_en' => $data['description_en'] ?? null,
                'description_km' => $data['description_km'] ?? null,
                'company_id' => $data['company_id'] ?? null,
            ]);

            if (!empty($data['modifications'])) {
                foreach ($data['modifications'] as $modification) {
                    ItemModification::create([
                        'id' => Str::uuid(),
                        'item_id' => $item->id,
                        'modification_name' => $modification['modification_name'],
                        'unit' => $modification['unit'],
                        'unit_price' => $modification['unit_price'],
                    ]);
                }
            }
            
            if (!empty($data['images'])) {
                $mainCount = collect($data['images'])->where('is_main', true)->count();

                if ($mainCount === 0) {
                    $data['images'][0]['is_main'] = 1;
                }

                if ($mainCount > 1) {
                    $mainFound = false;
                    foreach ($data['images'] as &$img) {
                        if ($img['is_main']) {
                            if (!$mainFound) {
                                $mainFound = true; // keep first main
                            } else {
                                $img['is_main'] = 0; // reset others
                            }
                        }
                    }
                    unset($img); 
                }

                // Save images
                foreach ($data['images'] as $imageData) {
                    $item->images()->create([
                        'id' => Str::uuid(),
                        'image' => $imageData['image'],
                        'is_main' => $imageData['is_main'] ?? 0,
                    ]);
                }
            }


            DB::commit();
            return redirect()->route('items.index')->with('success', 'Item created successfully.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Item creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => 'Failed to create item. Please try again.'])
                ->withInput();
        }
    }

    public function show(Item $item): Response
    {
        $item->load(['company', 'modifications']);

        return Inertia::render('Item/Show', [
            'item' => $item,
        ]);
    }

    public function edit(Item $item): Response
    {

        $item->load(['company', 'modifications', 'images']);
       $companies = Company::all()->map(function ($role) {
           return [
                'value' => $role->id,
                'label' => $role->name_en . ' - ' . $role->name_km, // Concatenate with space-hyphen-space
            ];

        });
        return Inertia::render('Item/Form', [
            'item' => $item,
            'companies' => $companies,
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Update item fields
            $item->update([
                'name_en' => $data['name_en'],
                'name_km' => $data['name_km'],
                'description_en' => $data['description_en'] ?? null,
                'description_km' => $data['description_km'] ?? null,
                'company_id' => $data['company_id'],
            ]);

            // === Handle modifications ===
            if (!empty($data['modifications'])) {
                $existingIds = collect($data['modifications'])
                    ->pluck('id')
                    ->filter()
                    ->toArray();

                // Delete removed modifications
                $item->modifications()->whereNotIn('id', $existingIds)->delete();

                // Update or insert
                foreach ($data['modifications'] as $modification) {
                    if (!empty($modification['id'])) {
                        $item->modifications()->where('id', $modification['id'])->update([
                            'modification_name' => $modification['modification_name'],
                            'unit' => $modification['unit'],
                            'unit_price' => $modification['unit_price'],
                        ]);
                    } else {
                        $item->modifications()->create([
                            'id' => Str::uuid(),
                            'modification_name' => $modification['modification_name'],
                            'unit' => $modification['unit'],
                            'unit_price' => $modification['unit_price'],
                        ]);
                    }
                }
            } else {
                $item->modifications()->delete();
            }

            if (!empty($data['images'])) {
                // Delete old images
                $item->images()->delete();

                // Insert new images with is_main
                foreach ($data['images'] as $imageData) {
                    $item->images()->create([
                        'id' => Str::uuid(),
                        'image' => $imageData['image'],
                        'is_main' => $imageData['is_main'] ?? 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('items.index')->with('success', 'Item updated successfully.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Item update failed', [
                'error' => $e->getMessage(),
                'item_id' => $item->id,
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => 'Failed to update item. Please try again.'])
                        ->withInput();
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Item $item)
    {
        // dd($item);
        try {
            DB::beginTransaction();

            // Delete all modifications first
            $modificationsDeleted = $item->modifications()->delete();
            Log::info('Modifications deleted', ['count' => $modificationsDeleted]);

            // Soft delete the item
            $itemDeleted = $item->delete();
            Log::info('Item deleted', ['success' => $itemDeleted]);

            DB::commit();


            return redirect()->route('items.index')
                ->with('success', 'Item deleted successfully.');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Item deletion failed', [
                'error' => $e->getMessage(),
                'item_id' => $item->id
            ]);

           

            return back()->withErrors(['error' => 'Failed to delete item. Please try again.']);
        }
    }


}
