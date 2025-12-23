<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\Permission;
use App\Traits\HasPermissionChecks;
use Illuminate\Support\Facades\DB;
use App\Http\Responses\PaginatedResponse;
use App\Http\Requests\RoleRequest;
class RoleController extends Controller
{
    use HasPermissionChecks;
    
    public function index(Request $request): \Inertia\Response
    {
        $this->authorizeAction('role.view');

        $search = $request->input("search", "");
        $sortField = $request->input("sort_field", "id");
        $sortDirection = $request->input("sort_direction", "desc");
        $perPage = (int) $request->input("per_page", 10);

        $query = Role::query()
            ->when($search, function ($query, $search) {
                if (DB::getDriverName() === "pgsql") {
                    $query->where("name", "ilike", "%{$search}%")
                        ->orWhere("guard_name", "ilike", "%{$search}%");
                } else {
                    $query->where("name", "LIKE", "%{$search}%")
                        ->orWhere("guard_name", "LIKE", "%{$search}%");
                }
            });

        $items = $query->orderBy($sortField, $sortDirection)
            ->paginate(perPage: $perPage);


        return Inertia::render("Role/Index", [
            "data" => new PaginatedResponse($items)
        ]);
    }

    public function create()
    {
        $this->authorizeAction('role.create');
        
        return Inertia::render('Role/Form', [
            'permissions' => Permission::select('id', 'name') // Only select needed fields
                ->orderBy('name', 'asc')
                ->get(),
            'rolePermissions' => [],
        ]);
    }


    public function store(RoleRequest $request)
    {
        $this->authorizeAction('role.create');

        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', __('messages.role_created_successfully'));
    }


   public function edit(Role $role)
    {
        $this->authorizeAction('role.edit');
        
        $role->load('permissions');
        
        return Inertia::render('Role/Form', [
            'role' => $role,
            'permissions' => Permission::select('id', 'name')->get(),
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
        ]);
    }
    public function show(Role $role)
    {
        $this->authorizeAction('role.edit');
        
        $role->load('permissions');
        
        return Inertia::render('Role/Show', [
            'role' => $role,
            'permissions' => Permission::all(), // Flat array, not grouped
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->authorizeAction('role.edit');

        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', __('messages.role_updated_successfully'));
    }


    public function destroy(Role $role)
    {
        $this->authorizeAction('role.delete');

        if ($role->name === 'Super Admin') {
            return back()->withErrors(['error' => 'Cannot delete Super role.']);
        }

        $user = auth()->user();

        if ($user && $user->roles->contains('id', $role->id)) {
            return back()->withErrors(['error' => 'You cannot delete a role assigned to your own account.']);
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

}
