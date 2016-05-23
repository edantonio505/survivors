@extends('layouts.app')

@section('content')
<div class="container row">
    <div class="small-12 medium-12 large-12 columns">
		@if(Auth::check())
			Welcome {{ Auth::user()->name }}
		@endif
	</div>
</div>
@endsection
