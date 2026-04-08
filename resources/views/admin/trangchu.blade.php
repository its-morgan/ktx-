@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Trang chủ quản trị</div>
        <div class="text-sm text-[#606060]">
            Tháng {{ $thanghientai }}/{{ $namhientai }}
        </div>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200/70 bg-[#F7F7F8] p-4">
        <div class="text-sm text-[#606060] font-semibold">Thông báo mới nhất</div>
        @if($thongbao->isEmpty())
            <div class="text-sm text-[#606060]">Chưa có thông báo.</div>
        @else
            <ul class="mt-2 space-y-2 text-sm text-[#606060]">
                @foreach($thongbao as $tb)
                    <li class="rounded-lg border border-gray-200/70 bg-[#F7F7F8] p-2">
                        <div class="font-semibold">{{ $tb->tieude }}</div>
                        <div>{{ $tb->noidung }}</div>
                        <div class="text-xs text-[#606060]">{{ $tb->ngaydang }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Khối 4 thẻ thống kê nhanh --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200/70 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-[#606060]">Tổng phòng</div>
                    <div class="mt-2 text-3xl font-bold text-[#121212]">{{ $tongphong }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F7F7F8] text-[#606060]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200/70 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-[#606060]">Tổng sinh viên</div>
                    <div class="mt-2 text-3xl font-bold text-[#121212]">{{ $tongsinhvien }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F7F7F8] text-[#606060]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200/70 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-[#606060]">Phòng trống</div>
                    <div class="mt-2 text-3xl font-bold text-[#121212]">{{ $tongphongtrong }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F7F7F8] text-[#606060]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200/70 border-l-4 border-l-amber-400 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-[#606060]">Đăng ký chờ xử lý</div>
                    <div class="mt-2 text-3xl font-bold text-[#121212]">{{ $dangkychoxuly }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F7F7F8] text-[#606060]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Doanh thu & Các thống kê phụ --}}
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200/70 border-l-4 border-l-rose-400 bg-white p-5">
            <div class="text-sm text-[#606060]">Báo hỏng chờ sửa</div>
            <div class="mt-2 text-3xl font-bold text-[#121212]">{{ $baohongchosua }}</div>
        </div>

        <div class="rounded-lg border border-gray-200/70 bg-white p-5">
            <div class="text-sm text-[#606060]">Hóa đơn chưa thanh toán</div>
            <div class="mt-2 text-3xl font-bold text-[#121212]">{{ $hoadonchuathanhtoan }}</div>
        </div>

        <div class="rounded-lg border border-gray-200/70 bg-brand-600 p-5 text-white">
            <div class="text-sm text-gray-200">Doanh thu tháng (đã thu)</div>
            <div class="mt-2 text-3xl font-bold">{{ number_format($doanhthuthang) }} đ</div>
            <div class="mt-1 text-xs text-gray-200 italic">* Tính theo hóa đơn đã xác nhận thanh toán.</div>
        </div>
    </div>

    {{-- Khu vực Biểu đồ --}}
    <div class="mt-6 rounded-lg border border-gray-200/70 bg-white p-5">
        <div class="mb-4 text-sm font-semibold text-[#121212]">Biểu đồ doanh thu 6 tháng gần nhất</div>
        <canvas id="doanhthu6thang" height="100"></canvas>
    </div>

    {{-- Hai bảng danh sách gần nhất --}}
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- Đăng ký phòng --}}
        <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
            <div class="border-b border-gray-200/70 px-6 py-4">
                <div class="text-sm font-semibold text-[#121212]">Đăng ký mới nhất (Chờ xử lý)</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#606060]">
                    <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Mã đơn</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Phòng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($danhsachdangkygannhat as $dangky)
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4 font-medium text-[#121212]">#{{ $dangky->id }}</td>
                            <td class="px-6 py-4">{{ optional($dangky->sinhvien->taikhoan)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($dangky->phong)->tenphong ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4 text-center text-[#606060]" colspan="3">Không có đăng ký chờ xử lý.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Báo hỏng --}}
        <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
            <div class="border-b border-gray-200/70 px-6 py-4">
                <div class="text-sm font-semibold text-[#121212]">Báo hỏng mới nhất (Chờ sửa)</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#606060]">
                    <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Mã đơn</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Phòng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($danhsachbaohonggannhat as $baohong)
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4 font-medium text-[#121212]">#{{ $baohong->id }}</td>
                            <td class="px-6 py-4">{{ optional($baohong->sinhvien->taikhoan)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($baohong->phong)->tenphong ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4 text-center text-[#606060]" colspan="3">Không có báo hỏng chờ sửa.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PHẦN XỬ LÝ BIỂU ĐỒ - 2 CỘT RIÊNG BIỆT --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Thẻ ẩn chứa dữ liệu, dùng single quotes để tránh lỗi nháy kép trong JSON --}}
    <div id="data-chart-container" style="display: none;"
         data-labels='{!! json_encode($nhan ?? []) !!}'
         data-tienphong='{!! json_encode($doanhthugannhat['tienphong'] ?? []) !!}'
         data-tiendichvu='{!! json_encode($doanhthugannhat['tiendichvu'] ?? []) !!}'>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = document.getElementById('data-chart-container');
            const labelsArr = JSON.parse(chartData.getAttribute('data-labels') || '[]');
            const tienPhongArr = JSON.parse(chartData.getAttribute('data-tienphong') || '[]');
            const tienDichVuArr = JSON.parse(chartData.getAttribute('data-tiendichvu') || '[]');

            const ctx = document.getElementById('doanhthu6thang');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labelsArr,
                        datasets: [
                            {
                                label: 'Tien phong (vnđ)',
                                data: tienPhongArr,
                                backgroundColor: '#4f46e5',
                                borderRadius: 6,
                            },
                            {
                                label: 'Tien dich vu (vnđ)',
                                data: tienDichVuArr,
                                backgroundColor: '#06b6d4',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                stacked: false,
                            },
                            y: {
                                beginAtZero: true,
                                stacked: false,
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { 
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
