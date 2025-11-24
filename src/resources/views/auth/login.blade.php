<x-cms::html>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full max-w-md">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Sign In</h3>
                <p class="text-sm text-muted-foreground">Enter your credentials to access your account</p>
            </div>
            <div class="p-6 pt-0 space-y-4"><button
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full"
                    type="button"><svg class="mr-2 h-4 w-4" viewBox="0 0 24 24">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4"></path>
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853"></path>
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05"></path>
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335"></path>
                    </svg>Continue with Google</button>
                <div class="relative">
                    <div class="absolute inset-0 flex items-center"><span class="w-full border-t"></span></div>
                    <div class="relative flex justify-center text-xs uppercase"><span
                            class="bg-background px-2 text-muted-foreground">Or continue with</span></div>
                </div>
                <form class="space-y-4">
                    <div class="space-y-2"><label
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                            for="_r_3_-form-item">Email</label><input
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="you@example.com" id="_r_3_-form-item"
                            aria-describedby="_r_3_-form-item-description" aria-invalid="false" type="email"
                            value="" name="email"></div>
                    <div class="space-y-2"><label
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                            for="_r_4_-form-item">Password</label><input
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            id="_r_4_-form-item" aria-describedby="_r_4_-form-item-description" aria-invalid="false"
                            type="password" value="" name="password"></div>
                    <div class="flex items-center justify-between"><a class="text-sm text-primary hover:underline"
                            href="/forgot-password">Forgot password?</a></div><button
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full"
                        type="submit">Sign In</button>
                </form>
            </div>
            <div class="items-center p-6 pt-0 flex justify-center">
                <p class="text-sm text-muted-foreground">Don't have an account? <a class="text-primary hover:underline"
                        href="/register">Sign up</a></p>
            </div>
        </div>
    </div>
</x-cms::html>
