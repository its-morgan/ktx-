<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'hethongquanlyktx') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="linear-shell transition-colors duration-300">
<div class="linear-shell">
    @include('admin.partials.sidebar')

    <div class="lg:pl-64">
        @include('admin.partials.navbar')

        <main class="animate-fade-up p-4 pb-24 sm:p-6 lg:pb-6">
            <x-breadcrumbs />

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    <div class="font-semibold">Co loi xay ra:</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $loi)
                            <li>{{ $loi }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('noidung')
        </main>
    </div>

    @include('admin.partials.toast')

    <nav class="linear-navbar-glass fixed bottom-0 left-0 right-0 z-50 border-t p-2 lg:hidden">
        <div class="mx-auto flex w-full max-w-lg items-center justify-between">
            <a href="{{ route('admin.trangchu') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('admin.trangchu') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9z"/>
                </svg>
                Home
            </a>
            <a href="{{ route('admin.quanlyhoadon') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('admin.quanlyhoadon') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-6m0 0V9a2 2 0 1 1 4 0v2m0 2v6M5 11h14" />
                </svg>
                Bill
            </a>
            <a href="{{ route('admin.quanlybaohong') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('admin.quanlybaohong') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.5 19h11a2.5 2.5 0 0 0 2.5-2.5V6.5A2.5 2.5 0 0 0 17.5 4h-11A2.5 2.5 0 0 0 4 6.5v10A2.5 2.5 0 0 0 6.5 19z"/>
                </svg>
                Repair
            </a>
            <a href="{{ route('admin.quanlysinhvien') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('admin.quanlysinhvien') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A4 4 0 0 1 8 13h8a4 4 0 0 1 2.879 4.804M15 11a3 3 0 1 0-6 0 3 3 0 0 0 6 0z"/>
                </svg>
                Profile
            </a>
        </div>
    </nav>
</div>
@stack('modals')
</body>
</html>
