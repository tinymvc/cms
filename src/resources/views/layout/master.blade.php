<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tiny CMS</title>
    @vite('app.js')
</head>

<body>

    @include('cms::layout.header')
    @include('cms::layout.sidebar')

    <main id="app">
        <div>
            @yield('content')
        </div>
    </main>

</body>

</html>
