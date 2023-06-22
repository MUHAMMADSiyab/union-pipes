<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Http\Requests\UserAccountEditRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    /**
     * Get all the users
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('user_access');

        $users = User::with('roles')->get();

        return response()->json($users);
    }

    /**
     * Get a single user
     * @param \App\Models\User $user
     * @return Illuminate\Http\Response
     */
    public function show(User $user)
    {
        Gate::authorize('user_access');
        Gate::authorize('user_show');

        return response()->json($user->with('roles')->whereId($user->id)->first());
    }

    /**
     * Assign users to user
     * @param App\Http\Requests\UserRequest $request
     * @return Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        Gate::authorize('user_access');
        Gate::authorize('user_create');

        $data = $request->only(['name', 'email', 'password']);

        DB::beginTransaction();

        try {
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);
            // Assign users to user
            $user->roles()->sync($request->roles);

            DB::commit();

            return response()->json($user->with('roles')->whereId($user->id)->first(), 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the user
     * @param App\Http\Requests\UserRequest $request
     * @param \App\Models\User $user
     * @return Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        Gate::authorize('user_access');
        Gate::authorize('user_edit');

        $data = $request->only(['name', 'email', 'password']);

        DB::beginTransaction();

        try {

            if (!empty($request->password)) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }

            $user = tap($user)->update($data);
            // Assign users to user
            $user->roles()->sync($request->roles);

            DB::commit();

            return response()->json($user->with('roles')->whereId($user->id)->first());
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete the user along with its users
     * @param \App\Models\User $user
     * @return Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        Gate::authorize('user_access');
        Gate::authorize('user_delete');

        if ($user->delete()) {
            return response()->json(["message" => "User deleted successfully"]);
        }
    }

    /**
     * Delete multitple users 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('user_access');
        Gate::authorize('user_delete');

        if (User::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Users deleted successfully"]);
        }
    }

    public function edit_user_account(UserAccountEditRequest $request)
    {
        $data = $request->all();

        $data['password'] = bcrypt($request->password);

        if (auth()->user()->update($data)) {
            return response()->json(['success' => 'Changes saved successfully']);
        }
    }
}
