<!DOCTYPE html>
<html>
<head>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="resources/css/materialize.min.css"  media="screen,projection"/>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!--Title !-->
    <title>Bouteille à la mer</title>

    <!--Fav icon-->
    <link rel="shortcut icon" type="image/png" href="{{ URL::asset('/img/logo.png') }}"/>

    <!-- custom css-->
    <link href="{{ URL::asset('/css/app.css') }}" rel='stylesheet' />
</head>

<body>
    @yield('content')

    <!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="resources/js/materialize.min.js"></script>

    <!-- custom js-->
    <script src="{{ URL::asset('/js/app.js') }}"></script>
</body>
</html>