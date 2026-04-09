@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex items-end justify-between">
        <div>
            <div class="text-2xl font-bold text-[#121212]">Báo cáo công nợ</div>
            <div class="text-sm text-[#606060]">
                Danh sách hóa đơn chưa thanh toán quá {{ $ngayQuaHan }} ngày.
            </div>
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200/70 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-[#606060]">Phòng nợ</div>
            <div class="mt-1 text-2xl font-bold text-[#121212]">{{ $thongke['tong_phong_no'] ?? 0 }}</div>
        </div>
        <div class="rounded-lg border border-gray-200/70 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-[#606060]">Sinh viên liên quan</div>
            <div class="mt-1 text-2xl font-bold text-[#121212]">{{ $thongke['tong_sinh_vien_no'] ?? 0 }}</div>
        </div>
        <div class="rounded-lg border border-gray-200/70 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-[#606060]">Hóa đơn quá hạn</div>
            <div class="mt-1 text-2xl font-bold text-[#121212]">{{ $thongke['so_hoa_don_qua_han'] ?? 0 }}</div>
        </div>
        <div class="rounded-lg border border-gray-200/70 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-[#606060]">Tổng tiền nợ</div>
            <div class="mt-1 text-2xl font-bold text-[#121212]">{{ number_format($thongke['tong_tien_no'] ?? 0) }} đ</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Phòng</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Hóa đơn quá hạn</th>
                        <th class="px-6 py-3">Tổng nợ</th>
                        <th class="px-6 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($congnoTheoPhong as $phongId => $dong)
                        @php
                            $phong = $dong['phong'] ?? null;
                            $danhsachSinhvien = $dong['sinhvien'] ?? collect();
                            $danhsachHoadon = collect($dong['hoadon'] ?? []);
                            $tongTien = (int) ($dong['tongtien'] ?? 0);
                        @endphp
                        <tr class="border-t border-gray-200/70 align-top">
                            <td class="px-6 py-4 font-semibold text-[#121212]">
                                {{ $phong->tenphong ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($danhsachSinhvien->isEmpty())
                                    <span class="text-[#9A9A9A]">Không có dữ liệu</span>
                                @else
                                    <div class="space-y-1">
                                        @foreach($danhsachSinhvien as $sv)
                                            <div>
                                                {{ $sv->masinhvien }} - {{ $sv->taikhoan->name ?? 'N/A' }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @foreach($danhsachHoadon as $hoadon)
                                        <div>
                                            Tháng {{ $hoadon->thang }}/{{ $hoadon->nam }}:
                                            <span class="font-medium text-[#121212]">{{ number_format($hoadon->tongtien) }} đ</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-[#121212]">
                                {{ number_format($tongTien) }} đ
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.guinhacnho', $phongId) }}" onsubmit="return confirm('Gửi thông báo nhắc nợ cho phòng này?')">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white hover:bg-gray-800">
                                        Gửi nhắc nợ
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4" colspan="5">
                                <x-empty-state
                                    title="Không có công nợ quá hạn"
                                    description="Tất cả hóa đơn đang được thanh toán đúng hạn."
                                    actionLabel="Tải lại trang"
                                    :actionHref="request()->fullUrl()"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
