<?php
    namespace App\Http\Controllers;

use Illuminate\Http\Request;


class HomeController extends Controller
    {
        public function index(Request $request)
        {
            if(session('id', false) !== false)
                session(['id' => -rand(1)]);

            return view('pages.main'); 
        }
    }
