<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    function login() {
        return view("testLogin");
    }

    function tryLogin(Request $request) {
        if (isset($request->username) && isset($request->password)) {

            $user = User
            ::where('username', '=', $request->username)
            ->first();
            var_dump($user);
        }
        return "yo";

    }

    function trySignup(Request $request) {
        $user = new User;
        $user->username = $request->name;
        $user->password = $request->password;
        $user->save();
    }
}
