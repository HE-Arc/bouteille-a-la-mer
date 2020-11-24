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
    <form action="/trySignup" method="POST" onsubmit="return onSignup()" id="signupForm">
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
        <div class="error" id="error"></div>
        <div class="spacer"></div>
        <input type="submit" class="btn waves-effect waves-light col s12" value="Continue">
    </form>
    <div class="spacer"></div>
    <p>Have an account ? <a href="login">Sign in</a></p>


</div>

<!-- Page js and css -->
<script>
    function onSignup() {
        if ($('#password').val() === $('#password2').val()) {
            $('#btnSubmit').attr("disabled", true);
            $.post("trySignup", $('#signupForm').serialize(), (result) => {
                console.log(result);
                if (result.success === true) {
                    alert("Success");
                } else {
                    $('#btnSubmit').attr("disabled", false);
                    if (result.error === "alreadyExist") {
                        displayError("This user already exist");
                    } else {
                        displayError("Something went wrong");
                    }
                }
            });
        } else {
            displayError("Warning ! the 2 passwords are not the same");
        }
        return false;
    }

    function displayError(error) {
        $('#error').html(error);
    }
</script>
@endsection