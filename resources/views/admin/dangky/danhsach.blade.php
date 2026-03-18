@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Duyệt đăng ký</div>
        <div class="text-sm text-gray-500">Admin duyệt / từ chối đăng ký phòng.</div>
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        @foreach (['Tất cả', 'Chờ xử lý', 'Đã duyệt', 'Từ chối'] as $loai)
            <a href="{{ route('admin.duyetdangky', ['status' => $loai]) }}"
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
                    <th class="px-6 py-3">Loại đăng ký</th>
                    <th class="px-6 py-3">Phòng</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3">Ghi chú</th>
                    <th class="px-6 py-3 text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachdangky as $dangky)
                    @php
                        $sinhvien = $mapsinhvien[$dangky->sinhvien_id] ?? null;
                        $phong = $mapphong[$dangky->phong_id] ?? null;
                    @endphp
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $sinhvien?->masinhvien ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $dangky->loaidangky ?? 'Thuê phòng' }}
                        </td>
                        <td class="px-6 py-4">{{ $phong?->tenphong ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                {{ $dangky->trangthai === 'Đã duyệt' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $dangky->trangthai === 'Từ chối' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $dangky->trangthai === 'Chờ xử lý' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            ">
                                {{ $dangky->trangthai }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $dangky->ghichu }}</td>
                        <td class="px-6 py-4 text-right">
                            @if ($dangky->trangthai === 'Chờ xử lý')
                                <form method="POST" action="{{ route('admin.xulyduyetdangky', ['id' => $dangky->id]) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700">
                                        Duyệt
                                    </button>
                                </form>

                                <button type="button"
                                        data-modal-target="modal-tuchoi-{{ $dangky->id }}" data-modal-toggle="modal-tuchoi-{{ $dangky->id }}"
                                        class="ml-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                                    Từ chối
                                </button>
                            @else
                                <span class="text-gray-400">Đã xử lý</span>
                            @endif
                        </td>
                    </tr>

                    <div id="modal-tuchoi-{{ $dangky->id }}" tabindex="-1" aria-hidden="true"
                         class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
                        <div class="relative max-h-full w-full max-w-lg">
                            <div class="relative rounded-lg bg-white shadow">
                                <div class="flex items-start justify-between rounded-t border-b p-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Từ chối đăng ký</h3>
                                    <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900"
                                            data-modal-hide="modal-tuchoi-{{ $dangky->id }}">
                                        <span class="sr-only">Đóng</span>
                                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                                        </svg>
                                    </button>
                                </div>

                                <form class="p-4" method="POST" action="{{ route('admin.xulytuchoidangky', ['id' => $dangky->id]) }}">
                                    @csrf
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-gray-900">Ghi chú (lý do)</label>
                                        <textarea name="ghichu" rows="4"
                                                  class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                                  placeholder="Ví dụ: Phòng đã đủ người..."></textarea>
                                    </div>
                                    <button type="submit" class="mt-4 w-full rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-700">
                                        Xác nhận từ chối
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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

