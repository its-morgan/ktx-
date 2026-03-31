@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý phòng</h1>
            <p class="text-sm text-gray-500">Admin thêm/sửa/xóa phòng bằng modal.</p>
        </div>

        <form method="GET" action="{{ route('admin.quanlyphong') }}" class="flex items-center gap-2">
            <input name="q" value="{{ old('q', $tuKhoa ?? '') }}" type="text" placeholder="Tìm theo tên phòng" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-gray-200 focus:border-gray-400" />
            <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-800">Tìm</button>
        </form>

        <button type="button" data-modal-target="modal-themphong" data-modal-toggle="modal-themphong" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Thêm phòng</button>
    </div>

    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
        <div class="flex h-10 overflow-hidden rounded-lg bg-gray-100">
            <a href="{{ route('admin.quanlyphong', array_merge(request()->query(), ['view' => 'table'])) }}" class="flex-1 text-center text-sm font-semibold transition-colors {{ $viewMode === 'table' ? 'bg-white text-gray-900' : 'text-gray-600 hover:bg-gray-200' }} px-4 py-2">Danh sách</a>
            <a href="{{ route('admin.quanlyphong', array_merge(request()->query(), ['view' => 'grid'])) }}" class="flex-1 text-center text-sm font-semibold transition-colors {{ $viewMode === 'grid' ? 'bg-white text-gray-900' : 'text-gray-600 hover:bg-gray-200' }} px-4 py-2">Sơ đồ phòng</a>
        </div>
    </div>

    @if ($viewMode === 'table')
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Phòng</th>
                        <th class="px-4 py-3 text-left font-semibold">Giới tính</th>
                        <th class="px-4 py-3 text-left font-semibold">Số lượng</th>
                        <th class="px-4 py-3 text-left font-semibold">Tình trạng</th>
                        <th class="px-4 py-3 text-left font-semibold">Giá</th>
                        <th class="px-4 py-3 text-right font-semibold">Thao tác</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse ($danhsachphong as $phong)
                        @php
                            $soluongdango = $soluongdango_theophong[$phong->id] ?? 0;
                            $daydu = $soluongdango >= (int) $phong->soluongtoida;
                            $phantram = $phong->soluongtoida > 0 ? min(100, round($soluongdango / $phong->soluongtoida * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $phong->tenphong }}</td>
                            <td class="px-4 py-3"><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $phong->gioitinh === 'Nữ' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">{{ $phong->gioitinh }}</span></td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">{{ $soluongdango }}/{{ $phong->soluongtoida }}</div>
                                <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full {{ $daydu ? 'bg-red-500' : 'bg-green-500' }}" style="<?php echo 'width: '.$phantram.'%;'; ?>"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $daydu ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $daydu ? 'Đầy' : 'Còn chỗ' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 font-semibold">{{ number_format($phong->giaphong, 0, ',', '.') }}₫</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.chitietphong', ['id' => $phong->id]) }}" class="rounded-md border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100">Chi tiết</a>
                                    <button type="button" data-modal-target="modal-capnhatphong-{{ $phong->id }}" data-modal-toggle="modal-capnhatphong-{{ $phong->id }}" class="rounded-md border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100">Sửa</button>
                                    <form method="POST" action="{{ route('admin.xoaphong', ['id' => $phong->id]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700" onclick="return confirm('Bạn có chắc muốn xóa phòng này không?')">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">Chưa có phòng.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($danhsachphong as $phong)
                @php
                    $soluongdango = $soluongdango_theophong[$phong->id] ?? 0;
                    $daydu = $soluongdango >= (int) $phong->soluongtoida;
                    $phantram = $phong->soluongtoida > 0 ? min(100, round($soluongdango / $phong->soluongtoida * 100)) : 0;
                    $isFemale = $phong->gioitinh === 'Nữ';
                @endphp
                <article class="flex flex-col rounded-2xl border p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $isFemale ? 'border-pink-200 bg-pink-50/80' : 'border-blue-200 bg-blue-50/80' }}">
                    <header class="mb-3 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">{{ $phong->tenphong }}</h2>
                        <span class="text-sm font-semibold {{ $isFemale ? 'text-pink-600' : 'text-blue-600' }}">{{ $isFemale ? '♀' : '♂' }}</span>
                    </header>

                    <div class="mb-3 space-y-2">
                        <p class="text-sm font-semibold text-gray-800">Giới tính: <span class="font-bold">{{ $phong->gioitinh }}</span></p>
                        <p class="text-sm font-semibold text-gray-800">Sức chứa: <span class="font-bold">{{ $soluongdango }}/{{ $phong->soluongtoida }}</span></p>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/70">
                            <div class="h-full rounded-full {{ $daydu ? 'bg-red-500' : 'bg-emerald-500' }}" style="<?php echo 'width: '.$phantram.'%;'; ?>"></div>
                        </div>
                        <p class="text-xs text-gray-500">Đầy: <span class="font-semibold text-gray-700">{{ $phantram }}%</span></p>
                        <p class="text-sm font-semibold {{ $daydu ? 'text-red-600' : 'text-emerald-600' }}">{{ $daydu ? 'Đầy' : 'Còn chỗ' }}</p>
                    </div>

                    <p class="text-sm font-semibold text-gray-800">Giá: <span class="text-emerald-700">{{ number_format($phong->giaphong, 0, ',', '.') }}₫</span></p>
                    <p class="text-xs text-gray-500">{{ $phong->mota }}</p>

                    <footer class="mt-auto pt-4">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.chitietphong', ['id' => $phong->id]) }}" class="flex-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-center text-xs font-semibold text-blue-700 hover:bg-blue-100">Chi tiết</a>
                            <button type="button" data-modal-target="modal-capnhatphong-{{ $phong->id }}" data-modal-toggle="modal-capnhatphong-{{ $phong->id }}" class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-center text-xs font-semibold text-gray-700 hover:bg-gray-100">Sửa</button>
                            <form method="POST" action="{{ route('admin.xoaphong', ['id' => $phong->id]) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-red-600 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-red-700" onclick="return confirm('Bạn có chắc muốn xóa phòng này không?')">Xóa</button>
                            </form>
                        </div>
                    </footer>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-gray-300 bg-white p-6 text-center text-gray-500">Chưa có phòng.</div>
            @endforelse
        </div>
    @endif

    @foreach ($danhsachphong as $phong)
        <div id="modal-capnhatphong-{{ $phong->id }}" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/40 p-4 md:p-6">
            <div class="mx-auto mt-12 w-full max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between border-b border-gray-200 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Cập nhật phòng</h3>
                        <p class="text-sm text-gray-500">Chỉnh sửa thông tin phòng và lưu thay đổi.</p>
                    </div>
                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-gray-900" data-modal-hide="modal-capnhatphong-{{ $phong->id }}">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/></svg>
                    </button>
                </div>
                <form class="mt-5 space-y-4" method="POST" action="{{ route('admin.capnhatphong', ['id' => $phong->id]) }}">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tên phòng</label>
                            <input name="tenphong" value="{{ $phong->tenphong }}" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-300" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Giới tính</label>
                            <select name="gioitinh" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-300">
                                <option value="Nam" {{ $phong->gioitinh === 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ $phong->gioitinh === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Giá phòng</label>
                            <input name="giaphong" value="{{ $phong->giaphong }}" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-300" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Số lượng tối đa</label>
                            <input name="soluongtoida" value="{{ $phong->soluongtoida }}" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-300" />
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Mô tả</label>
                        <textarea name="mota" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-300">{{ $phong->mota }}</textarea>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" data-modal-hide="modal-capnhatphong-{{ $phong->id }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Hủy</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <div id="modal-themphong" tabindex="-1" aria-hidden="true" class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
        <div class="relative max-h-full w-full max-w-lg">
            <div class="relative rounded-lg bg-white shadow">
                <div class="flex items-start justify-between rounded-t border-b p-4">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm phòng</h3>
                    <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900" data-modal-hide="modal-themphong">
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
                            <input name="tenphong" value="{{ old('tenphong') }}" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Giá phòng</label>
                                <input name="giaphong" value="{{ old('giaphong') }}" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Số lượng tối đa</label>
                                <input name="soluongtoida" value="{{ old('soluongtoida') }}" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-900">Giới tính</label>
                            <select name="gioitinh" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" required>
                                <option value="">-- Chọn định hướng giới tính --</option>
                                <option value="Nam" {{ old('gioitinh') === 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gioitinh') === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-900">Mô tả</label>
                            <textarea name="mota" rows="3" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900">{{ old('mota') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="mt-4 w-full rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Thêm</button>
                </form>
            </div>
        </div>
    </div>
@endsection
