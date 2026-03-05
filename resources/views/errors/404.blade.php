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
    <title>404 - Page Not Found | {{ config('app.name') }}</title>
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
</head>

<body
    class="flex min-h-screen items-center justify-center bg-gray-50 dark:bg-gray-900"
    x-data="{ 'darkMode': false }"
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
    $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{ 'dark': darkMode === true }"
>
    <div class="mx-auto max-w-2xl px-4 text-center">
        <div class="mb-8">
            <h1 class="mb-4 text-9xl font-bold text-gray-900 dark:text-white">404</h1>
            <p class="mb-2 text-3xl font-semibold text-gray-800 dark:text-gray-200">
                Page Not Found
            </p>
            <p class="mb-8 text-lg text-gray-600 dark:text-gray-400">
                Sorry, the page you are looking for doesn't exist or has been moved.
            </p>
        </div>

        <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a
                href="{{ url('/') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800"
            >
                <svg
                    class="size-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                </svg>
                Go Home
            </a>

            <button
                onclick="window.history.back()"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
            >
                <svg
                    class="size-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>
                Go Back
            </button>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
