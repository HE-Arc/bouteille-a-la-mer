@extends('layout.app')
@section('content')

<div id="app">
    <div class="container row">
        <center>
            <div>
                <img class="logo" src="{{ URL::asset('img/logo.png') }}" alt="logo" width="100px" action="trySignup" method="post">
                <h6>Bouteille à la mere</h6>
            </div>
        </center>
        <h6>Sign up</h6>
        <p>Hi there! Nice to see you again.</p>
        <form @submit="checkForm" id="signupForm" ref="signupForm">
            @csrf
            <div class="input-field col s12">
                <input placeholder="Username" v-model="username" name="username" id="username" type="text" class="validate">
                <label for="username">Username</label>
            </div>
            <div class="input-field col s12">
                <input placeholder="Password" v-model="password" name="password" id="password" type="password" class="validate">
                <label for="password">Password</label>
            </div>
            <div class="input-field col s12">
                <input placeholder="Password" v-model="password2" name="password2" id="password2" type="password" class="validate">
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
            <button class="btn waves-effect waves-light" type="submit" name="action" value="signup" id="btnSubmit">Submit</button>
        
        </form>
        <div class="spacer"></div>
        <p>Have an account ? <a href="login">Sign in</a></p>
    </div>
</div>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/signup.js') }}"></script>
@endsection