{{-- resources/views/vue.blade.php --}}
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Fromneibaf</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
	<link rel="manifest" href="manifest.webmanifest" />
</head>
<body class="antialiased">
	
	<div id="myApp"></div>

	<script>
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', () => {
				navigator.serviceWorker.register('/sw.js');
			});
		}
	</script>

	<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>