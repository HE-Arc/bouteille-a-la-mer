@extends('layout.app')
@section('content')

<div class="container row">
    <center>
        <div>
            <img class="logo" src="{{ URL::asset('img/logo.png') }}" alt="logo" width="100px">
            Bouteille à la mer
        </div>
    </center>
    <h2>Sign in</h2>
    <p>Hi there! Nice to see you again.</p>
    <form action="/tryLogin" method="POST">
        @csrf
        <div class="input-field col s12">
            <input placeholrder="Username" name="username" id="username", type="text" class="validate">
            <label for="username">Username</label>
        </div>
        <div class="input-field col s12">
            <input placeholrder="Password" name="password" id="password", type="password" class="validate">
            <label for="password">Password</label>
        </div>
        <div class="spacer"></div>
        <div class="spacer"></div>
        <input type="submit" class="btn waves-effect waves-light col s12" value="login">
    </form>
    <div class="spacer"></div>
    <p>You do not have an account ? <a href="signup">Sign up</a></p>


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