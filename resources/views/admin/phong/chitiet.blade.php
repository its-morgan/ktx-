@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="text-2xl font-bold text-[#121212]">Chi tiết phòng {{ $phong->tenphong }}</div>
            <div class="text-sm text-[#606060]">Quản lý tài sản và thông tin phòng.</div>
        </div>
        <a href="{{ route('admin.quanlyphong') }}" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm">Quay lại</a>
    </div>

    <div class="mb-6 rounded-lg border border-gray-200/70 bg-white p-4">
        <div class="text-sm text-[#606060]">Thông tin phòng</div>
        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-3">
            <div class="rounded-lg border border-gray-100 bg-[#F7F7F8] p-3">
                <div class="text-xs text-[#606060]">Giá phòng</div>
                <div class="text-lg font-semibold text-[#121212]">{{ number_format($phong->giaphong) }} đ</div>
            </div>
            <div class="rounded-lg border border-gray-100 bg-[#F7F7F8] p-3">
                <div class="text-xs text-[#606060]">Số lượng tối đa</div>
                <div class="text-lg font-semibold text-[#121212]">{{ $phong->soluongtoida }}</div>
            </div>
            <div class="rounded-lg border border-gray-100 bg-[#F7F7F8] p-3">
                <div class="text-xs text-[#606060]">Mô tả</div>
                <div class="text-lg text-[#121212]">{{ $phong->mota }}</div>
            </div>
        </div>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200/70 bg-white p-4">
        <form method="POST" action="{{ route('admin.themtaisan', ['id' => $phong->id]) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
            @csrf
            <input type="text" name="tentaisan" required placeholder="Tên tài sản" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
            <input type="number" name="soluong" required min="1" value="1" placeholder="Số lượng" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
            <input type="text" name="tinhtrang" required value="Đang sử dụng" placeholder="Tình trạng" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
            <button type="submit" class="rounded-lg bg-black px-3 py-2 text-sm font-medium text-white">Thêm tài sản</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Tài sản</th>
                        <th class="px-6 py-3">Số lượng</th>
                        <th class="px-6 py-3">Tình trạng</th>
                        <th class="px-6 py-3 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taisan as $item)
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4">{{ $item->tentaisan }}</td>
                            <td class="px-6 py-4">{{ $item->soluong }}</td>
                            <td class="px-6 py-4">{{ $item->tinhtrang }}</td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" data-modal-target="modal-suataisan-{{ $item->id }}" data-modal-toggle="modal-suataisan-{{ $item->id }}" class="rounded-lg border border-gray-200/80 bg-white px-3 py-2 text-sm font-medium text-[#606060] hover:bg-[#F7F7F8]">Sửa</button>
                                <form method="POST" action="{{ route('admin.xoataisan', ['id' => $phong->id, 'taisanId' => $item->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="ml-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700" onclick="return confirm('Xóa tài sản?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4" colspan="4">
                                <x-empty-state
                                    title="Chưa có tài sản"
                                    description="Tài sản trong phòng sẽ hiển thị tại đây sau khi thêm mới."
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
    @push('modals')
        @foreach($taisan as $item)
            <div id="modal-suataisan-{{ $item->id }}" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                <div class="w-full max-w-lg rounded-lg bg-white p-5">
                    <div class="mb-4 flex items-center justify-between border-b pb-2">
                        <h3 class="text-lg font-semibold">Sửa tài sản</h3>
                        <button type="button" class="text-[#606060] hover:text-[#121212]" data-modal-hide="modal-suataisan-{{ $item->id }}" aria-label="Đóng hộp thoại sửa tài sản">Đóng</button>
                    </div>
                    <form method="POST" action="{{ route('admin.capnhattaisan', ['id' => $phong->id, 'taisanId' => $item->id]) }}">
                        @csrf
                        <div class="grid grid-cols-1 gap-3">
                            <input name="tentaisan" value="{{ $item->tentaisan }}" required class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
                            <input name="soluong" type="number" min="1" value="{{ $item->soluong }}" required class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
                            <input name="tinhtrang" value="{{ $item->tinhtrang }}" required class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
                            <button type="submit" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endpush
@endsection
