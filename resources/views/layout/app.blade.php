<!DOCTYPE html>
<html>
<head>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="{{ URL::asset('materialize/css/materialize.min.css') }}"  media="screen,projection"/>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!--Title !-->
    <title>Bouteille à la mer</title>

    <!--Fav icon-->
    <link rel="shortcut icon" type="image/png" href="{{ URL::asset('/img/logo.png') }}"/>
    <link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
    
    <!--VueJs CDN-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>

    <!--Map box-->
    <script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css' rel='stylesheet' />
    
    <!--Materilize js-->
    <script type="text/javascript" src="{{ URL::asset('materialize/js/materialize.min.js') }}"></script>
</head>

<body>
    @yield('content')
</body>
</html>