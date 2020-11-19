@extends('layout.app')
@section('content')

<div class="container row">
    <center>
        <div>
            <img class="logo" src="{{ URL::asset('img/logo.png') }}" alt="logo" width="100px">
            Bouteille à la mer
        </div>
    </center>
    <h2>Sign up</h2>
    <p>Hi there! Nice to see you again.</p>
    <form action="/trySignup" method="POST">
        @csrf
        <div class="input-field col s12">
            <input placeholrder="Username" name="username" id="username", type="text" class="validate">
            <label for="username">Username</label>
        </div>
        <div class="input-field col s12">
            <input placeholrder="Password" name="password" id="password", type="password" class="validate">
            <label for="password">Password</label>
        </div>
        <div class="input-field col s12">
            <input placeholrder="Password" name="password2" id="password2", type="password" class="validate">
            <label for="password2">Password</label>
        </div>
        <div class="spacer"></div>
        <div class="spacer"></div>
        <input type="submit" class="btn waves-effect waves-light col s12" value="Continue">
    </form>
    <div class="spacer"></div>
    <p>Have an account ? <a href="login">Sign in</a></p>


</div>

<style>
    .logo {
        display: block;
    }
    .spacer {
        height: 100px;
    }
</style>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection