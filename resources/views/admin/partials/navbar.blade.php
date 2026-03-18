<header class="sticky top-0 z-30 border-b border-gray-200 bg-white">
    <div class="flex items-center justify-between px-4 py-3 sm:px-6">
        <div class="text-sm font-semibold text-gray-900">
            {{ config('app.name', 'hethongquanlyktx') }} - Admin
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}"
               class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Hồ sơ
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</header>
