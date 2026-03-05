<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <meta
        name="description"
        content="{{ config('synapps.apps.description') }}"
    >

    <title>{{ $title ?? config('synapps.apps.title', config('app.name', 'Laravel')) }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 antialiased dark:bg-gray-900">
    <div class="flex min-h-screen flex-col">
        <!-- Header -->
        <header class="bg-white shadow dark:bg-gray-800">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo/Brand -->
                    <div class="shrink-0">
                        <a
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                            href="/"
                        >
                            {{ config('synapps.apps.title', config('app.name', 'Laravel')) }}
                        </a>
                    </div>

                    <!-- Navigation Menu -->
                    <div class="hidden items-center gap-6 md:flex">
                        <a
                            class="text-sm font-medium text-gray-700 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                            href="/"
                            wire:navigate
                        >
                            Home
                        </a>
                        <a
                            class="text-sm font-medium text-gray-700 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                            href="{{ route('todos.index') }}"
                            wire:navigate
                        >
                            Todos
                        </a>
                        {{-- Add more menu items here --}}
                    </div>

                    <!-- Auth Links -->
                    <div class="flex items-center gap-4">
                        @if (config('synapps.auth'))
                            @if (auth()->check())
                                <a
                                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 font-semibold text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
                                    href="{{ backend_route(config('synapps.auth.routes.after_login')) }}"
                                >
                                    Dashboard
                                </a>
                            @else
                                <a
                                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 font-semibold text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
                                    href="{{ route('auth.login') }}"
                                >
                                    Login
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="grow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>

</html>
