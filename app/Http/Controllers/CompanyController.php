<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Services\IdGeneratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\HasPermissionChecks;    
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
class CompanyController extends Controller
{
     use HasPermissionChecks;

    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }


    public function index(Request $request): Response
    {
        $this->authorizeAction('company.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "company_id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);

        $query = Company::when($search, function ($query, $search) {
            $operator = DB::getDriverName() === "pgsql" ? "ilike" : "like";
            $query->where(function ($q) use ($search, $operator) {
                $q->where("name", $operator, "%{$search}%")
                  ->orWhere("company_id", $operator, "%{$search}%")
                  ->orWhere("description", $operator, "%{$search}%");
            });
        });

        $items = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

        return Inertia::render("Company/Index", [
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

    public function create(): Response
    {
        $this->authorizeAction('company.create');
        return Inertia::render('Company/Form');
    }

    public function store(StoreCompanyRequest $request)
    {
        $this->authorizeAction('company.create');

        try {
            $validated = $request->validated();
            $id = $this->idGenerator->generateId('company', 'companies', 'company_id', 10);
            $logoValue = !empty($validated['logo']) ? $validated['logo'] : '';

            $company = Company::create([
                'company_id' => $id,
                'name_en' => $validated['name_en'],
                'name_km' => $validated['name_km'],
                'description_en' => $validated['description_en'] ?? null,
                'description_km' => $validated['description_km'] ?? null,
                'logo' => $logoValue,
                'delete_at' => null,
            ]);

            return redirect()->route('companies.index')->with('success', 'Company created successfully.');

        } catch (QueryException $e) {
            // Log database-specific errors
            Log::error('QueryException while creating company', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Database error occurred while creating the company.');

        } catch (\Exception $e) {
            // Log general errors
            Log::error('Exception while creating company', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Unexpected error occurred while creating the company.');
        }
    }




    public function edit(Company $company): Response
    {
        $this->authorizeAction('company.edit');
        return Inertia::render('Company/Form', [
            'company' => $company,
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $this->authorizeAction('company.edit');
        $validated = $request->validated();

        $company->update($validated);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $this->authorizeAction('company.delete');

        if ($company->items()->exists()) {
            // Use translation helper here, assuming your lang file has company/messages.php with key 'delete_with_items'
            $errorMessage = __('company.messages.delete_with_items');

            if (request()->wantsJson()) {
                return response()->json(['message' => $errorMessage], 422);
            }

            return redirect()->route('companies.index')->withErrors(['error' => $errorMessage]);
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', __('company.messages.delete_success'));
    }



}
