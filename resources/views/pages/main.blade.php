@extends('layout.app')
@section('content')
<div class="container">
        <!-- Page Content goes here -->
        
<div id='map' style="width:100%; height:200px;"></div>
</div>

<!-- Map box -->

<script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css' rel='stylesheet' />
<script src="{{ URL::asset('/js/main.js') }}"></script>
@endsection