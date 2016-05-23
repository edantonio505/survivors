@extends('layouts.app')

@section('title')
	Requests
@endsection

@section('content')

<div class="row">
	@if(Auth::user()->connectionRequests()->count() > 0)
	<div class="large-6 large-centered columns">
	<h4>connection Requests</h4>
		<table>
  <thead>
    <tr>
      <th width="200">From</th>
      <th>View Profile</th>
      <th>Accept Request</th>
    </tr>
  </thead>
  <tbody>
  @foreach($requests as $user)
  	<tr>
      <td>{{ $user->name }}</td>
      <td><a href="{{ route('profile', ['username' => $user->name]) }}"><button class="expanded button">View Profile</button></a></td>
      <td><a href="{{ route('accept_connection', ['username' => $user->name]) }}"><button class="button">Accept Connection Request</button></a></td>
    </tr>


  @endforeach
  </tbody>
</table>
	</div>
	@else
		<h4>No Requests</h4>
	@endif
</div>


@endsection