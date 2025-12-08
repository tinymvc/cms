<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - TinyCMS</title>
    <link rel="stylesheet" href="{{ asset_url('/cms/fonts/Inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset_url('/cms/dashicons/dashicons.css') }}">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite('app.js')
</head>

<body class="antialiased font-sans">

    {!! $slot !!}

</body>

</html>
