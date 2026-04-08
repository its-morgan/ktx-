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
            <div class="linear-title">Trang chủ sinh viên</div>
            <div class="linear-subtitle">Tổng quan thông tin phòng, hóa đơn và thông báo.</div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="linear-card">
                <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-md border border-gray-200/70 bg-[#F7F7F8] text-[#606060]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 9v1m8-5a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                </div>
                <div class="text-xs font-medium uppercase tracking-wide text-[#606060]">Cần đóng</div>
                <div class="mt-1 text-base font-semibold text-[#121212]">
                    @if ($tongTienCanDong > 0)
                        {{ number_format($tongTienCanDong) }} đ
                    @else
                        0 đ
                    @endif
                </div>
            </div>

            <div class="linear-card">
                <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-md border border-gray-200/70 bg-[#F7F7F8] text-[#606060]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                    </svg>
                </div>
                <div class="text-xs font-medium uppercase tracking-wide text-[#606060]">Còn lại HĐ</div>
                <div class="mt-1 text-base font-semibold text-[#121212]">
                    @if (is_null($ngayConLaiHopDong))
                        Chưa có
                    @elseif ($ngayConLaiHopDong < 0)
                        Hết hạn
                    @else
                        {{ $ngayConLaiHopDong }} ngày
                    @endif
                </div>
            </div>

            <div class="linear-card">
                <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-md border border-gray-200/70 bg-[#F7F7F8] text-[#606060]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12c0 4.971-4.029 9-9 9s-9-4.029-9-9 4.029-9 9-9 9 4.029 9 9z" />
                    </svg>
                </div>
                <div class="text-xs font-medium uppercase tracking-wide text-[#606060]">Vi phạm</div>
                <div class="mt-1 text-base font-semibold text-[#121212]">{{ $kyluatcuaem->count() }}</div>
            </div>
        </div>
    </div>

    @if ($kyluatcuaem->isNotEmpty())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
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
        <div class="mb-6 rounded-lg border border-gray-200/70 bg-white p-5">
            <div class="text-lg font-semibold text-[#121212]">Bạn chưa được xếp phòng</div>
            <div class="text-sm text-[#606060]">Hãy vào mục Phòng trống để đăng ký nhé.</div>
        </div>
    @else
        @php
            $sodangtrenphong = $thanhviencungphong->count() + 1;
            $soluongtoida = $phonghientai->soluongtoida ?? 0;
        @endphp

        <div class="mb-6 linear-card">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="text-lg font-semibold text-[#121212]">Phòng của tôi</div>
                    <div class="text-sm text-[#606060]">Thông tin phòng, thành viên và tài sản hiện có.</div>
                </div>
                <div class="rounded-md border border-gray-200/70 bg-[#F7F7F8] px-3 py-2 text-sm font-medium text-[#606060]">
                    {{ $sodangtrenphong }} / {{ $soluongtoida }} thành viên
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="linear-panel-muted p-3">
                    <div class="text-xs uppercase tracking-wide text-[#606060]">Tên phòng</div>
                    <div class="mt-1 text-base font-semibold text-[#121212]">{{ $phonghientai->tenphong ?? '-' }}</div>
                </div>
                <div class="linear-panel-muted p-3">
                    <div class="text-xs uppercase tracking-wide text-[#606060]">Giới tính</div>
                    <div class="mt-1 text-base font-semibold text-[#121212]">
                        @if (optional($phonghientai)->gioitinh === 'Nữ')
                            Nữ
                        @else
                            Nam
                        @endif
                    </div>
                </div>
                <div class="linear-panel-muted p-3">
                    <div class="text-xs uppercase tracking-wide text-[#606060]">Sức chứa</div>
                    <div class="mt-1 text-base font-semibold text-[#121212]">{{ $sodangtrenphong }} / {{ $soluongtoida }}</div>
                </div>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                <div class="linear-panel p-4">
                    <div class="text-sm font-semibold text-[#121212]">Bạn cùng phòng</div>
                    @if ($thanhviencungphong->isEmpty())
                        <div class="mt-2 text-sm text-[#606060]">Chưa có thành viên khác.</div>
                    @else
                        <div class="mt-3 flex items-center -space-x-2 overflow-x-auto pb-1">
                            @foreach ($thanhviencungphong as $tv)
                                @php
                                    $tenThanhVien = optional($tv->taikhoan)->name ?? 'N/A';
                                    $chuCaiDau = strtoupper(substr($tenThanhVien, 0, 1));
                                @endphp
                                <div title="{{ $tenThanhVien }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200/70 bg-[#121212] text-sm font-semibold text-white">
                                    {{ $chuCaiDau }}
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($thanhviencungphong as $tv)
                                <span class="linear-chip">
                                    {{ optional($tv->taikhoan)->name ?? 'N/A' }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="linear-panel p-4">
                    <div class="text-sm font-semibold text-[#121212]">Tài sản trong phòng</div>
                    @if ($taisanphong->isEmpty())
                        <div class="mt-2 text-sm text-[#606060]">Chưa có thiết bị.</div>
                    @else
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($taisanphong as $ts)
                                <span class="linear-chip">
                                    {{ $ts->tentaisan ?? 'N/A' }} x{{ $ts->soluong ?? 0 }} - {{ $ts->tinhtrang ?? 'Chưa rõ' }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6 linear-card">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div class="text-base font-semibold text-[#121212]">Thông báo KTX</div>
            <div class="text-xs text-[#606060]">Cập nhật mới nhất</div>
        </div>

        @if ($thongbao->isEmpty())
            <div class="text-sm text-[#606060]">Chưa có thông báo mới.</div>
        @else
            <ol class="space-y-3">
                @foreach ($thongbao as $tb)
                    <li class="linear-panel-muted p-3">
                        <div class="flex items-start gap-3">
                            <div class="min-w-[74px] rounded-md border border-gray-200/70 bg-white px-2 py-1 text-center text-xs font-semibold text-[#606060]">
                                {{ \Illuminate\Support\Carbon::parse($tb->ngaydang)->format('d/m/Y') }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-[#121212]">
                                    <a href="{{ route('student.chitietthongbao', ['id' => $tb->id]) }}" class="hover:underline">
                                        {{ $tb->tieude }}
                                    </a>
                                </div>
                                <div class="mt-1 text-sm text-[#606060]">
                                    {{ \Illuminate\Support\Str::limit($tb->noidung, 120) }}
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    <div class="mb-6 linear-card">
        <div class="text-sm font-semibold text-[#121212]">Liên hệ khẩn cấp</div>
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
            @foreach ($lienhekhancap as $lh)
                <div class="linear-panel-muted p-3">
                    <div class="font-semibold text-[#121212]">{{ $lh['title'] }}</div>
                    <div class="text-sm text-[#606060]">{{ $lh['phone'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($hoadonchuathanhtoan->isNotEmpty())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
            <div class="font-semibold text-red-700">Bạn có {{ $hoadonchuathanhtoan->count() }} hóa đơn chưa thanh toán</div>
            <ul class="mt-2 space-y-1 text-sm text-red-700">
                @foreach ($hoadonchuathanhtoan as $hoadon)
                    <li>Phòng {{ optional($hoadon->phong)->tenphong ?? 'N/A' }} tháng {{ $hoadon->thang }}/{{ $hoadon->nam }}: {{ number_format($hoadon->tongtien) }} đ</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('student.danhsachphong') }}" class="linear-card transition hover:-translate-y-0.5 hover:bg-[#F7F7F8]">
            <div class="text-sm text-[#606060]">Xem phòng trống</div>
            <div class="mt-2 text-lg font-semibold text-[#121212]">Đăng ký phòng</div>
        </a>

        <a href="{{ route('student.hoadoncuaem') }}" class="linear-card transition hover:-translate-y-0.5 hover:bg-[#F7F7F8]">
            <div class="text-sm text-[#606060]">Xem hóa đơn</div>
            <div class="mt-2 text-lg font-semibold text-[#121212]">Hóa đơn hàng tháng</div>
        </a>

        <a href="{{ route('student.danhsachbaohong') }}" class="linear-card transition hover:-translate-y-0.5 hover:bg-[#F7F7F8]">
            <div class="text-sm text-[#606060]">Gửi yêu cầu</div>
            <div class="mt-2 text-lg font-semibold text-[#121212]">Báo hỏng</div>
        </a>

        <form method="POST" action="{{ route('student.yeucautraphong') }}" class="linear-card transition hover:-translate-y-0.5 hover:bg-[#F7F7F8]">
            @csrf
            <div class="text-sm text-[#606060]">Thông tin</div>
            <div class="mt-2 text-lg font-semibold text-[#121212]">Yêu cầu trả phòng</div>
            <button type="submit" class="linear-btn-primary mt-4">
                Gửi yêu cầu trả phòng
            </button>
        </form>
    </div>
@endsection
