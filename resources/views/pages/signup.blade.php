@extends('layout.app')
@section('content')

<div id="app">
    <div class="container row">
        <center>
            <div>
                <img class="logo" src="{{ URL::asset('img/logo.png') }}" alt="logo" width="100px" action="trySignup" method="post">
                Bouteille à la mere
            </div>
        </center>
        <h2>Sign up</h2>
        <p>Hi there! Nice to see you again.</p>
        <form @submit="checkForm" id="signupForm" ref="signupForm">
            @csrf
            <div class="input-field col s12">
                <input placeholrder="Username" v-model="username" name="username" id="username" type="text" class="validate">
                <label for="username">Username</label>
            </div>
            <div class="input-field col s12">
                <input placeholrder="Password" v-model="password" name="password" id="password" type="password" class="validate">
                <label for="password">Password</label>
            </div>
            <div class="input-field col s12">
                <input placeholrder="Password" v-model="password2" name="password2" id="password2" type="password" class="validate">
                <label for="password2">Password</label>
            </div>
            <div class="error" id="error" ref="error"></div>
            <p v-if="errors.length > 0">
                <b>Please correct the following error(s):</b>
                    <ul>
                        <li v-for="error in errors">@{{ error }}</li>
                    </ul>
                </p>
            <div class="spacer"></div>
            <input type="submit" class="btn waves-effect waves-light col s12" value="Continue">
        </form>
        <div class="spacer"></div>
        <p>Have an account ? <a href="login">Sign in</a></p>
    </div>
</div>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/signup.js') }}"></script>
@endsection