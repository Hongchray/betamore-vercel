<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\HasPermissionChecks;
use App\Models\Role;
use App\Enums\UserType;
use App\Services\IdGeneratorService;

class CustomerController extends Controller
{
    use HasPermissionChecks;

    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }


    public function index(Request $request): Response
    {
        $this->authorizeAction('customer.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "user_id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);
        $gender = $request->input('gender', null);

        $filters = [];
        if (!empty($gender)) {
            $filters[] = [
                'id' => 'gender',
                'value' => $gender
            ];
        }

        $query = User::withTrashed() 
        ->with('roles')
            ->where('type', UserType::CUSTOMER)
            ->when($search, function ($query, $search) {
                if (DB::getDriverName() === "pgsql") {
                    $query->where(function ($q) use ($search) {
                        $q->where("first_name", "ilike", "%{$search}%")
                        ->orWhere("last_name", "ilike", "%{$search}%")
                        ->orWhere("email", "ilike", "%{$search}%")
                        ->orWhere("phone", "ilike", "%{$search}%");
                    });
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where("first_name", "LIKE", "%{$search}%")
                        ->orWhere("last_name", "LIKE", "%{$search}%")
                        ->orWhere("email", "LIKE", "%{$search}%")
                        ->orWhere("phone", "LIKE", "%{$search}%");
                    });
                }
            })
             ->when($gender, function ($query, $gender) {
                if (is_array($gender) && !empty($gender)) {
                    $query->whereIn('gender', $gender);
                }
            });

        $items = $query->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage);

        return Inertia::render("Customer/Index", [ // Changed from "User/Index"
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

    public function create()
    {
        $this->authorizeAction('customer.create');

        $roles = Role::all()->map(function ($role) {
            return [
                'value' => $role->id,
                'label' => ucfirst(str_replace('-', ' ', $role->name)),
            ];
        });
        
        return Inertia::render('Customer/Form', [ // Changed from 'User/Form'
            'user_roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorizeAction('customer.create');

        $validated = $request->validated();
        $userId = $this->idGenerator->generateId('customer', 'users', 'user_id', 10);

        $user = User::create([
            'user_id' => $userId,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'image' => $validated['image'] ?? null,
            'telegram' => $validated['telegram'] ?? null,
            'gender' => $validated['gender'],
            'type' => UserType::CUSTOMER,
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['role_id'])) {
            $role = Role::findOrFail($validated['role_id']);
            $user->assignRole($role);
        }

        return redirect()->route('customers.index')->with('success', __('user.messages.created_successfully'));
    }
    public function edit(string $customer)
    {
        $this->authorizeAction('customer.edit');

        // Make sure we only get customers, not admin users
        $user = User::with('roles')
            ->where('type', UserType::CUSTOMER)
            ->where(function($query) use ($customer) {
                $query->where('id', $customer)
                    ->orWhere('user_id', $customer);
            })
            ->firstOrFail();

        $roles = Role::all()->map(function ($role) {
            return [
                'value' => $role->id,
                'label' => ucfirst(str_replace('-', ' ', $role->name)),
            ];
        });

        return Inertia::render("Customer/Form", [
            'user' => $user,
            'user_roles' => $roles,
        ]);
    }


    public function update(UpdateUserRequest $request, string $customer) // Changed parameter name
    {
        $this->authorizeAction('customer.edit');

        $user = User::where('type', UserType::CUSTOMER) // Ensure we only update customers
            ->findOrFail($customer);
        
        $validated = $request->validated();

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->image = $validated['image'] ?? null;
        $user->telegram = $validated['telegram'] ?? null;
        $user->gender = $validated['gender'];
        $user->type = $validated['type'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Handle role assignment for customers (nullable)
        if (!empty($validated['role_id'])) {
            $role = Role::findOrFail($validated['role_id']);
            $user->syncRoles([$role->name]);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('customers.index')->with('success', __('user.messages.updated_successfully'));
    }

    public function show(string $customer)
    {
        $this->authorizeAction('customer.view');

        $user = User::with([
                'addresses', 
                'orders' => function($query) {
                    $query->withCount('items'); // Count items for each order
                }
            ])
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->where('type', UserType::CUSTOMER)
            ->findOrFail($customer);
            
        return Inertia::render('Customer/Show', [
            'user' => $user,
            'order_count' => $user->orders_count,
            'total_paid' => $user->orders_sum_total_amount ?? 0
        ]);
    }


    


    public function destroy(string $customer)
{
    $this->authorizeAction('customer.delete');

    $user = User::where('type', UserType::CUSTOMER)
        ->withTrashed() // important to also find soft-deleted users
        ->findOrFail($customer);

    // Prevent deleting own account
    if (auth()->id() === $user->id) {
        return redirect()->route('customers.index')->withErrors([
            'error' => 'You cannot delete your own account.',
        ]);
    }

    // If user already soft deleted → permanently delete
    if ($user->trashed()) {
        $user->forceDelete();

        return redirect()->route('customers.index')->with('success', 'Customer permanently deleted.');
    }

    // Otherwise → soft delete
    $user->delete();

    return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
}


}
