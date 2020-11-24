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
    <form action="/tryLogin" method="POST" onsubmit="return onLogin()" id="loginForm">
        @csrf
        <div class="input-field col s12">
            <input placeholrder="Username" name="username" id="username", type="text" class="validate">
            <label for="username">Username</label>
        </div>
        <div class="input-field col s12">
            <input placeholrder="Password" name="password" id="password", type="password" class="validate">
            <label for="password">Password</label>
        </div>
        <div class="error" id="error"></div>
        <div class="spacer"></div>
        <input type="submit" class="btn waves-effect waves-light col s12" value="login" id="btnSubmit">
    </form>
    <div class="spacer"></div>
    <p>You do not have an account ? <a href="signup">Sign up</a></p>
</div>



<!-- Page js and css -->

<script>
    function onLogin() {
        $('#btnSubmit').attr("disabled", true);
        $.post("tryLogin", $('#loginForm').serialize(), (result) => {
            console.log(result);
            if (result.success === true) {
                window.location.replace("/");
            } else {
                $('#btnSubmit').attr("disabled", false);
                if (result.error === "wrongPassword") {
                    displayError("Wrong password. Try again.");
                } else if (result.error === "wrongUsername") {
                    displayError("This username doesn't exist. Try again or <a href='signup'>sign up</a>");
                } else {
                    displayError("Something went wrong");
                }
            }
        });
        return false;
    }

    function displayError(error) {
        console.log(error);
        $('#error').html(error);
    }
</script>
@endsection