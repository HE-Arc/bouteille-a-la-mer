@extends('layout.app')
@section('content')

<div id="app">
<div class="container row">
        <center>
            <div>
                <img class="logo" src="{{ URL::asset('img/logo.png') }}" alt="logo" width="100px">
                <h6>Bouteille à la mer</h6>
            </div>
        </center>
        <h6>Sign in</h6>
        <p>Hi there! Nice to see you again.</p>
        <form @submit="checkForm" id="loginForm"  ref="loginForm">
            @csrf
            <div class="input-field col s12">
                <input placeholrder="Username" v-model="username" name="username" id="username" type="text" class="validate">
                <label for="username">Username</label>
            </div>
            <div class="input-field col s12">
                <input placeholrder="Password" v-model="password" name="password" id="password" type="password" class="validate">
                <label for="password">Password</label>
            </div>
            <div class="error" id="error"></div>
            <div class="spacer"></div>
            <button class="btn waves-effect waves-light" type="submit" name="action" value="login" id="btnSubmit">Submit</button>
        </form>
        <p v-if="errors.length > 0">
            <b>Please correct the following error(s):</b>
            <ul>
                <li v-for="error in errors">@{{ error }}</li>
            </ul>
        </p>
        <div class="spacer"></div>
        <p>You do not have an account ? <a href="signup">Sign up</a></p>
    </div>
</div>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/login.js') }}"></script>
@endsection