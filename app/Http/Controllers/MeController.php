<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class MeController extends Controller
{
    public function index()
    {   
        return view('me');
    }

    public function update(Request $request)
    {   
        $this->validate($request, [
	        'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.\Auth::user()->id,
            'username' => 'required|alpha_num|max:255|unique:users,username,'.\Auth::user()->id,
            'password' => 'required_with:password_confirmation|same:password_confirmation|min:6',   
			'password_confirmation' => 'required_with:password|min:6', 
	    ]);

        if ($request->password == '' || $request->password == null) {
        	$user = User::find(\Auth::user()->id);
        	$user->name = $request->name;
        	$user->email = $request->email;
        	$user->username = $request->username;
        	$user->save();
        }else{
        	$user = User::find(\Auth::user()->id);
        	$user->name = $request->name;
        	$user->email = $request->email;
        	$user->username = $request->username;
        	$user->password = bcrypt($request->password);
        	$user->save();
        }
        return redirect('me')->with('status', 'Profile updated!');;
    }
}
