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
        content=""
    >

    <title>@yield('title', config('synapps.apps.title', config('app.name')))</title>
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
    @vite(['resources/css/app.css'])
    @livewireStyles
    @yield('styles')
</head>

<body
    id="page-top"
    x-data="{ page: '{{ synav()->getActiveMenu() }}', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
    $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{ 'dark bg-gray-900': darkMode === true }"
>

    <div class="page-wrapper">
        @include('synapps::components.layouts.partials.sidebar')
        <div class="main-container">
            @include('synapps::components.layouts.partials.overlay')
            @include('synapps::components.layouts.partials.header')

            <main>
                <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @vite(['resources/js/app.js'])
    @livewireScripts
    @yield('scripts')
</body>

</html>
