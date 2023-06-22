<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    /**
     * Get all permissions
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('permission_access');

        $permissions = Permission::all();
        return response()->json($permissions);
    }

    /**
     * Add a new permission
     *
     * @param  \App\Http\Requests\PermissionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        Gate::authorize('permission_access');
        Gate::authorize('permission_create');

        $permission = Permission::create($request->all());

        return response()->json($permission, 201);
    }

    /**
     * Get a single permission
     * @param App\Permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        Gate::authorize('permission_access');
        Gate::authorize('permission_show');

        return response()->json($permission, 201);
    }


    /**
     * Update a permission
     *
     * @param  \App\Http\Requests\PermissionRequest  $request
     * @param  App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        Gate::authorize('permission_access');
        Gate::authorize('permission_edit');

        $updatedPermission = tap($permission)->update($request->all());
        return response()->json($updatedPermission);
    }

    /**
     * Delete a permission
     *
     * @param  App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        Gate::authorize('permission_access');
        Gate::authorize('permission_delete');

        if ($permission->delete()) {
            return response()->json(["success" =>  "Permission deleted successfully"]);
        }
    }

    /**
     * Delete multiple permissions.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('permission_access');
        Gate::authorize('permission_delete');

        if (Permission::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Permissions deleted successfully"]);
        }
    }
}
