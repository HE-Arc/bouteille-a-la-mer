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
        //TODO If not connected
        /*if ($request->session()->has('loginID')) {
            return redirect('/');
        }*/
        return view("pages.login");
    }
    
    function signup(Request $request) {
        //TODO if not connected
        /*if ($request->session()->has('loginID')) {
            return redirect('/');
        }*/
        return view("pages.signup");
    }

    function logout(Request $request) {
        /*$request->session()->flush('loginID');
        $request->session()->flush('loginUsername');
        return redirect('/login');*/
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    function tryLogin(Request $request) {

        /*$user = User::firstOrCreate();
        Auth::login($user, true);
        Auth::logout($user);

        Auth::id();*/

        if ($request->session()->has('loginID')) {
            return ['error' => 'alreadyConnected', 'success' => true];
        }
        if (isset($request->username) && isset($request->password)) {
            /*$user = User
            ::where('username', '=', $request->username)
            ->first();
            if ($user != null) {
                if (Hash::check($request->password, $user->password)) {
                    
                    Auth::login($user, true);
                    return ['error' => '', 'success' => true];
                }
                return ['error' => 'wrongPassword'];
            }*/
            
            if(Auth::attempt(["username" => $request->username, "password" => $request->password])) {
                
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
