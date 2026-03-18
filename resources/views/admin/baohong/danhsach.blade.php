@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Quản lý báo hỏng</div>
        <div class="text-sm text-gray-500">Admin cập nhật trạng thái từ “Chờ sửa” sang “Đã xong”.</div>
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        @foreach (['Tất cả', 'Chờ sửa', 'Đã xong'] as $loai)
            <a href="{{ route('admin.quanlybaohong', ['status' => $loai]) }}"
               class="rounded-lg px-3 py-2 text-sm font-medium {{ (isset($status) && $status === $loai) || (!isset($status) && $loai === 'Tất cả') ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700' }}">
                {{ $loai }}
            </a>
        @endforeach
    </div>

    @php
        $mapsinhvien = $danhsachsinhvien->keyBy('id');
        $mapphong = $danhsachphong->keyBy('id');
    @endphp

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Sinh viên</th>
                    <th class="px-6 py-3">Phòng</th>
                    <th class="px-6 py-3">Mô tả</th>
                    <th class="px-6 py-3">Ảnh</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3 text-right">Cập nhật</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachbaohong as $baohong)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $mapsinhvien[$baohong->sinhvien_id]->masinhvien ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $mapphong[$baohong->phong_id]->tenphong ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $baohong->mota }}</td>
                        <td class="px-6 py-4">
                            @if ($baohong->anhminhhoa)
                                <a class="text-gray-900 underline" href="{{ asset($baohong->anhminhhoa) }}" target="_blank">Xem ảnh</a>
                            @else
                                <span class="text-gray-400">Không có</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $baohong->trangthai }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.capnhatbaohong', ['id' => $baohong->id]) }}" class="flex items-center justify-end gap-2">
                                @csrf
                                <select name="trangthai" class="rounded-lg border border-gray-300 p-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900">
                                    <option value="Chờ sửa" {{ $baohong->trangthai === 'Chờ sửa' ? 'selected' : '' }}>Chờ sửa</option>
                                    <option value="Đã xong" {{ $baohong->trangthai === 'Đã xong' ? 'selected' : '' }}>Đã xong</option>
                                </select>
                                <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                    Lưu
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-center text-gray-400" colspan="6">Hiện tại chưa có dữ liệu nào trong danh sách này.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

