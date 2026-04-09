@extends('student.layouts.chinh')

@section('noidung')
    <div id="thanh-toan-online" class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Hóa đơn của em</div>
        <div class="text-sm text-[#606060]">Danh sách hóa đơn theo phòng của em.</div>
    </div>

    <div class="space-y-4 md:hidden">
        @forelse ($danhsachhoadon as $hoadon)
            <div class="rounded-lg border border-gray-200/70 bg-white p-4 shadow-none">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-[#606060]">Tháng/Năm</div>
                        <div class="text-lg font-semibold text-[#121212]">{{ $hoadon->thang }}/{{ $hoadon->nam }}</div>
                    </div>
                    @php
                        $badgeType = $hoadon->trangthaithanhtoan === 'Đã thanh toán' ? 'success' : 'warning';
                    @endphp
                    <x-badge type="{{ $badgeType }}" :text="$hoadon->trangthaithanhtoan" />
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[#606060]">Điện</span>
                        <span class="font-medium text-[#121212]">{{ $hoadon->chisodiencu }} -> {{ $hoadon->chisodienmoi }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[#606060]">Nước</span>
                        <span class="font-medium text-[#121212]">{{ $hoadon->chisonuoccu }} -> {{ $hoadon->chisonuocmoi }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[#606060]">Tổng tiền</span>
                        <span class="font-semibold text-[#606060]">{{ number_format($hoadon->tongtien) }} đ</span>
                    </div>
                </div>

                <button
                    type="button"
                    data-modal-target="modal-bienlai-{{ $hoadon->id }}"
                    data-modal-toggle="modal-bienlai-{{ $hoadon->id }}"
                    class="mt-4 w-full rounded-lg bg-black px-3 py-2 text-sm font-medium text-white hover:bg-[#1C1C1C]"
                >
                    Xem biên lai
                </button>
            </div>
        @empty
            <x-empty-state
                title="Chưa có hóa đơn"
                description="Hóa đơn của em sẽ hiển thị tại đây khi được tạo."
                actionLabel="Tải lại trang"
                :actionHref="request()->fullUrl()"
            />
        @endforelse
    </div>

    <div class="hidden overflow-hidden rounded-lg border border-gray-200/70 bg-white md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Tháng/Năm</th>
                        <th class="px-6 py-3">Điện (cũ->mới)</th>
                        <th class="px-6 py-3">Nước (cũ->mới)</th>
                        <th class="px-6 py-3">Tổng tiền</th>
                        <th class="px-6 py-3">Trạng thái</th>
                        <th class="px-6 py-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($danhsachhoadon as $hoadon)
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4 font-medium text-[#121212]">{{ $hoadon->thang }}/{{ $hoadon->nam }}</td>
                            <td class="px-6 py-4">{{ $hoadon->chisodiencu }} -> {{ $hoadon->chisodienmoi }}</td>
                            <td class="px-6 py-4">{{ $hoadon->chisonuoccu }} -> {{ $hoadon->chisonuocmoi }}</td>
                            <td class="px-6 py-4">{{ number_format($hoadon->tongtien) }} đ</td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeType = $hoadon->trangthaithanhtoan === 'Đã thanh toán' ? 'success' : 'warning';
                                @endphp
                                <x-badge type="{{ $badgeType }}" :text="$hoadon->trangthaithanhtoan" />
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    type="button"
                                    data-modal-target="modal-bienlai-{{ $hoadon->id }}"
                                    data-modal-toggle="modal-bienlai-{{ $hoadon->id }}"
                                    class="rounded-lg bg-black px-3 py-2 text-sm font-medium text-white hover:bg-[#1C1C1C]"
                                >
                                    Xem biên lai
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4" colspan="6">
                                <x-empty-state
                                    title="Chưa có hóa đơn"
                                    description="Hóa đơn của em sẽ hiển thị tại đây khi được tạo."
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

    @foreach ($danhsachhoadon as $hoadon)
        <div id="modal-bienlai-{{ $hoadon->id }}" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-lg rounded-lg bg-white p-5">
                <div class="mb-4 flex items-center justify-between border-b pb-3">
                    <h3 class="text-lg font-semibold">Biên lai thanh toán</h3>
                    <button type="button" class="text-[#606060] hover:text-[#121212]" data-modal-hide="modal-bienlai-{{ $hoadon->id }}">Đóng</button>
                </div>
                <div class="mb-3 text-sm text-[#606060]">Phòng: {{ optional($hoadon->phong)->tenphong ?? 'N/A' }}</div>
                <div class="mb-3 text-sm text-[#606060]">Tháng/Năm: {{ $hoadon->thang }}/{{ $hoadon->nam }}</div>
                <div class="mb-4 flex flex-wrap items-center gap-4">
                    <div class="text-sm font-medium text-[#606060]">Quét QR để thanh toán:</div>
                    @php
                        $phongTen = optional($hoadon->phong)->tenphong ?? 'N/A';
                        $qrText = 'KTX - ' . $phongTen . ' - ' . $hoadon->thang . '/' . $hoadon->nam;
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=170x170&data=' . urlencode($qrText);
                    @endphp
                    <img src="{{ $qrUrl }}" alt="QR thanh toán" class="h-32 w-32 rounded-lg border border-gray-200/70" />
                </div>
                <table class="w-full text-left text-sm text-[#606060]">
                    <tbody>
                        <tr>
                            <td class="py-1">Tiền phòng cố định</td>
                            <td class="py-1 text-right">{{ number_format(optional($hoadon->phong)->giaphong ?? 0) }} đ</td>
                        </tr>
                        <tr>
                            <td class="py-1">Tiền điện</td>
                            <td class="py-1 text-right">{{ number_format((($hoadon->chisodienmoi - $hoadon->chisodiencu) * 3500)) }} đ</td>
                        </tr>
                        <tr>
                            <td class="py-1">Tiền nước</td>
                            <td class="py-1 text-right">{{ number_format((($hoadon->chisonuocmoi - $hoadon->chisonuoccu) * 15000)) }} đ</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold">Tổng cộng</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($hoadon->tongtien) }} đ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
