<header class="linear-navbar-glass sticky top-0 z-30 border-b">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
        <a href="{{ route('student.trangchu') }}" class="font-display text-sm font-semibold tracking-tight text-slate-900">
            {{ config('app.name', 'hethongquanlyktx') }}
        </a>

        <nav class="hidden items-center gap-1 sm:flex">
            <a href="{{ route('student.phongcuatoi') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 {{ request()->routeIs('student.phongcuatoi*') ? 'border border-slate-200 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:bg-white hover:text-slate-900' }}">
                Phòng của tôi
            </a>
            <a href="{{ route('student.danhsachphong') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 {{ request()->routeIs('student.danhsachphong') ? 'border border-slate-200 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:bg-white hover:text-slate-900' }}">
                Phòng trống
            </a>
            <a href="{{ route('student.hoadoncuaem') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 {{ request()->routeIs('student.hoadoncuaem') ? 'border border-slate-200 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:bg-white hover:text-slate-900' }}">
                Hóa đơn
            </a>
            <a href="{{ route('student.danhsachbaohong') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 {{ request()->routeIs('student.danhsachbaohong') ? 'border border-slate-200 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:bg-white hover:text-slate-900' }}">
                Báo hỏng
            </a>
            <a href="{{ route('student.taisanphong') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 {{ request()->routeIs('student.taisanphong') ? 'border border-slate-200 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:bg-white hover:text-slate-900' }}">
                Tài sản phòng
            </a>
            <a href="{{ route('student.thongbao') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 {{ request()->routeIs('student.thongbao*') ? 'border border-slate-200 bg-white text-slate-900 shadow-soft' : 'text-slate-500 hover:bg-white hover:text-slate-900' }}">
                Thông báo
            </a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('profile.edit') }}" class="linear-btn-secondary">
                Hồ sơ
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
