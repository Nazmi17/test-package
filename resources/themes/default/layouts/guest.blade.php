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
    @themeHeaders('guest')

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
    @themeStyles('guest')

    @vite(['resources/css/app.css'])
    @livewireStyles
    @stack('styles')
</head>

<body
    x-data="{ 'loaded': true, 'darkMode': $persist(false).as('darkMode') }"
    :class="{ 'dark bg-gray-900': darkMode === true }"
>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

    {{-- Theme CDN Scripts --}}
    @themeScripts('guest')

    @vite(['resources/js/app.js'])
    @livewireScripts
    @stack('scripts')
</body>

</html>
