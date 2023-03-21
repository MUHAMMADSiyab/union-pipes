<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    /**
     * Get all the roles
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('role_access');

        $roles = Role::with('permissions')->get();

        return response()->json($roles);
    }

    /**
     * Get a single role
     * @param \App\Models\Role $role
     * @return Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        Gate::authorize('role_access');
        Gate::authorize('role_show');

        return response()->json($role->with('permissions')->whereId($role->id)->first());
    }

    /**
     * Assign permissions to role
     * @param App\Http\Requests\RoleRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        Gate::authorize('role_access');
        Gate::authorize('role_create');

        DB::beginTransaction();

        try {
            $role = Role::create($request->only(['name']));
            // Assign permissions to role
            $role->permissions()->sync($request->permissions);

            DB::commit();

            return response()->json($role->with('permissions')->whereId($role->id)->first(), 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the role
     * @param App\Http\Requests\RoleRequest $request
     * @param \App\Models\Role $role
     * @return Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        Gate::authorize('role_access');
        Gate::authorize('role_edit');

        DB::beginTransaction();

        try {
            $role = tap($role)->update($request->all());
            // Assign permissions to role
            $role->permissions()->sync($request->permissions);

            DB::commit();

            return response()->json($role->with('permissions')->whereId($role->id)->first());
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete the role along with its permissions
     * @param \App\Models\Role $role
     * @return Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        Gate::authorize('role_access');
        Gate::authorize('role_delete');

        if ($role->delete()) {
            return response()->json(["message" => "Role deleted successfully"]);
        }
    }

    /**
     * Delete multitple roles 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('role_access');
        Gate::authorize('role_delete');

        if (Role::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Roles deleted successfully"]);
        }
    }
}
