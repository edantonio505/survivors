@extends('layouts.app')

@section('title')
	Admin Dashboard
@endsection
@section('content')
	<div class="row">
		<div class="large-5 columns">
			<form action="{{ route('create_topic_title') }}" method="POST">
			{{ csrf_field() }}
				<div class="row">
					<div class="large-12 columns">
						<label>Topic Title
							<input type="text" name="topic_title" />
						</label>
					</div>
					<div class="large-12 columns">
						<input type="submit" class="expanded button" value="Create a new Topic"/>
					</div>
				</div>
			</form>
		</div>

		<div class="large-7 columns">
			<ul>
			    @foreach($topics as $topic)
			    	<li>{{ $topic->topic_title }}</li>
			    @endforeach
			</ul>
		</div>
	</div>
@endsection
