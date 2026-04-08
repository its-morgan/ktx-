@php
    $itemClass = 'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300';
@endphp

<aside class="linear-sidebar-glass fixed left-0 top-0 z-40 hidden h-screen w-64 border-r lg:block">
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-200/80 px-5 py-4">
            <div class="font-display text-base font-semibold tracking-tight text-slate-900">KTX Workspace</div>
            <div class="mt-1 text-xs text-slate-500">Khu vực quản trị</div>
        </div>

        <nav class="flex-1 space-y-1.5 px-3 py-4">
            <a href="{{ route('admin.trangchu') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.trangchu') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9z"/>
                </svg>
                Trang chủ
            </a>

            <div class="px-2 pt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Quản lý</div>

            <a href="{{ route('admin.quanlyphong') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlyphong') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Quản lý phòng
            </a>

            <a href="{{ route('admin.quanlysinhvien') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlysinhvien') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 14a4 4 0 1 0-8 0m8 0H8m8 0a4 4 0 0 1 4 4v1H4v-1a4 4 0 0 1 4-4"/>
                </svg>
                Quản lý sinh viên
            </a>

            <a href="{{ route('admin.duyetdangky') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.duyetdangky') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-3-3v6M4 6h16v12H4z"/>
                </svg>
                Duyệt đăng ký
            </a>

            <a href="{{ route('admin.quanlyhoadon') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlyhoadon') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8m-8 4h8m-8 4h5M5 4h14v16H5z"/>
                </svg>
                Quản lý hóa đơn
            </a>

            <a href="{{ route('admin.quanlybaohong') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlybaohong') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 14L21 3m-5 0h5v5M3 10v11h11"/>
                </svg>
                Quản lý báo hỏng
            </a>

            <a href="{{ route('admin.quanlykyluat') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlykyluat') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18m9-9H3"/>
                </svg>
                Quản lý kỷ luật
            </a>

            <a href="{{ route('admin.quanlycauhinh') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlycauhinh') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 12h6m4 0h6M8 8v8m8-11v14"/>
                </svg>
                Giá điện nước
            </a>

            <a href="{{ route('admin.quanlyhopdong') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlyhopdong') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 4h10v16H7zM7 8h10"/>
                </svg>
                Quản lý hợp đồng
            </a>

            <a href="{{ route('admin.quanlythongbao') }}"
               class="{{ $itemClass }} {{ request()->routeIs('admin.quanlythongbao') ? 'border border-slate-200/80 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:-translate-y-0.5 hover:bg-white/85 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0"/>
                </svg>
                Quản lý thông báo
            </a>
        </nav>

        <div class="border-t border-slate-200/80 px-4 py-4">
            <div class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-500">admin</div>
        </div>
    </div>
</aside>

