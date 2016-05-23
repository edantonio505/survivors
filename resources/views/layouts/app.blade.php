<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title')</title>
        <link href='https://fonts.googleapis.com/css?family=Maven+Pro' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="{{ elixir('css/all.css') }}">
        @yield('styles')
    </head>
    <body>
        @include('partials.nav')
        @yield('content')
        <script src="{{ elixir('js/all.js') }}"></script>
    </body>
    @yield('script')
</html>