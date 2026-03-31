@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="text-2xl font-bold text-gray-900">Chi tiết phòng {{ $phong->tenphong }}</div>
            <div class="text-sm text-gray-500">Quản lý tài sản và thông tin phòng.</div>
        </div>
        <a href="{{ route('admin.quanlyphong') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">Quay lại</a>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4">
        <div class="text-sm text-gray-500">Thông tin phòng</div>
        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-3">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                <div class="text-xs text-gray-500">Giá phòng</div>
                <div class="text-lg font-semibold text-gray-900">{{ number_format($phong->giaphong) }} đ</div>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                <div class="text-xs text-gray-500">Số lượng tối đa</div>
                <div class="text-lg font-semibold text-gray-900">{{ $phong->soluongtoida }}</div>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                <div class="text-xs text-gray-500">Mô tả</div>
                <div class="text-lg text-gray-900">{{ $phong->mota }}</div>
            </div>
        </div>
    </div>

    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
        <form method="POST" action="{{ route('admin.themtaisan', ['id' => $phong->id]) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
            @csrf
            <input type="text" name="tentaisan" required placeholder="Tên tài sản" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <input type="number" name="soluong" required min="1" value="1" placeholder="Số lượng" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <input type="text" name="tinhtrang" required value="Đang sử dụng" placeholder="Tình trạng" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white">Thêm tài sản</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Tài sản</th>
                        <th class="px-6 py-3">Số lượng</th>
                        <th class="px-6 py-3">Tình trạng</th>
                        <th class="px-6 py-3 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taisan as $item)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4">{{ $item->tentaisan }}</td>
                            <td class="px-6 py-4">{{ $item->soluong }}</td>
                            <td class="px-6 py-4">{{ $item->tinhtrang }}</td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" data-modal-target="modal-suataisan-{{ $item->id }}" data-modal-toggle="modal-suataisan-{{ $item->id }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Sửa</button>
                                <form method="POST" action="{{ route('admin.xoataisan', ['id' => $phong->id, 'taisanId' => $item->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="ml-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700" onclick="return confirm('Xóa tài sản?')">Xóa</button>
                                </form>
                            </td>
                        </tr>

                        <div id="modal-suataisan-{{ $item->id }}" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                            <div class="w-full max-w-lg rounded-lg bg-white p-5">
                                <div class="mb-4 flex items-center justify-between border-b pb-2">
                                    <h3 class="text-lg font-semibold">Sửa tài sản</h3>
                                    <button type="button" class="text-gray-500 hover:text-gray-900" data-modal-hide="modal-suataisan-{{ $item->id }}">Đóng</button>
                                </div>
                                <form method="POST" action="{{ route('admin.capnhattaisan', ['id' => $phong->id, 'taisanId' => $item->id]) }}">
                                    @csrf
                                    <div class="grid grid-cols-1 gap-3">
                                        <input name="tentaisan" value="{{ $item->tentaisan }}" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                                        <input name="soluong" type="number" min="1" value="{{ $item->soluong }}" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                                        <input name="tinhtrang" value="{{ $item->tinhtrang }}" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white">Cập nhật</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-center text-gray-400" colspan="4">Chưa có tài sản.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
