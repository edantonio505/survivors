@extends('layouts.app')


@section('title')
	{{ $user->name }}'s profile
@endsection


@section('content')
<div class="container">
	<section>
		<div id="profilepagepic" class="small-12  small-centered medium-6 medium-centered large-4 large-centered columns">
			<div class="row">
				<div class="large-8 large-centered columns">
					<img class="profilepic"src="{{ $user->getAvatarProfileUrl() }}">
					<h4>{{ $user->name }}</h4>		
				</div>
				<div class="large-5 large-centered columns">
					@if(Auth::user()->hasConnectionRequestPending($user))
					Waiting for {{ $user->name }} to accept your request
					@elseif(Auth::user()->hasConnectionRequestReceived($user))
					<a href="{{ route('accept_connection', ['username' => $user->name]) }}"><button class="button">Accept Connection Request</button></a>
					@elseif(Auth::user()->isConnectionsWith($user))
					{{ $user->name }} is your connection
					@elseif(Auth::user()->id === $user->id)

					@else
					<a href="{{ route('add_connection', ['username'=> $user->name]) }}"><button class="expanded button">Add Connection</button></a>
					@endif
				</div>	
			</div>
		</div>
		<div class="row top-space" style="text-align:center;">
				<div class="large-4 columns">
					<h5>Topics</h5>
					<h3>{{ $user->Topics()->count() }}</h3>
				</div>
  				<div class="large-4 columns"><h5>Connections</h5><h3 class="green">{{ $user->connections()->count() }}</h3></div>
  				<div class="large-4 columns"><h5>Inspired</h5><h3>{{ $user->inspiredCount() }}</h3></div>
			</div>
	</section>
</div>
@endsection	