<x-cms::html>
    <div class="flex h-screen overflow-hidden">
        @include('cms::layout.sidebar')
        <div class="flex flex-1 flex-col overflow-hidden">
            @include('cms::layout.header')
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-background p-6">
                <div class="space-y-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</x-cms::html>
