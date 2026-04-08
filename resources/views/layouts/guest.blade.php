<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'He thong quan ly KTX') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="linear-shell transition-colors duration-300">
        <div class="relative flex min-h-screen items-center justify-center px-4 py-10">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_#e2e8f0,_#ffffff_55%)]"></div>

            <div class="relative w-full max-w-md animate-fade-up">
                <div class="mb-6 flex justify-center">
                    <a href="{{ route('login') }}">
                        <x-application-logo class="h-16" />
                    </a>
                </div>

                <div class="linear-panel px-6 py-6 sm:px-7">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
