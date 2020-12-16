@extends('layout.app')
@section('content')

<?php
$data = [
	'username' => Illuminate\Support\Facades\Auth::user()->username ?? "",
	'id' => Illuminate\Support\Facades\Auth::user()->id ?? session('id')
];
?>
<script>
	//Set the map box
	mapboxgl.accessToken = "{{ env('MAPBOX_KEY') }}";
</script>

<div id="app" :mdata="{{ json_encode($data) }}" class="main-app">
	<div id="status" class="center-align main-page z-depth-3">
		<p v-if="connected" class="flow-text">
			Connected as : <b>@{{username}}</b>
		</p>
		<p v-else class="flow-text">
			Not connected
		</p>
	</div>

	<div id="map" class="main-page"></div>
	
	<div class="container main-page">
		<a id="drop-btn" ref="drop_btn" class="btn-floating btn-large waves-effect waves-light"  @click="toggleDropPage">
			<img id="drop-img" src="{{ URL::asset('/img/drop_bottle.png') }}">
		</a>

		<ul id="slide-out" class="sidenav" ref="sidenav">
			<li>
				<div class="user-view">
					<div class="background">
						<img src="{{ URL::asset('/img/nico.png') }}" style="width: 100%; height: 100%;">
					</div>
					<a href="#user"><img class="circle" src="{{ URL::asset('/img/logo.png') }}"></a>
					<a href="#name"><span class="white-text name">@{{username}}</span></a>
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
					<div v-for="conversation in conversations" :key="conversation.id">
						<div class="col s10">
							<li>
								<a class="waves-effect truncate" href="#!" @click="toggleMessagePage(conversation.id)">@{{ conversation.messages[0].content }}</a>
							</li>
						</div>
						<div class="col s2 valign-wrapper">
							<p class="flow-text">@{{getTimeLeftStr(conversation.time_of_death)}}</p>
						</div>
					</div>
				</div>
			</div>
		</ul>
	</div>
	<div id="drop-page" ref="drop_page" class="hide-drop-page z-depth-3">
		<nav id="drop-bottle-title" class="z-depth-4">
			<div class="nav-wrapper">
				<a href="#" class="center">Drop a bottle !</a>
				<ul id="nav-mobile" class="left ">
					<li>
						<a ref="return_to_map_btn" @click="toggleDropPage">
							<i class="material-icons">arrow_back</i>
						</a>
					</li>
				</ul>
			</div>
		</nav>
		<div class="container">
			<div class="row">
				<form id="conversationForm" ref="conversationForm" onsubmit="event.preventDefault(); postConversation()">
					@csrf
					<div class="col s12">
						<div class="input-field col s12">
							<i class="material-icons prefix">access_time</i>
							<input id="life-time-input" type="text" class="timepicker" ref="timepicker" name="lifetime">
							<label for="life-time-input">Life time</label>
						</div>
						<div class="input-field col s12">
							<i class="material-icons prefix">mode_edit</i>
							<textarea id="first-message-input" class="materialize-textarea" name="message"></textarea>
							<label for="first-message-input">First message</label>
						</div>
						<div class="input-field col s12 center-align">
							<a id="confirm-drop-btn" class="btn-floating btn-large waves-effect waves-light z-depth-4">
								<input type="image" id="drop-img" src="{{ URL::asset('/img/drop_bottle.png') }}">
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div id="message-page" ref="message_page" class="white hide-message-page">
		<nav id="message-title">
				<div class="nav-wrapper">
					<a href="#" class="center">Chat !</a>
					<ul id="nav-mobile" class="left waves-effect waves-light">
						<li>
							<a ref="return_to_map_btn" @click="toggleMessagePage(-1)">
								<i class="material-icons">arrow_back</i>
							</a>
						</li>
					</ul>
				</div>
		</nav>
		<div class="page-content">

			<ul class="collection">
				<li class="collection-item" v-for="message in currentConversation.messages" :key="message.id">
					<span class="sender"><b>@{{message.username ?? 'Anonymous'}}</b></span>
					<a href="#!" class="secondary-content"><p>@{{timeToStr(message.posted)}}</p></i></a>
					<div v-if='message.content != ""'>
						<p class="truncate">
							@{{message.content}}
						</p>
					</div>
					<div v-if='message.image != ""'>
						<img :src="message.image" style="width: 100%; height: 100%;"/>
					</div>
				</li>
			</ul>

			<div class="row valign-wrapper">
				<div class="input-field col s10">
					<textarea ref="textareamessage" id="textareamessage" class="materialize-textarea" v-on:keydown.13.prevent="sendMessage"></textarea>
					<label for="textareamessage">Write a message</label>
				</div>
				<a class="btn-flat btn-large waves-effect waves-light" @click="sendMessage">
					<i class="material-icons col s2">send</i>
				</a>
			</div>
		</div>
	</div>
</div>

<script src="{{ URL::asset('/js/socketmessage.js') }}"></script>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection