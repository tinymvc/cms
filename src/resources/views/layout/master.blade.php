<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - TinyCMS</title>
    @vite('app.js')
</head>

<body class="antialiased">

    <div class="min-h-screen">
        @include('cms::layout.header')

        <div class="flex">
            @include('cms::layout.sidebar')

            <!-- Main Content -->
            <main>
                <div>
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

</body>

</html>
