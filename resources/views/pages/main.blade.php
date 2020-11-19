@extends('layout.app')
@section('content')
<div id="app">
	<div id="status" class="center-align card-panel teal">
		<p v-if="connected" class="flow-text">
			Connected
		</p>
		<p v-else class="flow-text">
			Not connected
		</p>
	</div>

	<div id="map"></div>
	
	<div class="container">
			<!-- Page Content goes here -->
			<a id="drop-btn" class="btn-floating btn-large waves-effect waves-light">
				<img id="drop-img" src="{{ URL::asset('/img/drop_bottle.png') }}">
			</a>
	</div>
</div>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection