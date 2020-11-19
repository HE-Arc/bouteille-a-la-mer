@extends('layout.app')
@section('content')
<div id='app'>
	<nav style="margin-top: 0px;" id="status">
		<div class="nav-wrapper">
			<p v-if="connected" class="flow-text">
				Connected
			</p>
			<p v-else class="flow-text">
				Not connected
			</p>
		</div>
	</nav>

	<div id='map'></div>
	
	<div class="container">
			<!-- Page Content goes here -->
	</div>
</div>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection