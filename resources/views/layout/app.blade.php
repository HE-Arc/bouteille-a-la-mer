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
    
    <!--VueJs CDN-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>

    <!-- Map box -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css' rel='stylesheet' />
</head>

<body>
    @yield('content')

    <!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="{{ URL::asset('materialize/js/materialize.min.js') }}"></script>
</body>
</html>