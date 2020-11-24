<?php
    namespace App\Http\Controllers;

    class HomeController extends Controller
    {
        public function index()
        {
            session(['key' => 'value']);
            return view('pages.main');
        }
    }
