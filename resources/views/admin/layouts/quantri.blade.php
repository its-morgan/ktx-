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
    {{-- Sidebar chỉ dành cho admin --}}
    @include('admin.partials.sidebar')

    <div class="lg:pl-64">
        @include('admin.partials.navbar')

        <main class="p-4 sm:p-6">
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
    </div>

    @include('admin.partials.toast')
</div>
</body>
</html>
