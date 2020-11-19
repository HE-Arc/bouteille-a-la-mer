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

	<div id="map" style="width: 0px;height: 0px;"></div>
	
	<div class="container">
		<!-- Page Content goes here -->
		<a id="drop-btn" class="btn-floating btn-large waves-effect waves-light">
			<img id="drop-img" src="{{ URL::asset('/img/drop_bottle.png') }}">
		</a>

		<nav> <h1>yo ?</h1> </nav>

		<ul id="slide-out" class="sidenav">
			<li><div class="user-view">
			<div class="background">
				<img src="images/office.jpg">
			</div>
			<a href="#user"><img class="circle" src="images/yuna.jpg"></a>
			<a href="#name"><span class="white-text name">John Doe</span></a>
			<a href="#email"><span class="white-text email">jdandturk@gmail.com</span></a>
			</div></li>
			<li><a href="#!"><i class="material-icons">cloud</i>First Link With Icon</a></li>
			<li><a href="#!">Second Link</a></li>
			<li><div class="divider"></div></li>
			<li><a class="subheader">Subheader</a></li>
			<li><a class="waves-effect" href="#!">Third Link With Waves</a></li>
		</ul>
		<a href="#" data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a>
	</div>
</div>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection