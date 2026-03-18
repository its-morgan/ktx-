@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Trang chủ quản trị</div>
        <div class="text-sm text-gray-500">
            Tháng {{ $thanghientai }}/{{ $namhientai }}
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Tổng phòng</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $tongphong }}</div>
                </div>
                <div class="h-10 w-10 rounded-xl bg-indigo-50"></div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Tổng sinh viên</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $tongsinhvien }}</div>
                </div>
                <div class="h-10 w-10 rounded-xl bg-sky-50"></div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Phòng trống</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $tongphongtrong }}</div>
                </div>
                <div class="h-10 w-10 rounded-xl bg-emerald-50"></div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Đăng ký chờ xử lý</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $dangkychoxuly }}</div>
                </div>
                <div class="h-10 w-10 rounded-xl bg-amber-50"></div>
            </div>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="text-sm text-gray-500">Báo hỏng chờ sửa</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $baohongchosua }}</div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="text-sm text-gray-500">Hóa đơn chưa thanh toán (tháng)</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $hoadonchuathanhtoan }}</div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-gray-900 to-gray-700 p-5 text-white">
            <div class="text-sm text-gray-200">Doanh thu tháng (đã thanh toán)</div>
            <div class="mt-2 text-3xl font-bold">{{ number_format($doanhthuthang) }} đ</div>
            <div class="mt-1 text-xs text-gray-200">Tính theo hóa đơn có trạng thái “Đã thanh toán”.</div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="text-sm font-semibold text-gray-900">Đăng ký gần nhất (Chờ xử lý)</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Mã đăng ký</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Phòng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($danhsachdangkygannhat as $dangky)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900">#{{ $dangky->id }}</td>
                            <td class="px-6 py-4">{{ $dangky->sinhvien_id }}</td>
                            <td class="px-6 py-4">{{ $dangky->phong_id }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-gray-500" colspan="3">Không có đăng ký chờ xử lý.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="text-sm font-semibold text-gray-900">Báo hỏng gần nhất (Chờ sửa)</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Mã báo hỏng</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Phòng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($danhsachbaohonggannhat as $baohong)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900">#{{ $baohong->id }}</td>
                            <td class="px-6 py-4">{{ $baohong->sinhvien_id }}</td>
                            <td class="px-6 py-4">{{ $baohong->phong_id }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-gray-500" colspan="3">Không có báo hỏng chờ sửa.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

