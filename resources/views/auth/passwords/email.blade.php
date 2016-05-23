@extends('layouts.app')

<!-- Main Content -->
@section('content')

                    @if (session('status'))
                        <div class="callout small primary" data-closable>
                            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif

<div class="row">
  <div class="medium-6 medium-centered large-4 large-centered columns">
    <form>
        {!! csrf_field() !!}
      <div class="row column log-in-form">
        <div>
            <h4 class="text-center">Reset Password</h4>
            <label>Email Address
                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="error">{{ $errors->first('email') }}</span>
                @endif
            </label>
            
            <p><input type="submit" class="expanded button" value="Send Password Reset Link"></p>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
