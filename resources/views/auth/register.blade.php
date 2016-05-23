@extends('layouts.app')

@section('content')
<div class="row container">
  <div class="medium-6 large-4 large-centered columns">
    <form method="POST" action="{{ url('/register') }}">
        {!! csrf_field() !!}
      <div class="row column log-in-form">
        <div>
        <h4 class="text-center">Sign up for an account</h4>
        <label>Name
            <input type="text"  name="name" value="{{ old('name') }}" placeholder="Create a Unique Username">
            @if ($errors->has('name'))
                <span class="error">{{ $errors->first('name') }}</span>
            @endif
        </label>
        <label>Email
            <input type="email"  name="email" value="{{ old('email') }}">
            @if ($errors->has('email'))
                <span class="error">{{ $errors->first('email') }}</span>
            @endif
        </label>
        <label>Password
            <input id="password" type="password"  name="password">
            @if ($errors->has('password'))
                <span class="error">{{ $errors->first('password') }}</span>
            @endif
        </label>
        <label>Confirm Password
            <input type="password"  name="password_confirmation">
            @if ($errors->has('password_confirmation'))
                <span class="error">{{ $errors->first('password_confirmation') }}</span>
            @endif
        </label>
        <input id="show-password" type="checkbox"><label for="show-password">Show password</label>
        <input type="submit" value="Register" class="expanded button" />
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
