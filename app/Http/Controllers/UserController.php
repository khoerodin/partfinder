<?php

namespace App\Http\Controllers;

use App\User;
use App\PartNumber;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        if (\Gate::denies('userView', PartNumber::class)) {
            abort(404);
        }

        $users = User::paginate();
        return view('users.index', compact('users'));
    }
}
