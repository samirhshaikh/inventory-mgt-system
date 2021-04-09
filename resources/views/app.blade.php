<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-200">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="turbolinks-cache-control" content="no-cache">

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBHW-sKPkxFMhQDkstZjOPwEIaaJLFK-s8&libraries=places"></script>

    <title>IMS</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    @routes
</head>
<body class="font-open-sans leading-none antialiased">

@inertia

<script src="{{ str_replace('8080//', '8080/', mix('js/app.js')) }}" data-turbolinks-suppress-warning="false"></script>

</body>
</html>
