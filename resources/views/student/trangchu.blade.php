@extends('student.layouts.chinh')

@section('student_page_title', 'Dashboard')

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

        $tongThanhVien = (isset($thanhviencungphong) ? $thanhviencungphong->count() : 0) + ($sinhvien ? 1 : 0);
        $hoaDonGanNhat = $hoadonchuathanhtoan->take(3);
        $trangThaiDon = 'Đang ở';

        if (empty($sinhvien?->phong_id)) {
            $trangThaiDon = 'Chờ duyệt';
        } elseif (is_null($ngayConLaiHopDong)) {
            $trangThaiDon = 'Chờ ký hợp đồng';
        }
    @endphp

    <div class="space-y-5">
        <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <article class="portal-kpi">
                <div class="text-sm text-slate-400">Phòng hiện tại</div>
                <div class="mt-1 text-3xl font-bold text-slate-900">
                    {{ $phonghientai->tenphong ?? 'Chưa xếp' }}
                </div>
                <div class="mt-1 text-sm text-slate-400">
                    @if (!empty($phonghientai))
                        Tòa {{ $phonghientai->toa ?? 'A' }} • Tầng {{ $phonghientai->tang ?? '-' }}
                    @else
                        Đang chờ bố trí phòng phù hợp
                    @endif
                </div>
            </article>

            <article class="portal-kpi">
                <div class="text-sm text-slate-400">Hóa đơn tháng này</div>
                <div class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($tongTienCanDong) }}đ</div>
                <div class="mt-1 text-sm text-amber-700">
                    @if ($hoaDonGanNhat->isNotEmpty())
                        Hạn gần nhất: {{ optional($hoaDonGanNhat->first())->thang }}/{{ optional($hoaDonGanNhat->first())->nam }}
                    @else
                        Không có hóa đơn quá hạn
                    @endif
                </div>
            </article>

            <article class="portal-kpi">
                <div class="text-sm text-slate-400">Hợp đồng còn lại</div>
                <div class="mt-1 text-3xl font-bold text-slate-900">
                    @if (is_null($ngayConLaiHopDong))
                        --
                    @elseif ($ngayConLaiHopDong < 0)
                        Hết hạn
                    @else
                        {{ $ngayConLaiHopDong }} ngày
                    @endif
                </div>
                <div class="mt-1 text-sm text-slate-400">
                    HK2 - {{ now()->format('Y') }}-{{ now()->addYear()->format('Y') }}
                </div>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <article class="portal-card">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Thông tin phòng ở</h2>
                    @if ($trangThaiDon === 'Đang ở')
                        <span class="portal-badge-success">{{ $trangThaiDon }}</span>
                    @elseif ($trangThaiDon === 'Chờ duyệt')
                        <span class="portal-badge-warning">{{ $trangThaiDon }}</span>
                    @else
                        <span class="portal-badge-muted">{{ $trangThaiDon }}</span>
                    @endif
                </div>

                <div class="space-y-2 text-sm text-slate-600">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-400">Phòng</span>
                        <span class="font-semibold text-slate-900">{{ $phonghientai->tenphong ?? 'Chưa xếp' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-400">Loại</span>
                        <span class="font-semibold text-slate-900">{{ !empty($phonghientai) ? ($phonghientai->succhuamax . ' người') : '--' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-400">Bạn cùng phòng</span>
                        <span class="font-semibold text-slate-900">
                            @if (!empty($thanhviencungphong) && $thanhviencungphong->isNotEmpty())
                                {{ $thanhviencungphong->pluck('taikhoan.name')->filter()->take(2)->implode(', ') }}
                                @if ($thanhviencungphong->count() > 2)
                                    +{{ $thanhviencungphong->count() - 2 }}
                                @endif
                            @else
                                Chưa có
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">HĐ hết hạn</span>
                        <span class="font-semibold text-slate-900">
                            @if (empty($sinhvien?->ngay_het_han))
                                Chưa có dữ liệu
                            @else
                                {{ \Illuminate\Support\Carbon::parse($sinhvien->ngay_het_han)->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>
                </div>
            </article>

            <article class="portal-card">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Hóa đơn gần nhất</h2>
                    <a href="{{ route('student.hoadoncuaem') }}" class="text-xs text-slate-600 hover:text-slate-900">Xem tất cả</a>
                </div>

                @if ($hoaDonGanNhat->isEmpty())
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-400">
                        Hiện chưa có hóa đơn cần xử lý.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($hoaDonGanNhat as $hoadon)
                            <div class="portal-card-soft">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold text-slate-900">Tiền phòng T{{ $hoadon->thang }}</div>
                                        <div class="text-xs text-slate-400">Kỳ {{ $hoadon->thang }}/{{ $hoadon->nam }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-slate-900">{{ number_format($hoadon->tongtien) }}đ</div>
                                        <span class="portal-badge-warning">Chưa TT</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        </section>

        <section class="portal-card">
            <div class="text-sm italic text-slate-400">
                Sidebar cố định khi cuộn. Dashboard bên phải chia module theo nghiệp vụ để sinh viên tự quản lý toàn bộ quá trình ở KTX.
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <article id="module-dangky" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Đăng ký phòng</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Chọn loại phòng, xem danh sách phòng còn trống theo thời gian thực</li>
                    <li>• Điền đơn đăng ký: thời gian ở, người ở cùng mong muốn</li>
                    <li>• Upload hồ sơ: CCCD/SV, giấy xác nhận sinh viên</li>
                    <li>• Theo dõi trạng thái: Chờ duyệt → Đã duyệt → Chờ ký HĐ → Đang ở</li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('student.danhsachphong') }}" class="linear-btn-primary">Đăng ký ngay</a>
                    <a href="{{ route('student.danhsachphong') }}#so-do-tang" class="linear-btn-secondary">Sơ đồ tầng</a>
                </div>
            </article>

            <article id="module-hoso" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Quản lý hồ sơ cá nhân</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Cập nhật số điện thoại, địa chỉ thường trú, liên hệ khẩn cấp</li>
                    <li>• Upload ảnh hồ sơ và giấy tờ bổ sung</li>
                    <li>• Xem thông tin phòng hiện tại, danh sách bạn cùng phòng</li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('profile.edit') }}" class="linear-btn-primary">Cập nhật hồ sơ</a>
                    <a href="{{ route('student.phongcuatoi') }}" class="linear-btn-secondary">Xem phòng hiện tại</a>
                </div>
            </article>

            <article id="module-hopdong" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Hợp đồng</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Xem chi tiết hợp đồng thuê phòng</li>
                    <li>• Ký hợp đồng điện tử và theo dõi tiến trình</li>
                    <li>• Tra cứu lịch sử hợp đồng các kỳ trước</li>
                    <li>• Gửi đơn gia hạn / không gia hạn hợp đồng</li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('student.hopdongcuatoi') }}" class="linear-btn-primary">Xem hợp đồng</a>
                    <a href="{{ route('student.phongcuatoi') }}" class="linear-btn-secondary">Gia hạn / Không gia hạn</a>
                </div>
            </article>

            <article id="module-taichinh" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Tài chính & Thanh toán</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Xem tiền phòng, điện, nước và các phí dịch vụ</li>
                    <li>• Lịch sử hóa đơn theo tháng / kỳ</li>
                    <li>• Thanh toán online: VNPay, MoMo, chuyển khoản</li>
                    <li>• Xuất hóa đơn / biên lai PDF và cảnh báo tới hạn</li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('student.hoadoncuaem') }}" class="linear-btn-primary">Xem hóa đơn</a>
                    <a href="{{ route('student.hoadoncuaem') }}" class="linear-btn-secondary">Lịch sử thanh toán</a>
                </div>
            </article>

            <article id="module-hotro" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Yêu cầu & Phản ánh</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Gửi yêu cầu sửa chữa điện, nước, khóa cửa, điều hòa...</li>
                    <li>• Upload ảnh mô tả sự cố và theo dõi tiến độ xử lý</li>
                    <li>• Đánh giá sau khi sự cố được hoàn tất</li>
                    <li>• Gửi góp ý về chất lượng dịch vụ KTX</li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('student.danhsachbaohong') }}" class="linear-btn-primary">Tạo yêu cầu sửa chữa</a>
                    <a href="{{ route('student.danhgia') }}" class="linear-btn-secondary">Đánh giá dịch vụ</a>
                </div>
            </article>

            <article id="module-dichvu" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Đăng ký dịch vụ</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Đăng ký gửi xe, giặt sấy, tủ locker</li>
                    <li>• Đăng ký / hủy internet tốc độ cao</li>
                    <li>• Đặt lịch sử dụng phòng gym, phòng sinh hoạt</li>
                </ul>
                <div class="mt-4 flex items-center gap-2">
                    <button type="button" class="linear-btn-secondary" disabled>Sắp triển khai</button>
                    <span class="text-xs text-slate-400">Module nghiệp vụ đã thiết kế UI, chờ tích hợp backend.</span>
                </div>
            </article>

            <article id="module-khach" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Khách & Ra vào</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Đăng ký khách đến thăm theo khung giờ</li>
                    <li>• Lưu thông tin CCCD và mục đích liên hệ</li>
                    <li>• Xem lịch sử ra vào phòng (nếu có thẻ từ)</li>
                </ul>
                <div class="mt-4">
                    <button type="button" class="linear-btn-secondary" disabled>Sắp triển khai</button>
                </div>
            </article>

            <article id="module-thongbao" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Thông báo</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Nhận thông báo hóa đơn mới, xử lý yêu cầu, thay đổi quy định</li>
                    <li>• Cài đặt kênh nhận: email, SMS, trong ứng dụng</li>
                    <li>• Xem toàn bộ lịch sử thông báo đã nhận</li>
                </ul>
                <div class="mt-4">
                    <a href="{{ route('student.thongbao') }}" class="linear-btn-primary">Mở trung tâm thông báo</a>
                </div>
            </article>

            <article id="module-traphong" class="portal-card">
                <h3 class="text-base font-semibold text-slate-900">Trả phòng</h3>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>• Nộp đơn trả phòng và chọn ngày dự kiến</li>
                    <li>• Checklist bàn giao tài sản phòng</li>
                    <li>• Theo dõi trạng thái hoàn trả tiền cọc</li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('student.yeucautraphong') }}">
                        @csrf
                        <button type="submit" class="linear-btn-primary">Gửi đơn trả phòng</button>
                    </form>
                    <a href="{{ route('student.phongcuatoi') }}" class="linear-btn-secondary">Xem checklist bàn giao</a>
                </div>
            </article>
        </section>
    </div>
@endsection

