@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Trang chủ sinh viên</div>
        <div class="text-sm text-gray-500">Chọn chức năng bên trên để sử dụng.</div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="{{ route('student.danhsachphong') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:bg-gray-50">
            <div class="text-sm text-gray-500">Xem phòng trống</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Đăng ký phòng</div>
        </a>

        <a href="{{ route('student.hoadoncuaem') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:bg-gray-50">
            <div class="text-sm text-gray-500">Xem hóa đơn</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Hóa đơn hàng tháng</div>
        </a>

        <a href="{{ route('student.danhsachbaohong') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:bg-gray-50">
            <div class="text-sm text-gray-500">Gửi yêu cầu</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Báo hỏng</div>
        </a>
    </div>
@endsection

