@php
    if (!function_exists('is_menu_active')) {
        /**
         * Check if a menu item is active based on the current URL.
         *
         * @param string $slug The slug of the menu item.
         * @param array $children Optional array of child menu slugs.
         * @return bool True if the menu item is active, false otherwise.
         */
        function is_menu_active(string $slug, array $children = [])
        {
            $currentUrl = request_url();
            $menuUrl = admin_url($slug);

            if (!empty($children)) {
                foreach ($children as $child) {
                    if (is_menu_active($child)) {
                        return true;
                    }
                }
            }

            return $currentUrl === $menuUrl;
        }
    }
@endphp
<div class="flex h-full w-64 flex-col border-r bg-gray-50 dark:bg-background">
    <div class="flex h-16 items-center border-b px-6">
        <a class="flex items-center gap-2 font-semibold" href="{{ admin_url() }}">
            <span class="dashicons dashicons-superhero text-3xl"></span>
            <span>TinyPress</span>
        </a>
    </div>
    <nav class="flex-1 space-y-1 px-3 py-4">
        @foreach (dashboard()->getMenu() as $item)
            @php
                $isActive = is_menu_active($item['slug'], $item['children']->pluck('slug')->toArray());
            @endphp
            <a @class([
                'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium',
                'transition-colors text-muted-foreground hover:bg-muted hover:text-foreground' => !$isActive,
                'bg-primary text-primary-foreground' => $isActive,
            ]) href="{{ admin_url($item['slug']) }}">
                <span class="dashicons {{ $item['icon'] }} text-2xl"></span>
                {{ $item['title'] }}</a>
        @endforeach
    </nav>
</div>
