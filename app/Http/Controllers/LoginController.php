<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use function PHPUnit\Framework\isEmpty;

class LoginController extends Controller
{
    function login() {
        //TODO If not connected
        return view("login");
    }
    function signup() {
        //TODO if not connected
        return view("signup");
    }

    function tryLogin(Request $request) {
        if (isset($request->username) && isset($request->password)) {
            $user = User
            ::where('username', '=', $request->username)
            ->first();
            if ($user != null) {
                if (Hash::check($request->password, $user->password)) {
                    return ['error' => '', 'success' => true];
                }
                return ['error' => 'wrongPassword'];
            }
            return ['error' => 'wrongUsername'];
        }
        return ['error' => 'wrongRequest'];
    }

    function trySignup(Request $request) {
        $user = new User;
        if (isset($request->username) && isset($request->password)) {
            if (User::where('username', '=', $request->username)->count() == 0) {
                $user->username = $request->username;
                $user->password = bcrypt($request->password);
                $user->save();
                return ['error' => '', 'success' => true];
            }
            return ['error' => 'alreadyExist'];
        }
        return ['error' => 'wrongRequest'];        
    }
}
