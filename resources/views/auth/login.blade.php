@extends('layouts.app')

@section('content')
<div class="container row">
  <div class="medium-6 medium-centered large-4 large-centered columns">
    <form method="POST" action="{{ url('/login') }}">
        {!! csrf_field() !!}
      <div class="row column log-in-form">
        <div>
            <h4 class="text-center">Log in with you email account</h4>
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
            <input id="show-password" type="checkbox"><label for="show-password">Show password</label>
            <input type="submit" value="login" class="expanded button" />
            <p class="text-center"><a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a></p>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
