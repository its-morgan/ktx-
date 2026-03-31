<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Hệ thống quản lý KTX') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div
            class="relative min-h-screen bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ asset('images/ktx.jpg') }}');"
        >
            <div class="absolute inset-0 bg-black/50"></div>

            <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8">
                <div class="w-full max-w-md">
                    <div class="mb-6 flex justify-center">
                        <a href="{{ route('login') }}">
                            <x-application-logo />
                        </a>
                    </div>

                    <div class="w-full overflow-hidden rounded-2xl border border-white/30 bg-white/95 px-6 py-6 shadow-2xl backdrop-blur-sm sm:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
