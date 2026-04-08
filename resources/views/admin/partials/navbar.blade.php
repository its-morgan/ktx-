<header class="linear-navbar-glass sticky top-0 z-30 border-b">
    <div class="flex items-center justify-between px-4 py-3 sm:px-6">
        <div>
            <div class="font-display text-sm font-semibold tracking-tight text-slate-900">
                {{ config('app.name', 'hethongquanlyktx') }} / Admin
            </div>
            <div class="text-xs text-slate-500">Quản trị hệ thống ký túc xá</div>
        </div>

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
