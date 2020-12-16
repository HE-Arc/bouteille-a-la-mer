<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use function PHPUnit\Framework\isEmpty;

class LoginController extends Controller
{
    function login(Request $request) {
        return view("pages.login");
    }
    
    function signup(Request $request) {
        return view("pages.signup");
    }

    function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    function tryLogin(Request $request) {

        if ($request->session()->has('loginID')) {
            return ['error' => 'alreadyConnected', 'success' => true];
        }
        if (isset($request->username) && isset($request->password)) {
            if(Auth::attempt(["username" => $request->username, "password" => $request->password])) {
                //$request->session()->put('login_id', Auth::id()); //Mendatory for the websocket !
                return ['error' => '', 'success' => true];
            }
            return ['error' => 'wrongUsername'];
        }
        return ['error' => 'wrongRequest'];
    }

    function trySignup(Request $request) {
        if ($request->session()->has('loginID')) {
            return ['error' => 'alreadyConnected', 'success' => true];
        }
        $user = new User;
        if (isset($request->username) && isset($request->password)) {
            if (User::where('username', '=', $request->username)->count() == 0) {
                $user->username = $request->username;
                $user->password = Hash::make($request->password);
                $user->save();
                Auth::login($user);
                return ['error' => '', 'success' => true];
            }
            return ['error' => 'alreadyExist'];
        }
        return ['error' => 'wrongRequest'];        
    }
}
