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
<body class="portal-shell transition-colors duration-300">
@php
    $hoadonCanXuLy = isset($hoadonchuathanhtoan) && method_exists($hoadonchuathanhtoan, 'count') ? $hoadonchuathanhtoan->count() : 0;
    $hotroCanXuLy = $hoadonCanXuLy > 0 ? 1 : 0;
    $tenSinhVien = auth()->user()->name ?? 'Sinh viên';
    $mssv = isset($sinhvien) && !empty($sinhvien?->mssv) ? $sinhvien->mssv : ('SV' . str_pad((string) (auth()->id() ?? 0), 6, '0', STR_PAD_LEFT));
@endphp

<div class="portal-shell">
    <aside class="portal-sidebar">
        <div class="flex h-full flex-col">
            <div class="border-b border-slate-200 px-4 py-4">
                <div class="font-display text-xl font-semibold text-slate-900">KTX Portal</div>
                <div class="text-xs text-slate-500">Sinh viên</div>
            </div>

            <div class="px-4 py-4">
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 font-semibold text-slate-800">
                        {{ strtoupper(substr($tenSinhVien, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-slate-900">{{ $tenSinhVien }}</div>
                        <div class="text-xs text-slate-500">{{ $mssv }}</div>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto pb-6">
                <div class="portal-sidebar-section">Tổng quan</div>
                <div class="px-2">
                    <a href="{{ route('student.trangchu') }}" class="portal-link {{ request()->routeIs('student.trangchu') ? 'portal-link-active' : '' }}">
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="portal-link {{ request()->routeIs('profile.edit') ? 'portal-link-active' : '' }}">
                        <span>Hồ sơ cá nhân</span>
                    </a>
                </div>

                <div class="portal-sidebar-section">Phòng ở</div>
                <div class="px-2">
                    <a href="{{ route('student.danhsachphong') }}" class="portal-link {{ request()->routeIs('student.danhsachphong') ? 'portal-link-active' : '' }}">
                        <span>Đăng ký phòng</span>
                    </a>
                    <a href="{{ route('student.hopdongcuatoi') }}" class="portal-link {{ request()->routeIs('student.hopdongcuatoi') ? 'portal-link-active' : '' }}">
                        <span>Hợp đồng</span>
                    </a>
                    <a href="{{ route('student.phongcuatoi') }}" class="portal-link {{ request()->routeIs('student.phongcuatoi*') ? 'portal-link-active' : '' }}">
                        <span>Gia hạn / Trả phòng</span>
                    </a>
                </div>

                <div class="portal-sidebar-section">Tài chính</div>
                <div class="px-2">
                    <a href="{{ route('student.hoadoncuaem') }}" class="portal-link {{ request()->routeIs('student.hoadoncuaem', 'student.phongcuatoi.hoadon.chitiet') ? 'portal-link-active' : '' }}">
                        <span>Hóa đơn</span>
                        @if ($hoadonCanXuLy > 0)
                            <span class="portal-badge-danger">{{ $hoadonCanXuLy }}</span>
                        @endif
                    </a>
                    <a href="{{ route('student.hoadoncuaem') }}#thanh-toan-online" class="portal-link">
                        <span>Thanh toán</span>
                    </a>
                    <a href="{{ route('student.hoadoncuaem') }}" class="portal-link {{ request()->routeIs('student.hoadoncuaem', 'student.phongcuatoi.hoadon.chitiet') ? 'portal-link-active' : '' }}">
                        <span>Lịch sử</span>
                    </a>
                </div>

                <div class="portal-sidebar-section">Hỗ trợ</div>
                <div class="px-2">
                    <a href="{{ route('student.danhsachbaohong') }}" class="portal-link {{ request()->routeIs('student.danhsachbaohong') ? 'portal-link-active' : '' }}">
                        <span>Yêu cầu sửa chữa</span>
                        @if ($hotroCanXuLy > 0)
                            <span class="portal-badge-danger">{{ $hotroCanXuLy }}</span>
                        @endif
                    </a>
                    <a href="{{ route('student.trangchu') }}#module-dichvu" class="portal-link">
                        <span>Đăng ký dịch vụ</span>
                    </a>
                    <a href="{{ route('student.thongbao') }}" class="portal-link {{ request()->routeIs('student.thongbao*', 'student.chitietthongbao') ? 'portal-link-active' : '' }}">
                        <span>Thông báo</span>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <div class="portal-main">
        <header class="portal-topbar">
            <div class="flex items-center justify-between px-4 py-4 sm:px-6">
                <div>
                    <div class="portal-title">@yield('student_page_title', 'Dashboard')</div>
                    <div class="portal-subtitle">Không gian quản lý toàn bộ quá trình ở tại ký túc xá</div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('student.thongbao') }}" class="portal-card-soft p-2 text-slate-500 hover:text-slate-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="linear-btn-primary">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="px-4 pb-24 pt-5 sm:px-6 lg:pb-6">
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
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

    @include('student.partials.toast')

    <nav class="linear-navbar-glass fixed bottom-0 left-0 right-0 z-50 border-t p-2 lg:hidden">
        <div class="mx-auto flex w-full max-w-lg items-center justify-between">
            <a href="{{ route('student.trangchu') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('student.trangchu') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9z"/>
                </svg>
                Home
            </a>
            <a href="{{ route('student.hoadoncuaem') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('student.hoadoncuaem', 'student.phongcuatoi.hoadon.chitiet') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-6m0 0V9a2 2 0 1 1 4 0v2m0 2v6M5 11h14" />
                </svg>
                Bill
            </a>
            <a href="{{ route('student.danhsachbaohong') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('student.danhsachbaohong') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.5 19h11a2.5 2.5 0 0 0 2.5-2.5V6.5A2.5 2.5 0 0 0 17.5 4h-11A2.5 2.5 0 0 0 4 6.5v10A2.5 2.5 0 0 0 6.5 19z"/>
                </svg>
                Repair
            </a>
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-0.5 rounded-md px-3 py-1 text-xs {{ request()->routeIs('profile.edit') ? 'text-slate-900' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A4 4 0 0 1 8 13h8a4 4 0 0 1 2.879 4.804M15 11a3 3 0 1 0-6 0 3 3 0 0 0 6 0z"/>
                </svg>
                Profile
            </a>
        </div>
    </nav>
</div>
</body>
</html>
