<header class="sticky top-0 z-30 border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
        <a href="{{ route('student.trangchu') }}" class="text-sm font-semibold text-gray-900">
            {{ config('app.name', 'hethongquanlyktx') }}
        </a>

        <nav class="hidden items-center gap-2 sm:flex">
            <a href="{{ route('student.danhsachphong') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('student.danhsachphong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                Phòng trống
            </a>
            <a href="{{ route('student.hoadoncuaem') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('student.hoadoncuaem') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                Hóa đơn
            </a>
            <a href="{{ route('student.danhsachbaohong') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('student.danhsachbaohong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                Báo hỏng
            </a>
            <a href="{{ route('student.taisanphong') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('student.taisanphong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                Tài sản phòng
            </a>
            <a href="{{ route('student.kyluatcuaem') }}"
               class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('student.kyluatcuaem') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                Lịch sử kỷ luật
            </a>
        </nav>

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
