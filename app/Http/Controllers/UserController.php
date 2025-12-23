<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\HasPermissionChecks;
use App\Models\Role;
use App\Enums\UserType;
use App\Services\IdGeneratorService;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use HasPermissionChecks;

    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function index(Request $request): Response
    {
        $this->authorizeAction('user.view');

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
        $query = User::with('roles')
            ->where('type', UserType::ADMIN)
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
            })->when($gender, function ($query, $gender) {
                if (is_array($gender) && !empty($gender)) {
                    $query->whereIn('gender', $gender);
                }
            });

        /** @var LengthAwarePaginator $items */
        $items = $query->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage);

        return Inertia::render("User/Index", [
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
        $this->authorizeAction('user.create');

        // Get all roles (or filter them manually)
        $roles = Role::all()->map(function ($role) {
            return [
                'value' => $role->id, // Use 'name' if you assign by name
                'label' => ucfirst(str_replace('-', ' ', $role->name)), // Make it more readable
            ];
        });
        return Inertia::render('User/Form', [
            'user_roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorizeAction('user.create');

        $validated = $request->validated();

        // Generate user ID with fresh prefix from database
        $userId = $this->idGenerator->generateId('admin', 'users', 'user_id', 10);


        $user = User::create([
            'user_id' => $userId,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'image' => $validated['image'],
            'telegram' => $validated['telegram'] ?? null,
            'gender' => $validated['gender'],
            'type' => UserType::ADMIN,
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['role_id'])) {
            $role = Role::findOrFail($validated['role_id']);
            $user->assignRole($role);
        }

        return redirect()->route('users.index')->with('success', __('user.messages.created_successfully'));
    }




   public function update(UpdateUserRequest $request, string $id)
    {
        $this->authorizeAction('user.edit');

        $user = User::findOrFail($id);
        $validated = $request->validated();

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->image = $validated['image'];
        $user->telegram = $validated['telegram'] ?? null;
        $user->gender = $validated['gender'];
        $user->type = UserType::ADMIN;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (isset($validated['role_id'])) {
            $role = Role::findOrFail($validated['role_id']);
            $user->syncRoles([$role->name]);
        }

        return redirect()->route('users.index')->with('success', __('user.messages.updated_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
            $this->authorizeAction('user.view');

        $user = User::findOrFail($id);
        return Inertia::render('User/Show', [
            'user' => $user
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
   public function edit(string $id)
    {
        $this->authorizeAction('user.edit');

        $user = User::with('roles')->findOrFail($id);

        $roles = Role::all()->map(function ($role) {
            return [
                'value' => $role->id,
                'label' => ucfirst(str_replace('-', ' ', $role->name)),
            ];
        });

        return Inertia::render("User/Form", [
            'user' => $user,
            'user_roles' => $roles,
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
  


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorizeAction('user.delete');

        $user = User::findOrFail($id);

          if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->withErrors([
                'error' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

}
