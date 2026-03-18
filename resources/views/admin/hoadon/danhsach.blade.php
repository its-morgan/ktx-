@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex items-end justify-between">
        <div>
            <div class="text-2xl font-bold text-gray-900">Quản lý hóa đơn</div>
            <div class="text-sm text-gray-500">
                Nhập chỉ số điện/nước và hệ thống tự tính tiền. Đơn giá điện: {{ number_format($dongiadien) }} đ, nước: {{ number_format($dongianuoc) }} đ.
            </div>
        </div>

        <button type="button"
                data-modal-target="modal-xulyhoadon" data-modal-toggle="modal-xulyhoadon"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Nhập chỉ số
        </button>
    </div>

    @php
        $mapphong = $danhsachphong->keyBy('id');
    @endphp

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Phòng</th>
                    <th class="px-6 py-3">Tháng/Năm</th>
                    <th class="px-6 py-3">Điện (cũ→mới)</th>
                    <th class="px-6 py-3">Nước (cũ→mới)</th>
                    <th class="px-6 py-3">Tổng tiền</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3 text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachhoadon as $hoadon)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $mapphong[$hoadon->phong_id]->tenphong ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">{{ $hoadon->thang }}/{{ $hoadon->nam }}</td>
                        <td class="px-6 py-4">{{ $hoadon->chisodiencu }} → {{ $hoadon->chisodienmoi }}</td>
                        <td class="px-6 py-4">{{ $hoadon->chisonuoccu }} → {{ $hoadon->chisonuocmoi }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">{{ number_format($hoadon->tongtien) }} đ</td>
                        <td class="px-6 py-4">
                            @if ($hoadon->trangthaithanhtoan === 'Đã thanh toán')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Đã thanh toán
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    Chưa thanh toán
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if ($hoadon->trangthaithanhtoan === 'Chưa thanh toán')
                                <form method="POST" action="{{ route('admin.xacnhanthanhtoan', $hoadon->id) }}" onsubmit="return confirm('Xác nhận hóa đơn này đã được thanh toán?')">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-blue-600 hover:underline">
                                        Xác nhận thanh toán
                                    </button>
                                </form>
                            @else
                                <span class="text-sm text-gray-400">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-gray-500" colspan="6">Chưa có hóa đơn.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-xulyhoadon" tabindex="-1" aria-hidden="true"
         class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
        <div class="relative max-h-full w-full max-w-2xl">
            <div class="relative rounded-lg bg-white shadow">
                <div class="flex items-start justify-between rounded-t border-b p-4">
                    <h3 class="text-lg font-semibold text-gray-900">Nhập chỉ số điện/nước</h3>
                    <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900"
                            data-modal-hide="modal-xulyhoadon">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                        </svg>
                    </button>
                </div>

                <form class="p-4" method="POST" action="{{ route('admin.xulyhoadon') }}">
                    @csrf
                    <div class="grid gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-900">Chọn phòng</label>
                            <select name="phong_id" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900">
                                <option value="">-- Chọn phòng --</option>
                                @foreach ($danhsachphong as $phong)
                                    <option value="{{ $phong->id }}">{{ $phong->tenphong }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Tháng</label>
                                <input name="thang" value="{{ old('thang', now()->format('m')) }}"
                                       class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Năm</label>
                                <input name="nam" value="{{ old('nam', now()->format('Y')) }}"
                                       class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Chỉ số điện cũ</label>
                                <input name="chisodiencu" value="{{ old('chisodiencu', 0) }}"
                                       class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Chỉ số điện mới</label>
                                <input name="chisodienmoi" value="{{ old('chisodienmoi', 0) }}"
                                       class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Chỉ số nước cũ</label>
                                <input name="chisonuoccu" value="{{ old('chisonuoccu', 0) }}"
                                       class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Chỉ số nước mới</label>
                                <input name="chisonuocmoi" value="{{ old('chisonuocmoi', 0) }}"
                                       class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="mt-4 w-full rounded-lg bg-gray-900 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-gray-800">
                        Lưu hóa đơn
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

