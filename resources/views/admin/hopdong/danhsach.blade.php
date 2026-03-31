@extends('admin.layouts.quantri')

@section('noidung')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-bold">Danh sách hợp đồng</h1>

    <form action="{{ route('admin.quanlyhopdong') }}" method="get" class="flex flex-wrap gap-2">
        <input type="text" name="timkiem" value="{{ old('timkiem', $timkiem) }}" placeholder="Tìm mã SV" class="rounded border px-3 py-2" />
        <select name="trangthai" class="rounded border px-3 py-2">
            <option value="Tất cả" {{ $trangthai == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
            <option value="Đang hiệu lực" {{ $trangthai == 'Đang hiệu lực' ? 'selected' : '' }}>Đang hiệu lực</option>
            <option value="Đã hết hạn" {{ $trangthai == 'Đã hết hạn' ? 'selected' : '' }}>Đã hết hạn</option>
            <option value="Đã thanh lý" {{ $trangthai == 'Đã thanh lý' ? 'selected' : '' }}>Đã thanh lý</option>
        </select>
        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Lọc</button>
    </form>
</div>

<div class="overflow-x-auto rounded border bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left font-medium text-gray-700">#</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Mã SV</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Tên SV</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Phòng</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">BĐ</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">KT</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Giá</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Trạng thái</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($danhsachhopdong as $hopdong)
                <tr>
                    <td class="px-4 py-2">{{ $hopdong->id }}</td>
                    <td class="px-4 py-2">{{ $hopdong->sinhvien->masinhvien ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $hopdong->sinhvien->taikhoan->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $hopdong->phong->tenphong ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $hopdong->ngay_bat_dau }}</td>
                    <td class="px-4 py-2">{{ $hopdong->ngay_ket_thuc }}</td>
                    <td class="px-4 py-2">{{ number_format($hopdong->giaphong_luc_ky) }}</td>
                    <td class="px-4 py-2">{{ $hopdong->trang_thai }}</td>
                    <td class="px-4 py-2 space-x-1">
                        <!-- Chi tiết bouton -->
                        <button type="button" data-modal-target="modal-chi-tiet-{{ $hopdong->id }}" data-modal-toggle="modal-chi-tiet-{{ $hopdong->id }}" class="rounded bg-gray-600 px-2 py-1 text-white">Chi tiết</button>

                        @if ($hopdong->trang_thai === 'Đang hiệu lực')
                            <button type="button" data-modal-target="modal-gia-han-{{ $hopdong->id }}" data-modal-toggle="modal-gia-han-{{ $hopdong->id }}" class="rounded bg-amber-600 px-2 py-1 text-white">Gia hạn</button>
                        @endif

                        @if ($hopdong->trang_thai !== 'Đã thanh lý')
                            <form action="{{ route('admin.hopdong.thanhly', $hopdong->id) }}" method="post" class="inline">
                                @csrf
                                <button type="submit" class="rounded bg-red-600 px-2 py-1 text-white" onclick="return confirm('Xác nhận thanh lý?')">Thanh lý</button>
                            </form>
                        @endif
                    </td>
                </tr>

                <!-- Modal Chi tiết -->
                <div id="modal-chi-tiet-{{ $hopdong->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30">
                    <div class="w-full max-w-lg rounded bg-white p-6">
                        <h3 class="mb-4 text-xl font-bold">Thông tin hợp đồng #{{ $hopdong->id }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li><strong>Sinh viên:</strong> {{ $hopdong->sinhvien->taikhoan->name ?? '-' }} ({{ $hopdong->sinhvien->masinhvien ?? '-' }})</li>
                            <li><strong>Phòng:</strong> {{ $hopdong->phong->tenphong ?? '-' }}</li>
                            <li><strong>Ngày bắt đầu:</strong> {{ $hopdong->ngay_bat_dau }}</li>
                            <li><strong>Ngày kết thúc:</strong> {{ $hopdong->ngay_ket_thuc }}</li>
                            <li><strong>Giá phòng tại ký:</strong> {{ number_format($hopdong->giaphong_luc_ky) }} đ</li>
                            <li><strong>Trạng thái:</strong> {{ $hopdong->trang_thai }}</li>
                            <li><strong>Ghi chú:</strong> {{ $hopdong->ghichu ?? '-' }}</li>
                        </ul>
                        <div class="mt-5 text-right">
                            <button type="button" data-modal-hide="modal-chi-tiet-{{ $hopdong->id }}" data-modal-toggle="modal-chi-tiet-{{ $hopdong->id }}" class="rounded bg-gray-500 px-4 py-2 text-white">Đóng</button>
                        </div>
                    </div>
                </div>

                <!-- Modal gia hạn -->
                <div id="modal-gia-han-{{ $hopdong->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30">
                    <div class="w-full max-w-md rounded bg-white p-6">
                        <h3 class="mb-4 text-xl font-bold">Gia hạn hợp đồng #{{ $hopdong->id }}</h3>
                        <form action="{{ route('admin.hopdong.giahan', $hopdong->id) }}" method="post">
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="ngay_ket_thuc" value="Ngày kết thúc mới" />
                                <x-text-input id="ngay_ket_thuc" class="mt-1 block w-full" type="date" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc', $hopdong->ngay_ket_thuc) }}" required autofocus />
                                <x-input-error :messages="$errors->get('ngay_ket_thuc')" class="mt-2" />
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" data-modal-hide="modal-gia-han-{{ $hopdong->id }}" data-modal-toggle="modal-gia-han-{{ $hopdong->id }}" class="rounded border px-4 py-2">Hủy</button>
                                <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">Chưa có hợp đồng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $danhsachhopdong->withQueryString()->links() }}</div>
@endsection
