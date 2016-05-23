@extends('layouts.app')

@section('title')
	{{ $topic->title }}
@endsection

@section('content')
<div class="container box-container">
	<h4>{{ $topic->title }}</h4>
	<p>{{ $topic->body }}</p>
	<p>by : <a href="{{ route('profile', ['username' => $topic->user->name]) }}">{{ $topic->user->name }}</a></p>
	<hr />

	<section>
	<form action="{{ route('postComment', ['id' => $topic->id]) }}" method="POST" id="comment-form">
	{{ csrf_field() }}
	  <div class="row">
	  	<div class="large-5 columns">
	  	<label>
			Add a comment
			<textarea name="body" placeholder="None"></textarea>
		</label>
		<input type="submit" class="small expanded button" value="submit your comment">
	  	</div>
	  </div>
	</form>
	</section>
	<section>
		<h5>Comments</h5>
		@foreach($topic->comments as $comment)
			<p>{{ $comment->user()->name }} said...</p>
			<blockquote>
				{{ $comment->body }} {{ $comment->created_at->diffForHumans() }} <br /> 
			</blockquote>
		@endforeach
	</section>
</div>
@endsection