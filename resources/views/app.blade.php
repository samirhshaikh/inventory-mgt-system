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

<style>
    .generic_vs_select .vs__search::placeholder,
    .generic_vs_select .vs__dropdown-toggle,
    .generic_vs_select .vs__dropdown-menu {
        @apply
        .bg-gray-200
        .text-gray-700
        .border
        .border-gray-400
        .text-sm
    }

    .generic_vs_select .vs__clear,
    .generic_vs_select .vs__open-indicator {
        @apply
        .bg-gray-200
    }

    .required_field,
    .required_field:focus,
    .required_field .vs__search::placeholder,
    .required_field .vs__dropdown-toggle,
    .required_field .vs__dropdown-menu {
        @apply .border-red-300
    }

    .vs__search, .vs__search:focus {
        line-height: 23px;
    }
</style>

</body>
</html>
