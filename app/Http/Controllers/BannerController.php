<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Traits\HasPermissionChecks;
use App\Services\IdGeneratorService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
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
        $this->authorizeAction('banner.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "banner_id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);

        $query = Banner::when($search, function ($query, $search) {
                if (DB::getDriverName() === "pgsql") {
                    $query->where(function ($q) use ($search) {
                        $q->where("name", "ilike", "%{$search}%")
                        ->orWhere("banner_id", "ilike", "%{$search}%")
                        ->orWhere("description", "ilike", "%{$search}%");
                    });
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where("name", "LIKE", "%{$search}%")
                        ->orWhere("banner_id", "LIKE", "%{$search}%")
                        ->orWhere("description", "LIKE", "%{$search}%");
                    });
                }
            });

        $items = $query->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage);

        return Inertia::render("Banner/Index", [
            "data" => [
                "data" => $items->items(),
                "current_page" => $items->currentPage(),
                "per_page" => $items->perPage(),
                "last_page" => $items->lastPage(),
                "total" => $items->total(),
                "search" => $search,
                "sort_field" => $sortField,
                "sort_direction" => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAction('banner.create');
        return Inertia::render('Banner/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBannerRequest $request)
    {
        $this->authorizeAction('banner.create');

        try {
            $validated = $request->validated();
            $bannerId = $this->idGenerator->generateId('banner', 'banners', 'banner_id', 10);

            $banner = Banner::create([
                'banner_id' => $bannerId,
                'name' => $validated['name'],
                'banner_image' => $validated['banner_image'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()
                ->route('banners.index')
                ->with('success', __('banner.messages.created_successfully'));
        } catch (\Exception $e) {
            Log::error('Error creating banner', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => __('banner.messages.creation_failed') . ' ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        $this->authorizeAction('banner.view');
        return Inertia::render('Banner/Show', [
            'banner' => $banner
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        $this->authorizeAction('banner.edit');
        return Inertia::render('Banner/Form', [
            'banner' => $banner
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $this->authorizeAction('banner.edit');

        try {
            $validated = $request->validated();

            $banner->update([
                'name' => $validated['name'],
                'banner_image' => $validated['banner_image'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()
                ->route('banners.index')
                ->with('success', __('banner.messages.updated_successfully'));
        } catch (\Exception $e) {
            Log::error('Error updating banner', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
                'banner_id' => $banner->id,
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => __('banner.messages.update_failed') . ' ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $this->authorizeAction('banner.delete');
        
        try {
            $banner->delete();
            
            return redirect()->route('banners.index')
                ->with('success', __('messages.banner_deleted_successfully'));
        }
        catch (\Exception $e) {
            Log::error('Error deleting banner', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'banner_id' => $banner->id
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete banner: ' . $e->getMessage()]);
        }
    }
}
