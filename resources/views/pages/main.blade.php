@extends('layout.app')
@section('content')

<div id='map'></div>
</div>

<div class="container">
        <!-- Page Content goes here -->
</div>


<!-- Map box -->
<script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css' rel='stylesheet' />

<script src="{{ URL::asset('/js/socketmessage.js') }}"></script>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection