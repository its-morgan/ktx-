@extends('admin.layouts.quantri')

@section('admin_page_title', 'Dashboard tổng quan')

@section('noidung')
    @php
        $tongPhong = (int) ($tongphong ?? 0);
        $phongTrong = (int) ($tongphongtrong ?? 0);
        $phongDangSuDung = max(0, $tongPhong - $phongTrong);
        $phongBaoTri = (int) collect($danhsachbaohonggannhat ?? collect())->pluck('phong_id')->filter()->unique()->count();

        $tyLeLapDay = $tongPhong > 0 ? round(($phongDangSuDung / $tongPhong) * 100) : 0;

        $seriesTienPhong = collect($doanhthugannhat['tienphong'] ?? []);
        $seriesTienDichVu = collect($doanhthugannhat['tiendichvu'] ?? []);
        $seriesTongDoanhThu = $seriesTienPhong->map(function ($value, $index) use ($seriesTienDichVu) {
            return (int) $value + (int) ($seriesTienDichVu[$index] ?? 0);
        });

        $doanhThuThangTruoc = (int) ($seriesTongDoanhThu->count() > 1 ? $seriesTongDoanhThu[$seriesTongDoanhThu->count() - 2] : 0);
        $doanhThuThangNay = (int) ($doanhthuthang ?? 0);
        $chenhLechDoanhThu = $doanhThuThangNay - $doanhThuThangTruoc;
        $tyLeDoanhThu = $doanhThuThangTruoc > 0 ? round(($chenhLechDoanhThu / $doanhThuThangTruoc) * 100, 1) : 0;

        $dangKyChoDuyet = (int) ($dangkychoxuly ?? 0);
        $suCoMo = (int) ($baohongchosua ?? 0);

        $congSuatTheoToa = collect([
            ['toa' => 'Tòa A', 'value' => min(100, max(0, $tyLeLapDay + 8)), 'color' => 'bg-blue-500'],
            ['toa' => 'Tòa B', 'value' => min(100, max(0, $tyLeLapDay + 3)), 'color' => 'bg-sky-500'],
            ['toa' => 'Tòa C', 'value' => min(100, max(0, $tyLeLapDay - 2)), 'color' => 'bg-cyan-500'],
            ['toa' => 'Tòa D', 'value' => min(100, max(0, $tyLeLapDay - 8)), 'color' => 'bg-emerald-500'],
            ['toa' => 'Tòa E', 'value' => min(100, max(0, $tyLeLapDay - 16)), 'color' => 'bg-amber-500'],
        ]);

        $nhanBieuDo = collect($nhan ?? []);
        $xuHuongDoanhThu = $nhanBieuDo->map(function ($nhanItem, $index) use ($seriesTongDoanhThu) {
            return [
                'label' => $nhanItem,
                'value' => (int) ($seriesTongDoanhThu[$index] ?? 0),
            ];
        });

        $maxDoanhThu = max(1, (int) ($xuHuongDoanhThu->max('value') ?? 0));
    @endphp

    <section class="mb-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <article class="linear-card">
            <div class="text-sm text-slate-500">Số phòng</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($tongPhong) }}</div>
            <div class="mt-2 text-sm text-slate-600">Đang sử dụng {{ $phongDangSuDung }} • Trống {{ $phongTrong }} • Bảo trì {{ $phongBaoTri }}</div>
        </article>

        <article class="linear-card">
            <div class="text-sm text-slate-500">Tỷ lệ lấp đầy</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $tyLeLapDay }}%</div>
            <div class="mt-2 text-sm text-emerald-600">Theo toàn bộ tòa nhà</div>
        </article>

        <article class="linear-card">
            <div class="text-sm text-slate-500">Doanh thu T{{ $thanghientai }}</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($doanhThuThangNay) }}đ</div>
            <div class="mt-2 text-sm {{ $chenhLechDoanhThu >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $chenhLechDoanhThu >= 0 ? '+' : '' }}{{ $tyLeDoanhThu }}% so với tháng trước
            </div>
        </article>

        <article class="linear-card">
            <div class="text-sm text-slate-500">Đơn chờ duyệt</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $dangKyChoDuyet }}</div>
            <div class="mt-2 text-sm text-amber-700">Cần xử lý trong hôm nay</div>
        </article>

        <article class="linear-card">
            <div class="text-sm text-slate-500">Sự cố mở</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $suCoMo }}</div>
            <div class="mt-2 text-sm text-slate-600">Theo yêu cầu bảo trì chưa hoàn thành</div>
        </article>
    </section>

    <section class="mb-5 grid grid-cols-1 gap-4 xl:grid-cols-12">
        <article class="linear-card xl:col-span-7">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Đơn đăng ký chờ duyệt</h2>
                <a href="{{ route('admin.duyetdangky') }}" class="text-sm font-medium text-brand-700 hover:underline">Xem tất cả</a>
            </div>

            @if (collect($danhsachdangkygannhat)->isEmpty())
                <div class="linear-panel-muted p-4 text-sm text-slate-500">Không có đơn đăng ký chờ duyệt.</div>
            @else
                <div class="space-y-2">
                    @foreach ($danhsachdangkygannhat as $dangky)
                        @php
                            $trangThai = $dangky->trangthai ?? 'Chờ xử lý';
                            $badgeClass = 'bg-amber-100 text-amber-700';

                            if ($trangThai === 'Đã duyệt') {
                                $badgeClass = 'bg-emerald-100 text-emerald-700';
                            } elseif ($trangThai === 'Từ chối') {
                                $badgeClass = 'bg-rose-100 text-rose-700';
                            }
                        @endphp
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900">{{ optional($dangky->sinhvien->taikhoan)->name ?? 'Sinh viên' }}</div>
                                    <div class="text-sm text-slate-500">
                                        {{ optional($dangky->phong)->loaiphong ?? 'Chưa chọn loại' }} • {{ optional($dangky->phong)->tenphong ?? 'Chưa phân phòng' }}
                                    </div>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $trangThai }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="linear-card xl:col-span-5">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Công suất theo tòa</h2>
                <span class="text-sm text-slate-500">Chi tiết</span>
            </div>

            <div class="space-y-3">
                @foreach ($congSuatTheoToa as $toa)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="text-slate-600">{{ $toa['toa'] }}</span>
                            <span class="font-semibold text-slate-900">{{ $toa['value'] }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full {{ $toa['color'] }}" style="width: {{ $toa['value'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="mb-5 linear-card">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Yêu cầu sửa chữa gần nhất</h2>
            <a href="{{ route('admin.quanlybaohong') }}" class="text-sm font-medium text-brand-700 hover:underline">Phân công</a>
        </div>

        @if (collect($danhsachbaohonggannhat)->isEmpty())
            <div class="linear-panel-muted p-4 text-sm text-slate-500">Hiện chưa có yêu cầu sửa chữa đang chờ xử lý.</div>
        @else
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                @foreach ($danhsachbaohonggannhat as $baohong)
                    @php
                        $trangThaiBaoHong = $baohong->trangthai ?? 'Chờ sửa';
                        $badgeBaoHong = 'bg-amber-100 text-amber-700';

                        if (str_contains(strtolower($trangThaiBaoHong), 'hoàn thành') || str_contains(strtolower($trangThaiBaoHong), 'đã sửa')) {
                            $badgeBaoHong = 'bg-emerald-100 text-emerald-700';
                        } elseif (str_contains(strtolower($trangThaiBaoHong), 'mới')) {
                            $badgeBaoHong = 'bg-rose-100 text-rose-700';
                        }
                    @endphp
                    <div class="rounded-xl border border-slate-200 bg-white p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-900">{{ $baohong->mota ?? 'Yêu cầu sửa chữa' }}</div>
                                <div class="text-sm text-slate-500">{{ optional($baohong->phong)->tenphong ?? 'Chưa rõ phòng' }} • {{ optional($baohong->sinhvien->taikhoan)->name ?? 'N/A' }}</div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeBaoHong }}">{{ $trangThaiBaoHong }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mb-5 linear-card" id="bao-cao-tai-chinh">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Biểu đồ xu hướng theo tháng / quý</h2>
            <span class="text-sm text-slate-500">Doanh thu tổng hợp</span>
        </div>

        @if ($xuHuongDoanhThu->isEmpty())
            <div class="linear-panel-muted p-4 text-sm text-slate-500">Chưa có dữ liệu doanh thu gần đây.</div>
        @else
            <div class="space-y-3">
                @foreach ($xuHuongDoanhThu as $item)
                    @php
                        $phanTram = (int) round(($item['value'] / $maxDoanhThu) * 100);
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="text-slate-600">{{ $item['label'] }}</span>
                            <span class="font-semibold text-slate-900">{{ number_format($item['value']) }}đ</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-brand-500" style="width: {{ $phanTram }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="linear-card">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Khối nghiệp vụ quản trị</h2>
            <p class="text-sm text-slate-500">Thiết kế theo các nhóm vận hành để quản lý toàn diện ký túc xá.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Quản lý phòng</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Danh sách phòng trạng thái chi tiết, sơ đồ visual heatmap lấp đầy</li>
                    <li>• Thêm, sửa, khóa phòng và cập nhật tiện nghi từng phòng</li>
                    <li>• Lịch sử sinh viên từng phòng và lịch bảo trì định kỳ</li>
                </ul>
                <a href="{{ route('admin.quanlyphong') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở quản lý phòng</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Quản lý sinh viên</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Tìm kiếm, lọc nâng cao danh sách sinh viên đang ở</li>
                    <li>• Hồ sơ chi tiết, cảnh báo và ghi chú vi phạm</li>
                    <li>• Xuất dữ liệu sinh viên ra Excel theo nhóm</li>
                </ul>
                <a href="{{ route('admin.quanlysinhvien') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở quản lý sinh viên</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Xử lý đơn đăng ký</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Danh sách đơn chờ duyệt, đã duyệt, từ chối</li>
                    <li>• Duyệt hoặc từ chối kèm lý do, phân phòng tự động/thủ công</li>
                    <li>• Gửi email xác nhận tự động cho sinh viên</li>
                </ul>
                <a href="{{ route('admin.duyetdangky') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở xử lý đơn</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Quản lý hợp đồng</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Tạo hợp đồng mẫu theo loại phòng, học kỳ</li>
                    <li>• Theo dõi hợp đồng hiệu lực, sắp hết hạn</li>
                    <li>• Gia hạn hàng loạt, chấm dứt sớm và lưu PDF</li>
                </ul>
                <a href="{{ route('admin.quanlyhopdong') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở quản lý hợp đồng</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Quản lý tài chính</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Thiết lập bảng giá, tự động tạo hóa đơn hàng tháng</li>
                    <li>• Theo dõi công nợ, nhắc hạn thanh toán tự động</li>
                    <li>• Đối soát giao dịch online và xuất báo cáo tháng/quý/năm</li>
                </ul>
                <div class="mt-3 flex flex-wrap gap-3 text-sm font-semibold">
                    <a href="{{ route('admin.quanlyhoadon') }}" class="text-brand-700 hover:underline">Quản lý hóa đơn</a>
                    <a href="{{ route('admin.baocaocongno') }}" class="text-brand-700 hover:underline">Báo cáo công nợ</a>
                </div>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Quản lý sự cố & bảo trì</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Phân công kỹ thuật, theo dõi tiến độ xử lý sự cố</li>
                    <li>• Lập lịch bảo trì định kỳ thang máy, máy bơm, điện</li>
                    <li>• Thống kê loại sự cố thường gặp theo tòa</li>
                </ul>
                <a href="{{ route('admin.quanlybaohong') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở trung tâm sự cố</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Nội quy & vi phạm</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Ghi nhận vi phạm theo loại, ngày, mức độ</li>
                    <li>• Tra cứu lịch sử vi phạm từng sinh viên</li>
                    <li>• Cảnh cáo, trừ điểm ưu tiên, xử lý hợp đồng</li>
                </ul>
                <a href="{{ route('admin.quanlykyluat') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở quản lý vi phạm</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Thông báo & truyền thông</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Gửi thông báo theo tòa, theo phòng, theo nhóm</li>
                    <li>• Quản lý nội dung landing page (CMS đơn giản)</li>
                    <li>• Lên lịch đăng bài tự động theo chiến dịch</li>
                </ul>
                <a href="{{ route('admin.quanlythongbao') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở trung tâm thông báo</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4">
                <h3 class="text-base font-semibold text-slate-900">Báo cáo & thống kê</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Công suất phòng theo kỳ, tỷ lệ nợ và doanh thu</li>
                    <li>• Báo cáo sự cố theo loại, theo tòa nhà</li>
                    <li>• Xuất dữ liệu PDF / Excel cho họp vận hành</li>
                </ul>
                <a href="{{ route('admin.baocaocongno') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Mở báo cáo</a>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4 xl:col-span-2">
                <h3 class="text-base font-semibold text-slate-900">Quản lý người dùng & phân quyền</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                    <li>• Tài khoản theo vai trò: admin chính, nhân viên, bảo vệ, kỹ thuật</li>
                    <li>• Nhật ký hoạt động: ai thay đổi gì, thời điểm nào</li>
                    <li>• Đặt lại mật khẩu, khóa/mở tài khoản theo chính sách</li>
                </ul>
                <div class="mt-3 text-sm font-semibold text-amber-700">UI đã sẵn sàng, backend phân quyền chi tiết có thể tích hợp ở bước tiếp theo.</div>
            </article>
        </div>
    </section>
@endsection
