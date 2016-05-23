@extends('layouts.app')

@section('title')
	Survivors Network | The network for all suvivors in all sitch. 
@endsection
@section('content')
	<div class="menu-centered"  style="margin-top:30px;">
		<ul class="menu spacing">
			@if(Auth::check())
				<li><a data-toggle="animatedModal10">New Topic</a></li>
			@endif
			<li><input type="search" placeholder="Search"></li>
			<li><button type="button" class="button">Search</button></li>
		</ul>
	</div>
	<section>
		<div class="grid container">
			@foreach($topics as $topic)
			  @include('partials.cards')
			@endforeach
		</div>
	</section>
	@if(Auth::check())
		@include('partials.topicmodal')
	@endif
@endsection

@section('script')
	<script src="socket.io/socket.io.js"></script>
	<script type="text/javascript">
		var socket = io('http://52.87.187.229:6001');
		socket.on('user.edantonio505:App\\Events\\UserIsInspired', function(data){
			console.log(data);
		});
	</script>
@endsection