@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Trang chủ sinh viên</div>
        <div class="text-sm text-gray-500">Chọn chức năng bên trên để sử dụng.</div>
    </div>

    @if ($kyluatcuaem->isNotEmpty())
        <div class="mb-6 rounded-xl border border-red-300 bg-red-50 p-4">
            <div class="text-red-700 font-semibold">Cảnh báo kỷ luật</div>
            <div class="text-sm text-red-600">Bạn có {{ $kyluatcuaem->count() }} vi phạm gần đây, hãy liên hệ ban quản lý để xử lý.</div>
            <ul class="mt-2 list-disc pl-5 text-sm text-red-700">
                @foreach ($kyluatcuaem as $item)
                    <li>{{ $item->ngayvipham }} - {{ $item->noidung }} (Mức độ: {{ $item->mucdo }})</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
        <div class="text-sm text-gray-500">Liên hệ khẩn cấp</div>
        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
            @foreach ($lienhekhancap as $lh)
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                    <div class="font-semibold text-gray-900">{{ $lh['title'] }}</div>
                    <div class="text-sm text-gray-600">{{ $lh['phone'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    @if (!empty($sinhvien?->phong_id))
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
            <div class="text-sm text-gray-500">Danh sách cùng phòng</div>
            @if ($thanhviencungphong->isEmpty())
                <div class="mt-2 text-sm text-gray-400">Chưa có thành viên khác.</div>
            @else
                <div class="mt-2 space-y-2">
                    @foreach ($thanhviencungphong as $tv)
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-2">
                            {{ optional($tv->taikhoan)->name ?? 'N/A' }} - Lớp: {{ $tv->lop ?? 'N/A' }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if ($hoadonchuathanhtoan->isNotEmpty())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 animate-pulse">
            <div class="font-semibold text-red-700">Bạn có {{ $hoadonchuathanhtoan->count() }} hóa đơn chưa thanh toán!</div>
            <ul class="mt-2 text-sm text-red-700">
                @foreach ($hoadonchuathanhtoan as $hoadon)
                    <li>Phòng {{ optional($hoadon->phong)->tenphong ?? 'N/A' }} tháng {{ $hoadon->thang }}/{{ $hoadon->nam }}: {{ number_format($hoadon->tongtien) }} đ</li>
                @endforeach
            </ul>
        </div>
    @endif

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

        <form method="POST" action="{{ route('student.yeucautraphong') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:bg-gray-50">
            @csrf
            <div class="text-sm text-gray-500">Thông tin</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Yêu cầu trả phòng</div>
            <button type="submit" class="mt-4 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                Gửi yêu cầu trả phòng
            </button>
        </form>
    </div>
@endsection

