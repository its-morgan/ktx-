@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="linear-page-title">Quản lý phòng</h1>
            <p class="linear-page-subtitle">Admin thêm/sửa/xóa phòng bằng modal.</p>
        </div>

        <form method="GET" action="{{ route('admin.quanlyphong') }}" class="flex items-center gap-2">
            <input name="q" value="{{ old('q', $tuKhoa ?? '') }}" type="text" placeholder="Tìm theo tên phòng" class="linear-input w-64" />
            <button type="submit" class="linear-btn-secondary">Tìm</button>
        </form>

        <button type="button" data-modal-target="modal-themphong" data-modal-toggle="modal-themphong" class="linear-btn-primary">Thêm phòng</button>
    </div>

    <div class="mb-6 rounded-lg border border-gray-200/70 bg-white p-1 shadow-none">
        <div class="flex h-10 overflow-hidden rounded-lg bg-gray-100">
            <a href="{{ route('admin.quanlyphong', array_merge(request()->query(), ['view' => 'table'])) }}" class="flex-1 text-center text-sm font-semibold transition-colors {{ $viewMode === 'table' ? 'bg-white text-[#121212]' : 'text-[#606060] hover:bg-gray-200' }} px-4 py-2">Danh sách</a>
            <a href="{{ route('admin.quanlyphong', array_merge(request()->query(), ['view' => 'grid'])) }}" class="flex-1 text-center text-sm font-semibold transition-colors {{ $viewMode === 'grid' ? 'bg-white text-[#121212]' : 'text-[#606060] hover:bg-gray-200' }} px-4 py-2">Sơ đồ phòng</a>
        </div>
    </div>

    @if ($viewMode === 'table')
        <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white shadow-none">
            <div class="overflow-x-auto">
                <table class="linear-table">
                    <thead>
                    <tr>
                        <th class="group cursor-pointer hover:bg-slate-100 transition-colors">
                            <div class="flex items-center gap-2">
                                Phòng
                                <svg class="h-3.5 w-3.5 text-zinc-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                        </th>
                        <th class="group cursor-pointer hover:bg-slate-100 transition-colors">
                            <div class="flex items-center gap-2">
                                Giới tính
                                <svg class="h-3.5 w-3.5 text-zinc-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                        </th>
                        <th class="group cursor-pointer hover:bg-slate-100 transition-colors">
                            <div class="flex items-center gap-2">
                                Số lượng
                                <svg class="h-3.5 w-3.5 text-zinc-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                        </th>
                        <th>Tình trạng</th>
                        <th class="group cursor-pointer hover:bg-slate-100 transition-colors">
                            <div class="flex items-center gap-2">
                                Giá
                                <svg class="h-3.5 w-3.5 text-zinc-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                        </th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse ($danhsachphong as $phong)
                        @php
                            $soluongdango = $soluongdango_theophong[$phong->id] ?? 0;
                            $daydu = $soluongdango >= (int) $phong->soluongtoida;
                            $phantram = $phong->soluongtoida > 0 ? min(100, round($soluongdango / $phong->soluongtoida * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $phong->tenphong }}</td>
                            <td class="px-4 py-3"><span class="linear-badge">{{ $phong->gioitinh }}</span></td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-slate-900">{{ $soluongdango }}/{{ $phong->soluongtoida }}</div>
                                <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $daydu ? 'bg-slate-800' : 'bg-brand-500' }}" style="width: {{ $phantram }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $daydu ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $daydu ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                                    {{ $daydu ? 'Đầy' : 'Còn chỗ' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-900 font-semibold tabular-nums">{{ number_format($phong->giaphong, 0, ',', '.') }}₫</td>
                            <td class="px-4 py-3">
                                <div class="linear-row-action flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.chitietphong', ['id' => $phong->id]) }}" class="linear-btn-secondary text-xs" aria-label="Xem chi tiết phòng {{ $phong->tenphong }}">Chi tiết</a>
                                    <button type="button" data-modal-target="modal-capnhatphong-{{ $phong->id }}" data-modal-toggle="modal-capnhatphong-{{ $phong->id }}" class="linear-btn-secondary text-xs" aria-label="Sửa phòng {{ $phong->tenphong }}">Sửa</button>
                                    <form method="POST" action="{{ route('admin.xoaphong', ['id' => $phong->id]) }}" class="inline" x-data="{ showConfirm: false }" @confirmed="$el.submit()">
                                        @csrf
                                        <button type="button" @click="showConfirm = true" class="linear-btn-danger text-xs" aria-label="Xóa phòng {{ $phong->tenphong }}">Xóa</button>
                                        <x-confirmation-modal 
                                            x-show="showConfirm" 
                                            @close="showConfirm = false"
                                            @confirmed="$el.closest('form').submit()"
                                            title="Xác nhận xóa phòng"
                                            message="Bạn có chắc muốn xóa phòng {{ $phong->tenphong }}?"
                                            confirm-text="Xóa"
                                            cancel-text="Hủy"
                                            type="danger"
                                        />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <x-empty-state
                                    title="Chưa có phòng"
                                    description="Danh sách phòng sẽ hiển thị tại đây sau khi thêm mới."
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
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($danhsachphong as $phong)
                @php
                    $soluongdango = $soluongdango_theophong[$phong->id] ?? 0;
                    $daydu = $soluongdango >= (int) $phong->soluongtoida;
                    $phantram = $phong->soluongtoida > 0 ? min(100, round($soluongdango / $phong->soluongtoida * 100)) : 0;
                    $isFemale = $phong->gioitinh === 'Nữ';
                @endphp
                <article class="flex flex-col rounded-lg border border-gray-200/70 bg-white p-4 transition hover:-translate-y-0.5 hover:bg-[#F7F7F8]">
                    <header class="mb-3 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-[#121212]">{{ $phong->tenphong }}</h2>
                        <span class="text-sm font-semibold {{ $isFemale ? 'text-[#606060]' : 'text-[#606060]' }}">{{ $isFemale ? '♀' : '♂' }}</span>
                    </header>

                    <div class="mb-3 space-y-2">
                        <p class="text-sm font-semibold text-[#121212]">Giới tính: <span class="font-bold">{{ $phong->gioitinh }}</span></p>
                        <p class="text-sm font-semibold text-[#121212]">Sức chứa: <span class="font-bold">{{ $soluongdango }}/{{ $phong->soluongtoida }}</span></p>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/70">
                            <div class="h-full rounded-full {{ $daydu ? 'bg-[#121212]' : 'bg-[#606060]' }}" style="<?php echo 'width: '.$phantram.'%;'; ?>"></div>
                        </div>
                        <p class="text-xs text-[#606060]">Đầy: <span class="font-semibold text-[#606060]">{{ $phantram }}%</span></p>
                        <p class="text-sm font-semibold {{ $daydu ? 'text-[#121212]' : 'text-[#606060]' }}">{{ $daydu ? 'Đầy' : 'Còn chỗ' }}</p>
                    </div>

                    <p class="text-sm font-semibold text-[#121212]">Giá: <span class="text-[#606060]">{{ number_format($phong->giaphong, 0, ',', '.') }}₫</span></p>
                    <p class="text-xs text-[#606060]">{{ $phong->mota }}</p>

                    <footer class="mt-auto pt-4">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.chitietphong', ['id' => $phong->id]) }}" class="flex-1 linear-btn-secondary text-xs text-center justify-center">Chi tiết</a>
                            <button type="button" data-modal-target="modal-capnhatphong-{{ $phong->id }}" data-modal-toggle="modal-capnhatphong-{{ $phong->id }}" class="flex-1 linear-btn-secondary text-xs text-center justify-center">Sửa</button>
                            <form method="POST" action="{{ route('admin.xoaphong', ['id' => $phong->id]) }}" class="flex-1" x-data="{ showConfirm: false }" @confirmed="$el.submit()">
                                @csrf
                                <button type="button" @click="showConfirm = true" class="w-full linear-btn-danger text-xs text-center justify-center" aria-label="Xóa phòng {{ $phong->tenphong }}">Xóa</button>
                                <x-confirmation-modal 
                                    x-show="showConfirm" 
                                    @close="showConfirm = false"
                                    @confirmed="$el.closest('form').submit()"
                                    title="Xác nhận xóa phòng"
                                    message="Bạn có chắc muốn xóa phòng {{ $phong->tenphong }}?"
                                    confirm-text="Xóa"
                                    cancel-text="Hủy"
                                    type="danger"
                                />
                            </form>
                        </div>
                    </footer>
                </article>
            @empty
                <div class="col-span-full">
                    <x-empty-state
                        title="Chưa có phòng"
                        description="Sơ đồ phòng sẽ hiển thị tại đây khi có dữ liệu."
                        actionLabel="Tải lại trang"
                        :actionHref="request()->fullUrl()"
                    />
                </div>
            @endforelse
        </div>
    @endif

    @push('modals')
        <div id="modal-themphong" tabindex="-1" aria-hidden="true" class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg">
                <div class="relative rounded-lg border border-gray-200/70 bg-white">
                    <div class="flex items-start justify-between rounded-t border-b p-4">
                        <h3 class="text-lg font-semibold text-[#121212]">Thêm phòng</h3>
                        <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-[#9A9A9A] hover:bg-[#F7F7F8] hover:text-[#121212]" data-modal-hide="modal-themphong" aria-label="Đóng hộp thoại thêm phòng">
                            <span class="sr-only">Đóng</span>
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                            </svg>
                        </button>
                    </div>

                    <form class="p-4" method="POST" action="{{ route('admin.themphong') }}">
                        @csrf
                        <div class="grid gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tên phòng</label>
                                    <input name="tenphong" value="{{ old('tenphong') }}" class="linear-input mt-1" required />
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tầng</label>
                                    <input name="tang" value="{{ old('tang') }}" class="linear-input mt-1" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Giá phòng</label>
                                    <input name="giaphong" value="{{ old('giaphong') }}" class="linear-input mt-1" required />
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Số lượng tối đa</label>
                                    <input name="soluongtoida" value="{{ old('soluongtoida') }}" class="linear-input mt-1" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Sức chứa</label>
                                    <input name="succhuamax" value="{{ old('succhuamax', old('soluongtoida')) }}" class="linear-input mt-1" required />
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Giới tính</label>
                                    <select name="gioitinh" class="linear-select mt-1" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="Nam" {{ old('gioitinh') === 'Nam' ? 'selected' : '' }}>Nam</option>
                                        <option value="Nữ" {{ old('gioitinh') === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Mô tả</label>
                                <textarea name="mota" rows="3" class="linear-textarea mt-1">{{ old('mota') }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 w-full rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Thêm</button>
                    </form>
                </div>
            </div>
        </div>
        @foreach ($danhsachphong as $phong)
            <div id="modal-capnhatphong-{{ $phong->id }}" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/40 p-4 md:p-6">
                <div class="mx-auto mt-12 w-full max-w-2xl rounded-lg border border-gray-200/70 bg-white p-6 shadow-none">
                    <div class="flex items-start justify-between border-b border-gray-200/70 pb-4">
                        <div>
                            <h3 class="text-xl font-bold text-[#121212]">Cập nhật phòng</h3>
                            <p class="text-sm text-[#606060]">Chỉnh sửa thông tin phòng và lưu thay đổi.</p>
                        </div>
                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200/70 text-[#606060] hover:bg-[#F7F7F8] hover:text-[#121212]" data-modal-hide="modal-capnhatphong-{{ $phong->id }}" aria-label="Đóng hộp thoại cập nhật phòng">
                            <span class="sr-only">Đóng</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/></svg>
                        </button>
                    </div>
                    <form class="mt-5 space-y-4" method="POST" action="{{ route('admin.capnhatphong', ['id' => $phong->id]) }}">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tên phòng</label>
                                <input name="tenphong" value="{{ $phong->tenphong }}" required class="linear-input mt-1" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Giới tính</label>
                                <select name="gioitinh" required class="linear-select mt-1">
                                    <option value="Nam" {{ $phong->gioitinh === 'Nam' ? 'selected' : '' }}>Nam</option>
                                    <option value="Nữ" {{ $phong->gioitinh === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tầng</label>
                                <input name="tang" value="{{ $phong->tang }}" required class="linear-input mt-1" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Giá phòng</label>
                                <input name="giaphong" value="{{ $phong->giaphong }}" required class="linear-input mt-1" />
                            </div>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Số lượng tối đa</label>
                                <input name="soluongtoida" value="{{ $phong->soluongtoida }}" required class="linear-input mt-1" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Sức chứa</label>
                                <input name="succhuamax" value="{{ $phong->succhuamax }}" required class="linear-input mt-1" />
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Mô tả</label>
                            <textarea name="mota" rows="3" class="linear-textarea mt-1">{{ $phong->mota }}</textarea>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" data-modal-hide="modal-capnhatphong-{{ $phong->id }}" class="linear-btn-secondary">Hủy</button>
                            <button type="submit" class="linear-btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endpush
@endsection
