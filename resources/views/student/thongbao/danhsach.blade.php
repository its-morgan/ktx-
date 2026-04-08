@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Thông báo</div>
        <div class="text-sm text-[#606060]">Cập nhật thông tin mới nhất từ hệ thống.</div>
    </div>

    {{-- Thống kê --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200/70 bg-white p-4">
            <div class="text-2xl font-bold text-[#121212]">{{ $thongKe['tong_so'] }}</div>
            <div class="text-xs text-[#606060]">Tổng số thông báo</div>
        </div>
        <div class="rounded-xl border border-gray-200/70 bg-white p-4">
            <div class="text-2xl font-bold text-[#121212]">{{ $thongKe['trong_thang'] }}</div>
            <div class="text-xs text-[#606060]">Trong tháng này</div>
        </div>
        <div class="rounded-xl border border-gray-200/70 bg-white p-4">
            <div class="text-2xl font-bold text-[#121212]">{{ $thongKe['tuan_nay'] }}</div>
            <div class="text-xs text-[#606060]">Tuần này</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="mb-4 flex gap-2">
        <a href="{{ route('student.thongbao') }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ $loai === 'tatca' ? 'bg-gray-900 text-white' : 'bg-white text-[#606060] border border-gray-200/70' }}">
            Tất cả
        </a>
        <a href="{{ route('student.thongbao', ['loai' => 'moi_nhat']) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ $loai === 'moi_nhat' ? 'bg-gray-900 text-white' : 'bg-white text-[#606060] border border-gray-200/70' }}">
            Mới nhất (7 ngày)
        </a>
    </div>

    {{-- Danh sách --}}
    <div class="space-y-3">
        @forelse($thongbao as $tb)
            <a href="{{ route('student.chitietthongbao', $tb->id) }}" class="block rounded-xl border border-gray-200/70 bg-white p-4 hover:shadow-sm transition-shadow">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <h3 class="truncate text-base font-semibold text-[#121212]">{{ $tb->tieude }}</h3>
                            <span class="shrink-0 text-xs text-[#606060]">{{ date('d/m/Y', strtotime($tb->ngaydang)) }}</span>
                        </div>
                        <p class="line-clamp-2 text-sm text-[#606060]">{{ $tb->noidung }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-xl border border-gray-200/70 bg-white p-8 text-center">
                <svg class="mx-auto mb-4 h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="mb-1 text-lg font-semibold text-[#121212]">Chưa có thông báo</h3>
                <p class="text-sm text-[#606060]">Các thông báo mới sẽ hiển thị tại đây.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $thongbao->links() }}
    </div>
@endsection
