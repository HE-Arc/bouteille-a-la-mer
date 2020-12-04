<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use function PHPUnit\Framework\isEmpty;

class LoginController extends Controller
{
    function login(Request $request) {
        //TODO If not connected
        if ($request->session()->has('loginID')) {
            return redirect('/');
        }
        return view("pages.login");
    }
    
    function signup(Request $request) {
        //TODO if not connected
        if ($request->session()->has('loginID')) {
            return redirect('/');
        }
        return view("pages.signup");
    }

    function logout(Request $request) {
        $request->session()->flush('loginID');
        $request->session()->flush('loginUsername');
        return redirect('/login');
    }

    function tryLogin(Request $request) {
        if ($request->session()->has('loginID')) {
            return ['error' => 'alreadyConnected', 'success' => true];
        }
        if (isset($request->username) && isset($request->password)) {
            $user = User
            ::where('username', '=', $request->username)
            ->first();
            if ($user != null) {
                if (Hash::check($request->password, $user->password)) {
                    $request->session()->put('loginID', $user->id);
                    $request->session()->put('loginUsername', $user->username);
                    return ['error' => '', 'success' => true];
                }
                return ['error' => 'wrongPassword'];
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
                $user->password = bcrypt($request->password);
                $user->save();
                session(['loginID' => $user->id]);
                session(['loginUsername' => $user->username]);
                return ['error' => '', 'success' => true];
            }
            return ['error' => 'alreadyExist'];
        }
        return ['error' => 'wrongRequest'];        
    }
}
