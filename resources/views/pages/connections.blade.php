@extends('layouts.app')

@section('title')
	Connections
@endsection

@section('content')
	<div class="row" style="text-align: center;">
		<div class="large-12 large-centered columns">
			<h4>Connections</h4>
		</div>
		@foreach(Auth::user()->connections() as $user)
			<div class="large-2 columns">
				<img class="user-connection-profile" src="{{$user->getAvatarListUrl()}}">
				<a href="{{ route('profile', ['username' => $user->name]) }}">{{ $user->name }}</a>
			</div>
		@endforeach
	</div>
@endsection