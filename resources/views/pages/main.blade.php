@extends('layout.app')
@section('content')
<div id="app">
	<div id="status" class="center-align card-panel teal">
		<p v-if="connected" class="flow-text">
			Connected as : @{{pseudo}}
		</p>
		<p v-else class="flow-text">
			Not connected
		</p>
	</div>
	<div id="demo"></div>

	<div id="map"></div>
	
	<div class="container">
		<!-- Page Content goes here -->
		<a id="drop-btn" class="btn-floating btn-large waves-effect waves-light">
			<img id="drop-img" src="{{ URL::asset('/img/drop_bottle.png') }}">
		</a>

		<ul id="slide-out" class="sidenav">
			<li>
				<div class="user-view">
					<div class="background">
						<img src="{{ URL::asset('/img/nico.png') }}" style="width: 100%; height: 100%;">
					</div>
					<a href="#user"><img class="circle" src="{{ URL::asset('/img/logo.png') }}"></a>
					<a href="#name"><span class="white-text name">@{{pseudo}}</span></a>
					<a href="#email"><span class="white-text email">@{{email}}</span></a>
				</div>
			</li>
			<li>
				<a href="#!">
					<i class="material-icons">chat</i>
					My conversations
				</a>
			</li>
			<div class="container">
				<div class="row">
					<div v-for="conversation in conversations" :key="conversation.key">
						<div class="col s10">
							<li>
								<a class="waves-effect truncate" href="#!">@{{conversation.lastmessage}}</a>
							</li>
						</div>
						<div class="col s2 valign-wrapper">
							<p class="flow-text">@{{conversation.strlefttime}}</p>
						</div>
					</div>
				</div>
			</div>
		</ul>
	</div>
</div>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection