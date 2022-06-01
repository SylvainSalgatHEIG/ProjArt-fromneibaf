{{-- resources/views/vue.blade.php --}}
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel 9 Vue 3 - DevDuniya.com</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>
<body class="antialiased">
	<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
		@if (Route::has('login'))
			<div class="fixed top-0 right-0 px-6 py-4 sm:block">
				@auth
					<a href="{{ url('/home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Home</a>
				@else
					<a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

					@if (Route::has('register'))
						<a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
					@endif
				@endauth
			</div>
		@endif
	<div id="myApp"></div>

	<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>