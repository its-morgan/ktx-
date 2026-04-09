<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
        <div class="flex items-center gap-2 text-sm">
            <div class="font-display text-lg font-semibold text-slate-900">KTX Admin</div>
            <span class="text-slate-300">|</span>
            <div class="text-slate-600">@yield('admin_page_title', 'Dashboard tổng quan')</div>
        </div>

        <div class="flex items-center gap-2">
            <form class="hidden lg:block">
                <label class="relative block">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                        </svg>
                    </span>
                    <input type="search" class="linear-input w-56 pl-9" placeholder="Tìm kiếm" />
                </label>
            </form>

            <a href="{{ route('admin.quanlythongbao') }}" class="linear-btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0"/>
                </svg>
                Thông báo
            </a>

            <a href="{{ route('profile.edit') }}" class="hidden items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:flex">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-orange-100 text-xs font-semibold text-orange-700">BQ</span>
                Ban quản lý
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="linear-btn-primary" data-loading-text="Đang đăng xuất...">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</header>
