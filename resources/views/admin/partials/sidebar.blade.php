<aside class="fixed left-0 top-0 z-40 hidden h-screen w-64 border-r border-gray-200 bg-white lg:block">
    <div class="flex h-full flex-col">
        <div class="border-b border-gray-200 px-6 py-5">
            <div class="text-lg font-bold text-gray-900">KTX</div>
            <div class="text-xs text-gray-500">Khu vực quản trị</div>
        </div>

        <nav class="flex-1 space-y-2 px-3 py-4 text-sm">
            <a href="{{ route('admin.trangchu') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.trangchu') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 1.707a1 1 0 0 0-1.414 0l-9 9A1 1 0 0 0 0 12h2v6a1 1 0 0 0 1 1h4v-4h2v4h4a1 1 0 0 0 1-1v-6h2a1 1 0 0 0 .707-1.707l-9-9z"/></svg>
                Trang chủ
            </a>

            <div class="pt-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Quản lý</div>

            <a href="{{ route('admin.quanlyphong') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlyphong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                Quản lý phòng
            </a>

            <a href="{{ route('admin.quanlysinhvien') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlysinhvien') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 0 1 8 13h8a4 4 0 0 1 2.879 4.804M15 11a3 3 0 1 0-6 0 3 3 0 0 0 6 0z"/></svg>
                Quản lý sinh viên
            </a>

            <a href="{{ route('admin.duyetdangky') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.duyetdangky') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6"/></svg>
                Duyệt đăng ký
            </a>

            <a href="{{ route('admin.quanlyhoadon') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlyhoadon') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6m-6-2h6m-6-2h6m2 10h2a2 2 0 0 0 2-2V7m-6 2h-5V5h5m2 0H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h2"/></svg>
                Quản lý hóa đơn
            </a>

            <a href="{{ route('admin.quanlybaohong') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlybaohong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12H5.5a1.5 1.5 0 0 1-1.5-1.5V8m0-2a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H8.5a1.5 1.5 0 0 1-1.5-1.5V15"/></svg>
                Quản lý báo hỏng
            </a>

            <a href="{{ route('admin.quanlykyluat') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlykyluat') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0l-3.5 3.5M21 7l-3.5-3.5M3 10h4m2-2v6m0 0h4m0-6v6m0 0h4"/></svg>
                Quản lý kỷ luật
            </a>

            <a href="{{ route('admin.quanlycauhinh') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlycauhinh') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.104-.896 2-2 2s-2-.896-2-2 .896-2 2-2 2 .896 2 2zm0-6c0 1.104-.896 2-2 2s-2-.896-2-2 .896-2 2-2 2 .896 2 2zM22 12.5v3a2 2 0 0 1-2 2h-4.5M13 14.5v3a2 2 0 0 1-2 2H8m12.5-8.5l-2.5-2.5m0 0L13 13m5.5-5.5L14 9"/></svg>
                Quản lý giá điện nước
            </a>

            <a href="{{ route('admin.quanlyhopdong') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlyhopdong') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m3-13H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/></svg>
                Quản lý hợp đồng
            </a>

            <a href="{{ route('admin.quanlythongbao') }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.quanlythongbao') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }} transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4-1h.01M4 12a8 8 0 1 1 16 0 8 8 0 0 1-16 0z"/></svg>
                Quản lý thông báo
            </a>
        </nav>

        <div class="border-t border-gray-200 px-4 py-4 text-sm">
            <div class="text-gray-900 font-semibold">{{ auth()->user()->name }}</div>
            <div class="text-gray-500">admin</div>
        </div>
    </div>
</aside>
