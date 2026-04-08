@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <a href="{{ route('student.thongbao') }}" class="mb-2 inline-flex items-center text-sm text-[#606060] hover:text-[#121212]">
            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Quay lại danh sách
        </a>
        <div class="text-2xl font-bold text-[#121212]">{{ $thongbao->tieude }}</div>
        <div class="text-sm text-[#606060]">Đăng ngày {{ date('d/m/Y H:i', strtotime($thongbao->ngaydang)) }}</div>
    </div>

    {{-- Nội dung --}}
    <div class="rounded-xl border border-gray-200/70 bg-white p-6">
        <div class="prose max-w-none text-[#606060]">
            {!! nl2br(e($thongbao->noidung)) !!}
        </div>
    </div>

    {{-- Thông báo liên quan --}}
    @if($thongbaoLienQuan->count() > 0)
        <div class="mt-6">
            <h3 class="mb-4 text-lg font-semibold text-[#121212]">Thông báo liên quan</h3>
            <div class="space-y-3">
                @foreach($thongbaoLienQuan as $tb)
                    <a href="{{ route('student.chitietthongbao', $tb->id) }}" class="block rounded-lg border border-gray-200/70 bg-white p-4 hover:shadow-sm transition-shadow">
                        <div class="mb-1 flex items-center justify-between">
                            <span class="font-medium text-[#121212]">{{ $tb->tieude }}</span>
                            <span class="text-xs text-[#606060]">{{ date('d/m/Y', strtotime($tb->ngaydang)) }}</span>
                        </div>
                        <p class="line-clamp-2 text-sm text-[#606060]">{{ $tb->noidung }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
