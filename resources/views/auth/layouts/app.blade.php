<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
	<link href="{{ asset('frontend/css/front-style.css') }}" rel="stylesheet">

    <script>
        var BASE_URL = "{{ URL::to('/') }}";
        var CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
    
	<script src="{{ asset('js/jquery-3.6.0.min.js')}}"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('js/additional-methods.min.js') }}"></script>
    <script src="{{ asset('toastr/toastr.min.js') }}"></script>
    <script> var BASE_URL = "{{ URL::to('/') }}"; </script>
    <!-- <script src="{{ asset('js/script.js') }}" defer></script> -->

    <!-- <script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
    <script type="text/javascript" src="{{asset('js/firebase/firebase_configurations.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/firebase/firebase_operations.js')}}"></script> -->


    <script src="{{ asset('js/authentication.js') }}" defer></script>
</head>
<body>
    <div id="cover-spin"></div>
    @yield('content')
</body>
</html>
