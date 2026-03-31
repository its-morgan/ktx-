@extends('student.layouts.chinh')

@section('noidung')
    @php
        $tongTienCanDong = (int) $hoadonchuathanhtoan->sum('tongtien');
        $ngayConLaiHopDong = null;

        if (!empty($sinhvien?->ngay_het_han)) {
            $ngayConLaiHopDong = now()->startOfDay()->diffInDays(
                \Illuminate\Support\Carbon::parse($sinhvien->ngay_het_han)->startOfDay(),
                false
            );
        }
    @endphp

    <div class="mb-6 space-y-4">
        <div>
            <div class="text-2xl font-bold text-gray-900">Trang chủ sinh viên</div>
            <div class="text-sm text-gray-500">Tổng quan nhanh thông tin phòng, hóa đơn và thông báo.</div>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div class="rounded-2xl border border-rose-100 bg-rose-50/80 p-3 shadow-sm">
                <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 9v1m8-5a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                </div>
                <div class="text-[11px] font-medium text-rose-700">Cần đóng</div>
                <div class="mt-1 text-sm font-semibold text-rose-900">
                    @if ($tongTienCanDong > 0)
                        {{ number_format($tongTienCanDong) }} đ
                    @else
                        0 đ
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-amber-100 bg-amber-50/80 p-3 shadow-sm">
                <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                    </svg>
                </div>
                <div class="text-[11px] font-medium text-amber-700">Còn lại HĐ</div>
                <div class="mt-1 text-sm font-semibold text-amber-900">
                    @if (is_null($ngayConLaiHopDong))
                        Chưa có
                    @elseif ($ngayConLaiHopDong < 0)
                        Hết hạn
                    @else
                        {{ $ngayConLaiHopDong }} ngày
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/80 p-3 shadow-sm">
                <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12c0 4.971-4.029 9-9 9s-9-4.029-9-9 4.029-9 9-9 9 4.029 9 9z" />
                    </svg>
                </div>
                <div class="text-[11px] font-medium text-indigo-700">Vi phạm</div>
                <div class="mt-1 text-sm font-semibold text-indigo-900">{{ $kyluatcuaem->count() }}</div>
            </div>
        </div>
    </div>

    @if ($kyluatcuaem->isNotEmpty())
        <div class="mb-6 rounded-2xl border border-red-300 bg-red-50 p-4">
            <div class="font-semibold text-red-700">Cảnh báo kỷ luật</div>
            <div class="text-sm text-red-600">Bạn có {{ $kyluatcuaem->count() }} vi phạm gần đây, vui lòng liên hệ ban quản lý để xử lý.</div>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                @foreach ($kyluatcuaem as $item)
                    <li>{{ $item->ngayvipham }} - {{ $item->noidung }} (Mức độ: {{ $item->mucdo }})</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (empty($sinhvien?->phong_id))
        <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-5 text-blue-800">
            <div class="text-lg font-semibold">Bạn chưa được xếp phòng</div>
            <div class="text-sm">Hãy vào mục Phòng trống để đăng ký nhé.</div>
        </div>
    @else
        @php
            $sodangtrenphong = $thanhviencungphong->count() + 1;
            $soluongtoida = $phonghientai->soluongtoida ?? 0;
        @endphp

        <div class="mb-6 rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="text-lg font-semibold text-indigo-900">Phòng của tôi</div>
                    <div class="text-sm text-indigo-600">Thông tin phòng, thành viên và tài sản hiện có.</div>
                </div>
                <div class="rounded-xl border border-white/70 bg-white/80 px-3 py-2 text-sm font-medium text-indigo-700">
                    {{ $sodangtrenphong }} / {{ $soluongtoida }} thành viên
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-indigo-100 bg-white/90 p-3">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Tên phòng</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">{{ $phonghientai->tenphong ?? '-' }}</div>
                </div>
                <div class="rounded-2xl border border-indigo-100 bg-white/90 p-3">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Giới tính</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">
                        @if (optional($phonghientai)->gioitinh === 'Nữ')
                            Nữ
                        @else
                            Nam
                        @endif
                    </div>
                </div>
                <div class="rounded-2xl border border-indigo-100 bg-white/90 p-3">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Sức chứa</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">{{ $sodangtrenphong }} / {{ $soluongtoida }}</div>
                </div>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-indigo-100 bg-white/90 p-4">
                    <div class="text-sm font-semibold text-gray-800">Bạn cùng phòng</div>
                    @if ($thanhviencungphong->isEmpty())
                        <div class="mt-2 text-sm text-gray-500">Chưa có thành viên khác.</div>
                    @else
                        <div class="mt-3 flex items-center -space-x-2 overflow-x-auto pb-1">
                            @foreach ($thanhviencungphong as $tv)
                                @php
                                    $tenThanhVien = optional($tv->taikhoan)->name ?? 'N/A';
                                    $chuCaiDau = strtoupper(substr($tenThanhVien, 0, 1));
                                @endphp
                                <div title="{{ $tenThanhVien }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-white bg-indigo-500 text-sm font-semibold text-white">
                                    {{ $chuCaiDau }}
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($thanhviencungphong as $tv)
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">
                                    {{ optional($tv->taikhoan)->name ?? 'N/A' }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-white/90 p-4">
                    <div class="text-sm font-semibold text-gray-800">Tài sản trong phòng</div>
                    @if ($taisanphong->isEmpty())
                        <div class="mt-2 text-sm text-gray-500">Chưa có thiết bị.</div>
                    @else
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($taisanphong as $ts)
                                <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700">
                                    {{ $ts->tentaisan ?? 'N/A' }} x{{ $ts->soluong ?? 0 }} - {{ $ts->tinhtrang ?? 'Chưa rõ' }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6 rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div class="text-base font-semibold text-emerald-800">Thông báo KTX</div>
            <div class="text-xs text-emerald-600">Cập nhật mới nhất</div>
        </div>

        @if ($thongbao->isEmpty())
            <div class="text-sm text-gray-500">Chưa có thông báo mới.</div>
        @else
            <ol class="relative ml-2 border-l border-emerald-200 pl-4">
                @foreach ($thongbao as $tb)
                    <li class="mb-4">
                        <span class="absolute -left-[7px] mt-1.5 h-3 w-3 rounded-full border border-emerald-300 bg-emerald-500"></span>
                        <div class="flex items-start gap-3">
                            <div class="min-w-[74px] rounded-lg bg-emerald-50 px-2 py-1 text-center text-xs font-semibold text-emerald-700">
                                {{ \Illuminate\Support\Carbon::parse($tb->ngaydang)->format('d/m/Y') }}
                            </div>
                            <div class="flex-1 rounded-xl border border-emerald-100 bg-emerald-50/40 p-3">
                                <div class="font-semibold text-gray-900">
                                    <a href="{{ route('student.chitietthongbao', ['id' => $tb->id]) }}" class="hover:text-emerald-700 hover:underline">
                                        {{ $tb->tieude }}
                                    </a>
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    {{ \Illuminate\Support\Str::limit($tb->noidung, 120) }}
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5">
        <div class="text-sm font-semibold text-gray-700">Liên hệ khẩn cấp</div>
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
            @foreach ($lienhekhancap as $lh)
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <div class="font-semibold text-gray-900">{{ $lh['title'] }}</div>
                    <div class="text-sm text-gray-600">{{ $lh['phone'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($hoadonchuathanhtoan->isNotEmpty())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
            <div class="font-semibold text-red-700">Bạn có {{ $hoadonchuathanhtoan->count() }} hóa đơn chưa thanh toán</div>
            <ul class="mt-2 space-y-1 text-sm text-red-700">
                @foreach ($hoadonchuathanhtoan as $hoadon)
                    <li>Phòng {{ optional($hoadon->phong)->tenphong ?? 'N/A' }} tháng {{ $hoadon->thang }}/{{ $hoadon->nam }}: {{ number_format($hoadon->tongtien) }} đ</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('student.danhsachphong') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-sm">
            <div class="text-sm text-gray-500">Xem phòng trống</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Đăng ký phòng</div>
        </a>

        <a href="{{ route('student.hoadoncuaem') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-sm">
            <div class="text-sm text-gray-500">Xem hóa đơn</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Hóa đơn hàng tháng</div>
        </a>

        <a href="{{ route('student.danhsachbaohong') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-sm">
            <div class="text-sm text-gray-500">Gửi yêu cầu</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Báo hỏng</div>
        </a>

        <form method="POST" action="{{ route('student.yeucautraphong') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-sm">
            @csrf
            <div class="text-sm text-gray-500">Thông tin</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">Yêu cầu trả phòng</div>
            <button type="submit" class="mt-4 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                Gửi yêu cầu trả phòng
            </button>
        </form>
    </div>
@endsection
