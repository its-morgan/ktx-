@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Phòng của tôi</div>
        <div class="text-sm text-[#606060]">Tổng quan về phòng ở hiện tại.</div>
    </div>

    @if(!$coPhong)
        {{-- Chưa có phòng --}}
        <div class="rounded-xl border border-gray-200/70 bg-white p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-[#121212]">Bạn chưa có phòng</h3>
            <p class="mb-6 text-sm text-[#606060]">Vui lòng chọn phòng trống và gửi đăng ký.</p>
            
            @if($danhsachphongtrong->count() > 0)
                <div class="text-left">
                    <h4 class="mb-4 text-sm font-medium text-[#121212]">Gợi ý phòng phù hợp:</h4>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($danhsachphongtrong as $phong)
                            <div class="rounded-lg border border-gray-200/70 p-4">
                                <div class="font-medium text-[#121212]">{{ $phong->tenphong }}</div>
                                <div class="text-sm text-[#606060]">{{ number_format($phong->giaphong) }} đ/tháng</div>
                                <div class="mt-2 text-xs text-[#606060]">{{ $phong->succhuamax }} người tối đa</div>
                                <form method="POST" action="{{ route('student.dangkyphong') }}" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="phong_id" value="{{ $phong->id }}">
                                    <button type="submit" class="w-full rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                        Đăng ký ngay
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <a href="{{ route('student.danhsachphong') }}" class="mt-6 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700">
                Xem tất cả phòng trống
                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    @else
        {{-- Có phòng --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Thông tin phòng --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Card phòng --}}
                <div class="rounded-xl border border-gray-200/70 bg-white p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-[#121212]">{{ $phong->tenphong }}</h3>
                            <p class="text-sm text-[#606060]">Tầng {{ $phong->tang }} • {{ $phong->gioitinh }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-[#121212]">{{ number_format($phong->giaphong) }} đ</div>
                            <div class="text-xs text-[#606060]">/tháng</div>
                        </div>
                    </div>

                    @if($canhBaoHetHan)
                        <div class="mb-4 rounded-lg {{ $canhBaoHetHan['muc_do'] === 'nguy_hiểm' ? 'bg-red-50 border-red-200' : ($canhBaoHetHan['muc_do'] === 'cảnh_báo' ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200') }} border p-4">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 {{ $canhBaoHetHan['muc_do'] === 'nguy_hiểm' ? 'text-red-500' : ($canhBaoHetHan['muc_do'] === 'cảnh_báo' ? 'text-yellow-500' : 'text-blue-500') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium {{ $canhBaoHetHan['muc_do'] === 'nguy_hiểm' ? 'text-red-800' : ($canhBaoHetHan['muc_do'] === 'cảnh_báo' ? 'text-yellow-800' : 'text-blue-800') }}">
                                        Hợp đồng sắp hết hạn (còn {{ $canhBaoHetHan['so_ngay_con_lai'] }} ngày)
                                    </p>
                                    <p class="text-xs {{ $canhBaoHetHan['muc_do'] === 'nguy_hiểm' ? 'text-red-600' : ($canhBaoHetHan['muc_do'] === 'cảnh_báo' ? 'text-yellow-600' : 'text-blue-600') }}">
                                        Hết hạn: {{ $canhBaoHetHan['ngay_het_han'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <div class="text-xs text-[#606060]">Người đang ở</div>
                            <div class="text-lg font-semibold text-[#121212]">{{ $banCungPhong->count() + 1 }}/{{ $phong->succhuamax }}</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <div class="text-xs text-[#606060]">Bạn cùng phòng</div>
                            <div class="text-lg font-semibold text-[#121212]">{{ $banCungPhong->count() }}</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <div class="text-xs text-[#606060]">Đánh giá phòng</div>
                            <div class="flex items-center gap-1">
                                <span class="text-lg font-semibold text-[#121212]">{{ $diemTrungBinh }}</span>
                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if($hopdongHienTai)
                        <div class="mt-4 rounded-lg bg-gray-50 p-4">
                            <h4 class="mb-2 text-sm font-medium text-[#121212]">Thông tin hợp đồng</h4>
                            <div class="grid gap-2 text-sm sm:grid-cols-2">
                                <div class="text-[#606060]">Bắt đầu: <span class="text-[#121212]">{{ date('d/m/Y', strtotime($hopdongHienTai->ngay_bat_dau)) }}</span></div>
                                <div class="text-[#606060]">Kết thúc: <span class="text-[#121212]">{{ date('d/m/Y', strtotime($hopdongHienTai->ngay_ket_thuc)) }}</span></div>
                                <div class="text-[#606060]">Giá phòng lúc ký: <span class="text-[#121212]">{{ number_format($hopdongHienTai->giaphong_luc_ky) }} đ</span></div>
                                <div class="text-[#606060]">Trạng thái: <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{ $hopdongHienTai->trang_thai }}</span></div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Hóa đơn chưa thanh toán --}}
                @if($hoadonChuaThanhToan->count() > 0)
                    <div class="rounded-xl border border-red-200 bg-red-50 p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="font-semibold text-red-800">Hóa đơn chưa thanh toán</h3>
                            </div>
                            <div class="text-lg font-bold text-red-600">{{ number_format($tongNo) }} đ</div>
                        </div>
                        <div class="space-y-2">
                            @foreach($hoadonChuaThanhToan as $hoadon)
                                <div class="flex items-center justify-between rounded-lg bg-white p-3">
                                    <div>
                                        <div class="font-medium text-[#121212]">{{ $hoadon->thang }}/{{ $hoadon->nam }}</div>
                                        <div class="text-xs text-[#606060]">Hạn thanh toán: {{ date('d/m/Y', strtotime($hoadon->ngayxuat . ' +5 days')) }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-[#121212]">{{ number_format($hoadon->tongtien) }} đ</div>
                                        <a href="{{ route('student.phongcuatoi.hoadon.chitiet', $hoadon->id) }}" class="text-xs text-blue-600 hover:text-blue-700">Xem chi tiết</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Bạn cùng phòng --}}
                @if($banCungPhong->count() > 0)
                    <div class="rounded-xl border border-gray-200/70 bg-white p-6">
                        <h3 class="mb-4 font-semibold text-[#121212]">Bạn cùng phòng</h3>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($banCungPhong as $ban)
                                <div class="flex items-center gap-3 rounded-lg border border-gray-100 p-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600">
                                        {{ substr($ban->taikhoan->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-[#121212]">{{ $ban->taikhoan->name ?? 'Không có tên' }}</div>
                                        <div class="text-xs text-[#606060]">{{ $ban->mssv ?? '' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Tài sản phòng --}}
                @if($taisan->count() > 0 || $vattu->count() > 0)
                    <div class="rounded-xl border border-gray-200/70 bg-white p-6">
                        <h3 class="mb-4 font-semibold text-[#121212]">Tài sản & Vật tư phòng</h3>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($taisan as $item)
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3">
                                    <span class="text-sm text-[#121212]">{{ $item->tentaisan }}</span>
                                    <span class="text-xs text-[#606060]">x{{ $item->soluong }}</span>
                                </div>
                            @endforeach
                            @foreach($vattu as $item)
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3">
                                    <span class="text-sm text-[#121212]">{{ $item->tenvattu }}</span>
                                    <span class="text-xs text-[#606060]">x{{ $item->soluong }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Đánh giá phòng --}}
                <div class="rounded-xl border border-gray-200/70 bg-white p-6">
                    <h3 class="mb-3 font-semibold text-[#121212]">Đánh giá phòng</h3>
                    @if($daDanhGia)
                        <div class="rounded-lg bg-green-50 p-4 text-center">
                            <svg class="mx-auto mb-2 h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <p class="text-sm text-green-700">Bạn đã đánh giá phòng tháng này</p>
                        </div>
                    @else
                        <p class="mb-4 text-sm text-[#606060]">Chia sẻ đánh giá của bạn về phòng này</p>
                        <a href="{{ route('student.danhgia') }}" class="block w-full rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-medium text-white hover:bg-gray-800">
                            Viết đánh giá
                        </a>
                    @endif
                </div>

                {{-- Thông báo mới --}}
                @if($thongbaoMoiNhat->count() > 0)
                    <div class="rounded-xl border border-gray-200/70 bg-white p-6">
                        <h3 class="mb-3 font-semibold text-[#121212]">Thông báo mới</h3>
                        <div class="space-y-3">
                            @foreach($thongbaoMoiNhat as $tb)
                                <a href="{{ route('student.chitietthongbao', $tb->id) }}" class="block rounded-lg bg-gray-50 p-3 hover:bg-gray-100">
                                    <div class="mb-1 text-sm font-medium text-[#121212] line-clamp-1">{{ $tb->tieude }}</div>
                                    <div class="text-xs text-[#606060]">{{ date('d/m/Y', strtotime($tb->ngaydang)) }}</div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('student.thongbao') }}" class="mt-3 block text-center text-sm text-blue-600 hover:text-blue-700">
                            Xem tất cả
                        </a>
                    </div>
                @endif

                {{-- Quick Actions --}}
                <div class="rounded-xl border border-gray-200/70 bg-white p-6">
                    <h3 class="mb-3 font-semibold text-[#121212]">Thao tác nhanh</h3>
                    <div class="space-y-2">
                        <a href="{{ route('student.phongcuatoi.hoadon') }}" class="flex items-center gap-2 rounded-lg p-2 text-sm text-[#606060] hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Lịch sử hóa đơn
                        </a>
                        <a href="{{ route('student.danhsachbaohong') }}" class="flex items-center gap-2 rounded-lg p-2 text-sm text-[#606060] hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Báo hỏng
                        </a>
                        <a href="{{ route('student.taisanphong') }}" class="flex items-center gap-2 rounded-lg p-2 text-sm text-[#606060] hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Xem tài sản phòng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
