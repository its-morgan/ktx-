@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Hóa đơn của em</div>
        <div class="text-sm text-gray-500">Danh sách hóa đơn theo phòng của em.</div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Tháng/Năm</th>
                    <th class="px-6 py-3">Điện (cũ→mới)</th>
                    <th class="px-6 py-3">Nước (cũ→mới)</th>
                    <th class="px-6 py-3">Tổng tiền</th>
                    <th class="px-6 py-3">Trạng thái</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachhoadon as $hoadon)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $hoadon->thang }}/{{ $hoadon->nam }}</td>
                        <td class="px-6 py-4">{{ $hoadon->chisodiencu }} → {{ $hoadon->chisodienmoi }}</td>
                        <td class="px-6 py-4">{{ $hoadon->chisonuoccu }} → {{ $hoadon->chisonuocmoi }}</td>
                        <td class="px-6 py-4">{{ number_format($hoadon->tongtien) }} đ</td>
                        <td class="px-6 py-4">{{ $hoadon->trangthaithanhtoan }}</td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-gray-500" colspan="5">Em chưa có hóa đơn (hoặc chưa được xếp phòng).</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

