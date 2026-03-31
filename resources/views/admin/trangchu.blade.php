@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Trang chủ quản trị</div>
        <div class="text-sm text-gray-500">
            Tháng {{ $thanghientai }}/{{ $namhientai }}
        </div>
    </div>

    <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 p-4">
        <div class="text-sm text-blue-700 font-semibold">Thông báo mới nhất</div>
        @if($thongbao->isEmpty())
            <div class="text-sm text-gray-500">Chưa có thông báo.</div>
        @else
            <ul class="mt-2 space-y-2 text-sm text-gray-700">
                @foreach($thongbao as $tb)
                    <li class="rounded-lg border border-blue-200 bg-blue-100 p-2">
                        <div class="font-semibold">{{ $tb->tieude }}</div>
                        <div>{{ $tb->noidung }}</div>
                        <div class="text-xs text-blue-600">{{ $tb->ngaydang }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Khối 4 thẻ thống kê nhanh --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Tổng phòng</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $tongphong }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Tổng sinh viên</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $tongsinhvien }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Phòng trống</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $tongphongtrong }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Đăng ký chờ xử lý</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $dangkychoxuly }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Doanh thu & Các thống kê phụ --}}
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="text-sm text-gray-500">Báo hỏng chờ sửa</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $baohongchosua }}</div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="text-sm text-gray-500">Hóa đơn chưa thanh toán</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $hoadonchuathanhtoan }}</div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-gray-900 to-gray-700 p-5 text-white">
            <div class="text-sm text-gray-200">Doanh thu tháng (đã thu)</div>
            <div class="mt-2 text-3xl font-bold">{{ number_format($doanhthuthang) }} đ</div>
            <div class="mt-1 text-xs text-gray-200 italic">* Tính theo hóa đơn đã xác nhận thanh toán.</div>
        </div>
    </div>

    {{-- Khu vực Biểu đồ --}}
    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-4 text-sm font-semibold text-gray-900">Biểu đồ doanh thu 6 tháng gần nhất</div>
        <canvas id="doanhthu6thang" height="100"></canvas>
    </div>

    {{-- Hai bảng danh sách gần nhất --}}
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- Đăng ký phòng --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="text-sm font-semibold text-gray-900">Đăng ký mới nhất (Chờ xử lý)</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Mã đơn</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Phòng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($danhsachdangkygannhat as $dangky)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900">#{{ $dangky->id }}</td>
                            <td class="px-6 py-4">{{ optional($dangky->sinhvien->taikhoan)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($dangky->phong)->tenphong ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-center text-gray-500" colspan="3">Không có đăng ký chờ xử lý.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Báo hỏng --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="text-sm font-semibold text-gray-900">Báo hỏng mới nhất (Chờ sửa)</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Mã đơn</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Phòng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($danhsachbaohonggannhat as $baohong)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900">#{{ $baohong->id }}</td>
                            <td class="px-6 py-4">{{ optional($baohong->sinhvien->taikhoan)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($baohong->phong)->tenphong ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-center text-gray-500" colspan="3">Không có báo hỏng chờ sửa.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PHẦN XỬ LÝ BIỂU ĐỒ - KHÔNG DÙNG PHP TRONG SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Thẻ ẩn chứa dữ liệu, dùng single quotes để tránh lỗi nháy kép trong JSON --}}
    <div id="data-chart-container" style="display: none;"
         data-labels='{!! json_encode($nhan ?? []) !!}'
         data-values='{!! json_encode($doanhthugannhat ?? []) !!}'>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = document.getElementById('data-chart-container');
            const labelsArr = JSON.parse(chartData.getAttribute('data-labels') || '[]');
            const valuesArr = JSON.parse(chartData.getAttribute('data-values') || '[]');

            const ctx = document.getElementById('doanhthu6thang');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labelsArr,
                        datasets: [{
                            label: 'Doanh thu thực tế (vnđ)',
                            data: valuesArr,
                            backgroundColor: '#4f46e5',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
@endsection
