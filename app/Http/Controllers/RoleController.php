<?php

namespace App\Http\Controllers;

use App\Role;
use App\Permission;
use App\PartNumber;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        if (\Gate::denies('roleView', PartNumber::class)) {
            abort(404);
        }

        $roles = Role::paginate();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        if (\Gate::denies('roleView', PartNumber::class)) {
            abort(404);
        }
        return view('roles.create');
    }

    public function store(Request $request)
    {
        if (\Gate::denies('roleView', PartNumber::class)) {
            abort(404);
        }

        $this->validate($request, [
            'name' => 'required|alphanum|unique:roles',
            'label' => 'required',
        ]);

        Role::create($request->only('name', 'label'));

        return back()->withStatus('Success');
    }

    public function edit(Role $role)
    {
        if (\Gate::denies('roleView', PartNumber::class)) {
            abort(404);
        }

        return view('roles.edit', compact('role'));
    }

    public function update(Role $role, Request $request)
    {
        if (\Gate::denies('roleView', PartNumber::class)) {
            abort(404);
        }

        $this->validate($request, [
            'name' => 'required|alphanum|unique:roles',
            'label' => 'required',
        ]);

        $role->update($request->only('name', 'label'));

        return back()->withStatus('Success');
    }
}
