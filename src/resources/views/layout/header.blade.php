<div class="flex h-16 items-center justify-between border-b bg-gray-50 dark:bg-background px-6">
    <div class="flex items-center space-x-4">
        <button
            class="p-2 rounded-lg transition-colors text-foreground/65 hover:bg-muted hover:text-foreground/85 hidden lg:block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-panel-left w-5 h-5" aria-hidden="true">
                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                <path d="M9 3v18"></path>
            </svg>
        </button>
        <div class="flex items-center space-x-2">
            <a class="transition-colors text-foreground/65 hover:text-foreground/85" href="{{ admin_url() }}"
                data-discover="true">
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            @php
                $menuItem = dashboard()->getCurrentMenuItem();
            @endphp
            @if (!empty($menuItem))
                <div class="hidden md:flex items-center space-x-2 text-sm text-border">
                    <span>/</span>
                    @isset($menuItem['child'])
                        <a href="{{ admin_url($menuItem['slug']) }}"
                            class="text-foreground/65 hover:text-foreground/85">{{ $menuItem['title'] }}</a>
                        <span>/</span>
                        <span class="text-foreground font-medium">{{ $menuItem['child']['title'] }}</span>
                    @else
                        <span class="text-foreground font-medium">{{ $menuItem['title'] }}</span>
                @endif
            </div>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-4">
        <button
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground px-4 py-2 relative h-10 w-10 rounded-full"
            type="button" id="radix-_R_16kndlb_" aria-haspopup="menu" aria-expanded="false" data-state="closed">
            <span class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-full">
                <img src="{{ get_gravatar(user('email')) }}" alt="">
            </span>
        </button>
    </div>
    </div>
