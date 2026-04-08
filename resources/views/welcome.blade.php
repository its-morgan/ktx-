<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'hethongquanlyktx') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="linear-shell">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_#F7F7F8,_#FFFFFF_58%)]"></div>

            <div class="relative mx-auto flex min-h-screen w-full max-w-5xl flex-col justify-center px-4 py-10 sm:px-6">
                <div class="mb-10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <x-application-logo class="h-12" />
                        <div>
                            <div class="text-sm font-semibold tracking-tight text-[#121212]">KTX Workspace</div>
                            <div class="text-xs text-[#606060]">Hệ thống quản lý ký túc xá</div>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-2">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="linear-btn-primary">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="linear-btn-secondary">Đăng nhập</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="linear-btn-primary">Đăng ký</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="linear-card">
                        <div class="text-xs uppercase tracking-wide text-[#606060]">Quản trị</div>
                        <h2 class="mt-2 text-lg font-semibold text-[#121212]">Dashboard Admin</h2>
                        <p class="mt-2 text-sm text-[#606060]">Quản lý phòng, sinh viên, hóa đơn, báo hỏng và hợp đồng trong một giao diện thống nhất.</p>
                    </div>

                    <div class="linear-card">
                        <div class="text-xs uppercase tracking-wide text-[#606060]">Sinh viên</div>
                        <h2 class="mt-2 text-lg font-semibold text-[#121212]">Tự phục vụ</h2>
                        <p class="mt-2 text-sm text-[#606060]">Đăng ký phòng, theo dõi hóa đơn, gửi báo hỏng và cập nhật thông tin cá nhân nhanh chóng.</p>
                    </div>

                    <div class="linear-card">
                        <div class="text-xs uppercase tracking-wide text-[#606060]">Thông báo</div>
                        <h2 class="mt-2 text-lg font-semibold text-[#121212]">Cập nhật tức thời</h2>
                        <p class="mt-2 text-sm text-[#606060]">Hệ thống thông báo tập trung giúp truyền tải thay đổi quan trọng đến đúng đối tượng.</p>
                    </div>
                </div>

                <div class="mt-8 text-center text-sm text-[#606060]">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </div>
    </body>
</html>
