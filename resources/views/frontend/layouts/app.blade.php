<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title>{{ config('app.name', 'Laravel') }}</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- google fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Mulish&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Mulish:wght@700&display=swap" rel="stylesheet">
<!-- google fonts ends -->
	<meta content="" name="descriptison">
	<meta content="" name="keywords">

	<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
	<link href="{{ asset('frontend/css/front-style.css') }}" rel="stylesheet">

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script> var BASE_URL = "{{ URL::to('/') }}"; </script>
</head>
<body>
    <div class="main-section">
        @yield('content')
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>	
</body>
</html>