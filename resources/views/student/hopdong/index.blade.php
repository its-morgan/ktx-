@extends('student.layouts.chinh')

@section('noidung')
<div class="space-y-4">
    <h1 class="text-2xl font-bold">Hợp đồng của tôi</h1>

    @if ($danhsachhopdong->isEmpty())
        <div class="rounded border border-gray-200 p-4 bg-white text-center text-gray-600">Bạn chưa có hợp đồng nào.</div>
    @else
        <div class="overflow-x-auto rounded border bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Phòng</th>
                        <th class="px-4 py-2 text-left">Bắt đầu</th>
                        <th class="px-4 py-2 text-left">Kết thúc</th>
                        <th class="px-4 py-2 text-left">Giá ký</th>
                        <th class="px-4 py-2 text-left">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($danhsachhopdong as $hopdong)
                        <tr>
                            <td class="px-4 py-2">{{ $hopdong->id }}</td>
                            <td class="px-4 py-2">{{ $hopdong->phong->tenphong ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $hopdong->ngay_bat_dau }}</td>
                            <td class="px-4 py-2">{{ $hopdong->ngay_ket_thuc }}</td>
                            <td class="px-4 py-2">{{ number_format($hopdong->giaphong_luc_ky) }}</td>
                            <td class="px-4 py-2">{{ $hopdong->trang_thai }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
