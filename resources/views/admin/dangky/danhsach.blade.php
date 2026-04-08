@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Duyệt đăng ký</div>
        <div class="text-sm text-[#606060]">Admin duyệt / từ chối đăng ký phòng.</div>
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        @foreach (['Tất cả', 'Chờ xử lý', 'Đã duyệt', 'Từ chối'] as $loai)
            <a href="{{ route('admin.duyetdangky', ['status' => $loai]) }}"
               class="rounded-lg px-3 py-2 text-sm font-medium {{ (isset($status) && $status === $loai) || (!isset($status) && $loai === 'Tất cả') ? 'bg-gray-900 text-white' : 'bg-gray-100 text-[#606060]' }}">
                {{ $loai }}
            </a>
        @endforeach
    </div>

    @php
        $mapsinhvien = $danhsachsinhvien->keyBy('id');
        $mapphong = $danhsachphong->keyBy('id');
    @endphp

    <x-table class="w-full text-left text-sm text-[#606060]">
        <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
        <tr>
            <th class="px-6 py-3">Sinh viên</th>
            <th class="px-6 py-3">Loại đăng ký</th>
            <th class="px-6 py-3">Phòng</th>
            <th class="px-6 py-3">Trạng thái</th>
            <th class="px-6 py-3">Ghi chú</th>
            <th class="px-6 py-3 text-right">Thao tác</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
                @forelse ($danhsachdangky as $dangky)
                    @php
                        $sinhvien = $mapsinhvien[$dangky->sinhvien_id] ?? null;
                        $phong = $mapphong[$dangky->phong_id] ?? null;
                    @endphp
                    <tr class="border-t border-gray-200/70 hover:bg-[#F7F7F8] transition">
                        <td class="px-6 py-4 font-medium text-[#121212]">
                            {{ $sinhvien?->masinhvien ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 font-medium text-[#121212]">
                            {{ $dangky->loaidangky ?? 'Thuê phòng' }}
                        </td>
                        <td class="px-6 py-4">{{ $phong?->tenphong ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $badgeType = match ($dangky->trangthai) {
                                    'Đã duyệt' => 'success',
                                    'Từ chối' => 'danger',
                                    'Chờ xử lý' => 'warning',
                                        default => 'info',
                                };
                            @endphp
                            <x-badge type="{{ $badgeType }}" :text="$dangky->trangthai" />
                        </td>
                        <td class="px-6 py-4">{{ $dangky->ghichu }}</td>
                        <td class="px-6 py-4 text-right">
                            @if ($dangky->trangthai === 'Chờ xử lý')
                                <form method="POST" action="{{ route('admin.xulyduyetdangky', ['id' => $dangky->id]) }}" class="inline">
                                    @csrf
                                    <input type="date" name="ngay_het_han" value="{{ now()->addMonths(5)->format('Y-m-d') }}" class="rounded-lg border border-gray-200/80 p-1 text-sm" />
                                    <button type="submit"
                                            class="ml-2 rounded-lg bg-black px-3 py-2 text-sm font-medium text-white hover:bg-[#1C1C1C]">
                                        Duyệt
                                    </button>
                                </form>

                                <button type="button"
                                        data-modal-target="modal-tuchoi-{{ $dangky->id }}" data-modal-toggle="modal-tuchoi-{{ $dangky->id }}"
                                        class="ml-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                                    Từ chối
                                </button>
                            @else
                                <span class="text-[#9A9A9A]">Đã xử lý</span>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8">
                                <x-empty-state
                                    title="Chưa có đăng ký nào"
                                    description="Danh sách đăng ký của sinh viên sẽ hiển thị tại đây."
                                    actionLabel="Tải lại trang"
                                    :actionHref="request()->fullUrl()"
                                />
                        </td>
                    </tr>
                @endforelse
        </tbody>
    </x-table>
@push('modals')
    @foreach ($danhsachdangky as $dangky)
        <div id="modal-tuchoi-{{ $dangky->id }}" tabindex="-1" aria-hidden="true"
             class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg">
                <div class="relative rounded-lg border border-gray-200/70 bg-white">
                    <div class="flex items-start justify-between rounded-t border-b p-4">
                        <h3 class="text-lg font-semibold text-[#121212]">Từ chối đăng ký</h3>
                        <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-[#9A9A9A] hover:bg-[#F7F7F8] hover:text-[#121212]"
                                data-modal-hide="modal-tuchoi-{{ $dangky->id }}" aria-label="Đóng hộp thoại từ chối đăng ký">
                            <span class="sr-only">Đóng</span>
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                            </svg>
                        </button>
                    </div>

                    <form class="p-4" method="POST" action="{{ route('admin.xulytuchoidangky', ['id' => $dangky->id]) }}">
                        @csrf
                        <div>
                            <label class="mb-2 block text-sm font-medium text-[#121212]">Ghi chú (lý do)</label>
                            <textarea name="ghichu" rows="4"
                                      class="block w-full rounded-lg border border-gray-200/80 p-2.5 text-sm text-[#121212] focus:border-gray-900 focus:ring-gray-900"
                                      placeholder="Ví dụ: Phòng đã đủ người..."></textarea>
                        </div>
                        <button type="submit" class="mt-4 w-full rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-700">
                            Xác nhận từ chối
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endpush
@endsection

