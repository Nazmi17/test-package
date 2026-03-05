<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >
    <meta
        name="description"
        content="{{ config('synapps.apps.description') }}"
    >

    <title>{{ $title ?? config('synapps.apps.title', config('app.name')) }}</title>

    {{-- Theme CDN Headers --}}
    @themeHeaders('frontend')

    <link
        href="https://fonts.googleapis.com"
        rel="preconnect"
    >
    <link
        href="https://fonts.gstatic.com"
        rel="preconnect"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap"
        rel="stylesheet"
    >

    {{-- Theme CDN Styles --}}
    @themeStyles('frontend')

    @vite(['resources/css/app.css'])
    @livewireStyles
    @stack('styles')
</head>

<body
    x-data="{ 'loaded': true, 'darkMode': $persist(false).as('darkMode') }"
    :class="{ 'dark bg-gray-900': darkMode === true }"
>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="text-xl font-bold">
                    <a href="/">{{ config('synapps.apps.title', config('app.name')) }}</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a
                            href="{{ route('backend.home.dashboard') }}"
                            class="text-gray-700 dark:text-gray-300 hover:text-primary"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('auth.login') }}"
                            class="text-gray-700 dark:text-gray-300 hover:text-primary"
                        >
                            Login
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-100 dark:bg-gray-900 py-6">
            <div class="container mx-auto px-4 text-center text-gray-600 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('synapps.apps.title', config('app.name')) }}. All rights reserved.
            </div>
        </footer>
    </div>

    {{-- Theme CDN Scripts --}}
    @themeScripts('frontend')

    @vite(['resources/js/app.js'])
    @livewireScripts
    @stack('scripts')
</body>

</html>
