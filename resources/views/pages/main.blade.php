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
	
	<div id="status" class="valign-wrapper z-depth-3">
		<p v-if="username" class="flow-text">
			Connected as : <b>@{{username}}</b>
		</p>
		<p v-else class="flow-text">
			Not connected
		</p>
	</div>

	<div id="map" class="main-page" onclick="M.Sidenav.getInstance(app.$refs.sidenav).close()"></div>
	
	<div class="container main-page">
		<a id="drop-btn" ref="drop_btn" class="btn-floating btn-large waves-effect waves-light"  @click="toggleDropPage">
			<img id="drop-img" src="{{ URL::asset('/img/drop_bottle.png') }}">
		</a>

		<a id="center-btn" class="waves-effect waves-light"  @click="centerOnMe()">
			<i class="material-icons">adjust</i>
		</a>

		<ul id="slide-out" class="sidenav" ref="sidenav">
			<li>
				<div class="user-view">
					<div class="background">
						<img src="{{ URL::asset('/img/default.png') }}" style="width: 100%;">
					</div>
					<a><img class="circle" src="{{ URL::asset('/img/logo.png') }}"></a>
					<a><span class="white-text email">@{{ username }}</span></a>
				</div>
			</li>
			<li>
				<a href="/login" v-if="username != null">
					<i class="material-icons">login</i>
					Login
				</a>
			</li>
			<li v-if="username == null">
				<a href="/logout">
					<i class="material-icons">logout</i>
					Logout
				</a>
			</li>
			<li>
				<a>
					<i class="material-icons">chat</i>
					My bottles
				</a>
			</li>
			<div class="container">
				<div class="row">
					<div v-for="conversation in getMyBottles" :key="'c'+conversation.id">
						<div class="col s10">
							<li>
								<a class="waves-effect truncate" @click="toggleMessagePage(conversation.id)">
									@{{ conversation.messages[0].content }}
								</a>
							</li>
						</div>
						<div class="col s2 valign-wrapper">
							<p class="time-of-death">@{{getTimeLeftStr(conversation.time_of_death)}}</p>
						</div>
					</div>
				</div>
			</div>
		</ul>	
		<a id="burger" data-target="slide-out" class="sidenav-trigger hide-on-small-only"><i class="material-icons large">menu</i></a>

	</div>
	<div id="drop-page" ref="drop_page" class="hide-drop-page z-depth-3">
		<nav id="drop-bottle-title" class="z-depth-4">
			<div class="nav-wrapper">
				<a class="center">Drop a bottle !</a>
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
							<input id="life-time-input" type="text" class="timepicker" ref="timepicker" name="lifetime" value="00:30">
							<label for="life-time-input">Life time</label>
						</div>
						<div class="input-field col s12">
							<i class="material-icons prefix">mode_edit</i>
							<textarea id="first-message-input" class="materialize-textarea" name="message" ref="firstmessage"></textarea>
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
				<b class="right" style="margin-right: 10px;">@{{ getTimeLeftStr(currentConversation.time_of_death) }}</b>
				<ul id="nav-mobile" class="left waves-effect waves-light">
					<li>
						<a ref="return_to_map_btn" @click="toggleMessagePage(-1)">
							<i class="material-icons">arrow_back</i>
						</a>
					</li>
				</ul>
			</div>
		</nav>

		<div ref="conversation" class="page-content">
			<ul class="collection">
				<li class="collection-item" v-if="updateMessage" v-for="message in currentConversation.messages" :key="'m' + message.id">
					<span class="sender" :class="{mymessage : message.author == id}"><b>@{{message.username ?? 'Anon#' + (-message.author)}}</b></span>
					<a class="secondary-content">
						<div class="like-button" @click="likeMessage(message.id)">
							<i class="material-icons" style="color: green">thumb_up</i>
							<b>@{{ message.nbLike }}</b>
						</div>
						@{{timeToStr(message.posted)}}
					</a>
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


			<form action="#">
				<div class="file-field input-field"><!-- TODO -->
					<div class="file-path-wrapper" style="display: none">
						<input ref="uploadImage" id="uploadImage" type="file"  accept="image/png, image/jpeg">
						<input ref="uploadImageName" id="uploadImageID" class="file-path validate truncate" type="text" placeholder="Upload your image here" readonly>
					</div>
				</div>
			</form>

			<div class="row valign-wrapper">
				<div class="input-field col s10">
					<textarea ref="textareamessage" id="textareamessage" class="materialize-textarea" v-on:keydown.13.prevent="sendMessage"></textarea>
					<label for="textareamessage">Write a message</label>
				</div>
				<div id="uploadImageBtn" class="btn-flat btn-large waves-effect waves-light col s1" @click="triggerUpload">
					<i class="material-icons">image</i>
				</div>
				<div class="btn-flat btn-large waves-effect waves-light col s1" @click="sendMessage">
					<i class="material-icons">send</i>
				</div>
			</div>
		</div>

	</div>
</div>

<script src="{{ URL::asset('/js/socketmessage.js') }}"></script>

<!-- Page js and css -->
<script src="{{ URL::asset('/js/main.js') }}"></script>
<link href="{{ URL::asset('/css/main.css') }}" rel='stylesheet' />
@endsection