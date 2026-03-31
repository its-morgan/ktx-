<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'hethongquanlyktx') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="min-h-screen">
    @include('student.partials.navbar')

    <main class="mx-auto max-w-6xl p-4 sm:p-6">
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <div class="font-semibold">Có lỗi xảy ra:</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $loi)
                        <li>{{ $loi }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('noidung')
    </main>

    @include('student.partials.toast')

    <!-- Bottom navigation cho di động -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-gray-200 bg-white p-2 shadow-inner lg:hidden">
        <div class="mx-auto flex w-full max-w-lg items-center justify-between">
            <a href="{{ route('student.trangchu') }}" class="flex flex-col items-center text-xs text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9z"/></svg>
                Home
            </a>
            <a href="{{ route('student.hoadoncuaem') }}" class="flex flex-col items-center text-xs text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m0 0V9a2 2 0 1 1 4 0v2m0 2v6M5 11h14" /></svg>
                Bill
            </a>
            <a href="{{ route('student.danhsachbaohong') }}" class="flex flex-col items-center text-xs text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.5 19h11a2.5 2.5 0 0 0 2.5-2.5V6.5A2.5 2.5 0 0 0 17.5 4h-11A2.5 2.5 0 0 0 4 6.5v10A2.5 2.5 0 0 0 6.5 19z"/></svg>
                Repair
            </a>
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center text-xs text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 0 1 8 13h8a4 4 0 0 1 2.879 4.804M15 11a3 3 0 1 0-6 0 3 3 0 0 0 6 0z"/></svg>
                Profile
            </a>
        </div>
    </nav>
</div>
</body>
</html>
