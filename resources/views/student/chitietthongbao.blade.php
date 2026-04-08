@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <a href="{{ route('student.trangchu') }}" class="text-sm text-[#606060] hover:underline">&larr; Quay về</a>
        <div class="mt-2 text-2xl font-bold text-[#121212]">Chi tiết thông báo</div>
        <div class="text-sm text-[#606060]">{{ $thongbao->tieude }}</div>
    </div>

    <div class="rounded-lg border border-gray-200/70 bg-white p-6">
        <div class="mb-3 text-sm text-[#606060]">Ngày đăng: {{ $thongbao->ngaydang }}</div>
        <div class="text-lg text-[#606060]">{{ $thongbao->noidung }}</div>
    </div>
@endsection
