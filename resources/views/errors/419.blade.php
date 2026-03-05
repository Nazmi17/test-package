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
    <title>419 - Page Expired | {{ config('app.name') }}</title>
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
            <div class="mb-4 flex justify-center">
                <svg
                    class="size-24 text-yellow-600 dark:text-yellow-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>
            <h1 class="mb-4 text-6xl font-bold text-gray-900 dark:text-white">419</h1>
            <p class="mb-2 text-3xl font-semibold text-gray-800 dark:text-gray-200">
                Page Expired
            </p>
            <p class="mb-8 text-lg text-gray-600 dark:text-gray-400">
                Your session has expired. Please refresh the page and try again.
            </p>
        </div>

        <div class="mb-8 rounded-lg bg-amber-50 p-4 dark:bg-amber-900/20">
            <div class="flex items-start gap-3">
                <svg
                    class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <div class="text-left">
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-300">
                        CSRF Token Mismatch
                    </p>
                    <p class="mt-1 text-sm text-amber-700 dark:text-amber-400">
                        This usually happens when the page has been inactive for too long. Refreshing the page will resolve this issue.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
            <button
                onclick="window.location.reload()"
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
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                    />
                </svg>
                Refresh Page
            </button>

            <a
                href="{{ url('/') }}"
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
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                </svg>
                Go Home
            </a>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
