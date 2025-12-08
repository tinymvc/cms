<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - TinyCMS</title>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset_url('/cms/fonts/Inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset_url('/cms/dashicons/dashicons.css') }}">
    @vite('app.js')
</head>

<body class="antialiased font-sans">

    {!! $slot !!}

</body>

</html>
