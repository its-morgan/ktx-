<aside class="fixed left-0 top-0 z-40 hidden h-screen w-64 border-r border-gray-200 bg-white lg:block">
    <div class="flex h-full flex-col">
        <div class="border-b border-gray-200 px-6 py-5">
            <div class="text-lg font-bold text-gray-900">KTX</div>
            <div class="text-xs text-gray-500">Khu vực quản trị</div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4 text-sm">
            <a href="{{ route('admin.trangchu') }}"
               class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.trangchu') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}">
                Trang chủ
            </a>

            <div class="pt-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Quản lý</div>

            <a href="{{ route('admin.quanlyphong') }}"
               class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlyphong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}">
                Quản lý phòng
            </a>

            <a href="{{ route('admin.quanlysinhvien') }}"
               class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlysinhvien') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}">
                Quản lý sinh viên
            </a>

            <a href="{{ route('admin.duyetdangky') }}"
               class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.duyetdangky') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}">
                Duyệt đăng ký
            </a>

            <a href="{{ route('admin.quanlyhoadon') }}"
               class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlyhoadon') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}">
                Quản lý hóa đơn
            </a>

            <a href="{{ route('admin.quanlybaohong') }}"
               class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlybaohong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}">
                Quản lý báo hỏng
            </a>
        </nav>

        <div class="border-t border-gray-200 px-4 py-4 text-sm">
            <div class="text-gray-900 font-semibold">{{ auth()->user()->name }}</div>
            <div class="text-gray-500">admin</div>
        </div>
    </div>
</aside>
