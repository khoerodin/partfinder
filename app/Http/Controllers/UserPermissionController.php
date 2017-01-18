<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Permission;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    public function index(User $user)
    {
        if (\Gate::denies('userView', PartNumber::class)) {
            abort(404);
        }

        $permissions = Permission::all();
        return view('users.permissions.index', compact('permissions', 'user'));
    }

    public function update(Request $request, User $user)
    {
        if (\Gate::denies('userView', PartNumber::class)) {
            abort(404);
        }
        
        $user->permissions()->sync($request->permission ?? []);
        return back()->withStatus('Success');
    }
}
