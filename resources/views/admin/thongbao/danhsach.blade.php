@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Quản lý thông báo</div>
        <div class="text-sm text-gray-500">Thêm, sửa hoặc xóa thông báo cho sinh viên.</div>
    </div>

    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
        <form method="POST" action="{{ route('admin.themthongbao') }}" class="space-y-3">
            @csrf
            <div class="grid gap-3 md:grid-cols-3">
                <input name="tieude" type="text" placeholder="Tiêu đề" value="{{ old('tieude') }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm w-full" required>
                <input name="ngaydang" type="date" value="{{ old('ngaydang', now()->format('Y-m-d')) }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm w-full" required>
                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Thêm thông báo</button>
            </div>
            <textarea name="noidung" placeholder="Nội dung thông báo" rows="3"
                      class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>{{ old('noidung') }}</textarea>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Tiêu đề</th>
                    <th class="px-6 py-3">Nội dung</th>
                    <th class="px-6 py-3">Ngày đăng</th>
                    <th class="px-6 py-3 text-right">Hành động</th>
                </tr>
                </thead>
                <tbody>
                @forelse($thongbao as $item)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $item->tieude }}</td>
                        <td class="px-6 py-3">{{ $item->noidung }}</td>
                        <td class="px-6 py-3">{{ $item->ngaydang }}</td>
                        <td class="px-6 py-3 text-right">
                            <button type="button" data-modal-target="modal-capnhat-{{ $item->id }}" data-modal-toggle="modal-capnhat-{{ $item->id }}" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100">Sửa</button>
                            <form method="POST" action="{{ route('admin.xoathongbao', ['id' => $item->id]) }}" class="inline-block">
                                @csrf
                                <button type="submit" class="ml-2 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">Xóa</button>
                            </form>
                        </td>
                    </tr>

                    <div id="modal-capnhat-{{ $item->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                        <div class="w-full max-w-lg rounded-lg bg-white p-5">
                            <h3 class="mb-4 text-lg font-semibold">Cập nhật thông báo</h3>
                            <form method="POST" action="{{ route('admin.capnhatthongbao', ['id' => $item->id]) }}" class="space-y-3">
                                @csrf
                                <input name="tieude" type="text" value="{{ $item->tieude }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                                <input name="ngaydang" type="date" value="{{ $item->ngaydang }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                                <textarea name="noidung" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>{{ $item->noidung }}</textarea>
                                <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Lưu</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr class="border-t border-gray-200">
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Chưa có thông báo.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
