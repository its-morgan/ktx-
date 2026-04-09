@php
    $itemBase = 'group mt-1.5 flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200';
    $itemDefault = 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
    $itemActive = 'bg-white text-slate-900 ring-1 ring-slate-200 shadow-soft';

    $soDonChoDuyet = isset($dangkychoxuly) ? (int) $dangkychoxuly : null;
    $soSuCoMo = isset($baohongchosua) ? (int) $baohongchosua : null;
    $soCongNo = isset($hoadonchuathanhtoan) ? (int) $hoadonchuathanhtoan : null;

    $tenNguoiDung = auth()->user()->name ?? 'Ban quản lý';
    $chuCaiDau = strtoupper(substr($tenNguoiDung, 0, 1));
@endphp

<aside class="fixed left-0 top-0 z-40 hidden h-screen w-64 border-r border-slate-200 bg-[#fbfbfc] lg:block">
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="font-display text-xl font-semibold text-slate-900">KTX Admin</div>
            <div class="mt-1 text-xs text-slate-500">Dashboard tổng quan</div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <div class="px-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Tổng quan</div>

            <a href="{{ route('admin.trangchu') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.trangchu') ? $itemActive : $itemDefault }}">
                <span>Dashboard</span>
            </a>

            <div class="mt-4 px-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Quản lý</div>

            <a href="{{ route('admin.quanlyphong') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlyphong', 'admin.chitietphong') ? $itemActive : $itemDefault }}">
                <span>Phòng ở</span>
            </a>

            <a href="{{ route('admin.quanlysinhvien') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlysinhvien') ? $itemActive : $itemDefault }}">
                <span>Sinh viên</span>
            </a>

            <a href="{{ route('admin.duyetdangky') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.duyetdangky') ? $itemActive : $itemDefault }}">
                <span>Đơn đăng ký</span>
                @if (!is_null($soDonChoDuyet) && $soDonChoDuyet > 0)
                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">{{ $soDonChoDuyet }}</span>
                @endif
            </a>

            <a href="{{ route('admin.quanlyhopdong') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlyhopdong', 'admin.hopdong.*') ? $itemActive : $itemDefault }}">
                <span>Hợp đồng</span>
            </a>

            <div class="mt-4 px-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Tài chính</div>

            <a href="{{ route('admin.quanlyhoadon') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlyhoadon', 'admin.hoadon.*') ? $itemActive : $itemDefault }}">
                <span>Hóa đơn</span>
            </a>

            <a href="{{ route('admin.baocaocongno') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.baocaocongno') ? $itemActive : $itemDefault }}">
                <span>Công nợ</span>
                @if (!is_null($soCongNo) && $soCongNo > 0)
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">{{ $soCongNo }}</span>
                @endif
            </a>

            <div class="mt-4 px-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Vận hành</div>

            <a href="{{ route('admin.quanlybaohong') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlybaohong') ? $itemActive : $itemDefault }}">
                <span>Sự cố</span>
                @if (!is_null($soSuCoMo) && $soSuCoMo > 0)
                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700">{{ $soSuCoMo }}</span>
                @endif
            </a>

            <a href="{{ route('admin.quanlybaotri') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlybaotri') ? $itemActive : $itemDefault }}">
                <span>Bảo trì</span>
            </a>

            <a href="{{ route('admin.quanlykyluat') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlykyluat') ? $itemActive : $itemDefault }}">
                <span>Vi phạm</span>
            </a>

            <div class="mt-4 px-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Hệ thống</div>

            <a href="{{ route('admin.quanlycauhinh') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlycauhinh') ? $itemActive : $itemDefault }}">
                <span>Người dùng</span>
            </a>

            <a href="{{ route('admin.quanlylienhe') }}"
               class="{{ $itemBase }} {{ request()->routeIs('admin.quanlylienhe') ? $itemActive : $itemDefault }}">
                <span>Liên hệ</span>
            </a>

            <a href="javascript:void(0)"
               class="{{ $itemBase }} text-slate-400 cursor-not-allowed">
                <span>Phân quyền (Sắp triển khai)</span>
            </a>

            <a href="javascript:void(0)"
               class="{{ $itemBase }} text-slate-400 cursor-not-allowed">
                <span>Nhật ký (Sắp triển khai)</span>
            </a>
        </nav>

        <div class="border-t border-slate-200 px-4 py-4">
            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">
                    {{ $chuCaiDau }}
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-slate-900">{{ $tenNguoiDung }}</div>
                    <div class="text-xs text-slate-500">Ban quản lý</div>
                </div>
            </div>
        </div>
    </div>
</aside>
