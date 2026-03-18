@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <div class="text-2xl font-bold text-gray-900">Quản lý phòng</div>
            <div class="text-sm text-gray-500">Admin thêm/sửa/xóa phòng bằng modal.</div>
        </div>

        <form method="GET" action="{{ route('admin.quanlyphong') }}" class="flex items-center gap-2">
            <input name="q" value="{{ old('q', $tuKhoa ?? '') }}" type="text" placeholder="Tìm theo tên phòng"
                   class="rounded-lg border border-gray-300 p-2 text-sm" />
            <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">Tìm</button>
        </form>

        <button type="button"
                data-modal-target="modal-themphong" data-modal-toggle="modal-themphong"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Thêm phòng
        </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Tên phòng</th>
                    <th class="px-6 py-3">Giá phòng</th>
                    <th class="px-6 py-3">Số lượng (đang ở / tối đa)</th>
                    <th class="px-6 py-3">Mô tả</th>
                    <th class="px-6 py-3 text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachphong as $phong)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $phong->tenphong }}</td>
                        <td class="px-6 py-4">{{ number_format($phong->giaphong) }} đ</td>
                        <td class="px-6 py-4">
                            @php
                                $soluongdango = $soluongdango_theophong[$phong->id] ?? 0;
                            @endphp
                            <span class="font-semibold text-gray-900">{{ $soluongdango }}</span>
                            <span class="text-gray-400">/</span>
                            <span class="text-gray-700">{{ $phong->soluongtoida }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $phong->mota }}</td>
                        <td class="px-6 py-4 text-right">
                            <button type="button"
                                    data-modal-target="modal-capnhatphong-{{ $phong->id }}" data-modal-toggle="modal-capnhatphong-{{ $phong->id }}"
                                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Sửa
                            </button>

                            <form method="POST" action="{{ route('admin.xoaphong', ['id' => $phong->id]) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="ml-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
                                        onclick="return confirm('Bạn có chắc muốn xóa phòng này không?')">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>

                    <div id="modal-capnhatphong-{{ $phong->id }}" tabindex="-1" aria-hidden="true"
                         class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
                        <div class="relative max-h-full w-full max-w-lg">
                            <div class="relative rounded-lg bg-white shadow">
                                <div class="flex items-start justify-between rounded-t border-b p-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Cập nhật phòng</h3>
                                    <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900"
                                            data-modal-hide="modal-capnhatphong-{{ $phong->id }}">
                                        <span class="sr-only">Đóng</span>
                                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                                        </svg>
                                    </button>
                                </div>

                                <form class="p-4" method="POST" action="{{ route('admin.capnhatphong', ['id' => $phong->id]) }}">
                                    @csrf
                                    <div class="grid gap-4">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-900">Tên phòng</label>
                                            <input name="tenphong" value="{{ $phong->tenphong }}" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="mb-2 block text-sm font-medium text-gray-900">Giá phòng</label>
                                                <input name="giaphong" value="{{ $phong->giaphong }}" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                                            </div>
                                            <div>
                                                <label class="mb-2 block text-sm font-medium text-gray-900">Số lượng tối đa</label>
                                                <input name="soluongtoida" value="{{ $phong->soluongtoida }}" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                                            </div>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-900">Mô tả</label>
                                            <textarea name="mota" rows="3" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900">{{ $phong->mota }}</textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="mt-4 w-full rounded-lg bg-gray-900 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-gray-800">
                                        Lưu
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-gray-500" colspan="5">Chưa có phòng.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-themphong" tabindex="-1" aria-hidden="true"
         class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
        <div class="relative max-h-full w-full max-w-lg">
            <div class="relative rounded-lg bg-white shadow">
                <div class="flex items-start justify-between rounded-t border-b p-4">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm phòng</h3>
                    <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900"
                            data-modal-hide="modal-themphong">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                        </svg>
                    </button>
                </div>

                <form class="p-4" method="POST" action="{{ route('admin.themphong') }}">
                    @csrf
                    <div class="grid gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-900">Tên phòng</label>
                            <input name="tenphong" value="{{ old('tenphong') }}" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Giá phòng</label>
                                <input name="giaphong" value="{{ old('giaphong') }}" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Số lượng tối đa</label>
                                <input name="soluongtoida" value="{{ old('soluongtoida') }}" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-900">Mô tả</label>
                            <textarea name="mota" rows="3" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900">{{ old('mota') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="mt-4 w-full rounded-lg bg-gray-900 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-gray-800">
                        Thêm
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

