<html>

<form action="/trySignup" method="post">
    @csrf
    <h1>Inscription</h1>
    <input type="text" name="username">
    <input type="password" name="password">
    <input type="submit" value="Sign up">
</form>
<form action="/tryLogin" method="post">
    @csrf
    <h1>Login</h1>
    <input type="text" name="username">
    <input type="password" name="password">
    <input type="submit" value="Login">
</form>
</html>