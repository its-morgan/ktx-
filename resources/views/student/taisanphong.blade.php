@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Tài sản phòng</div>
        <div class="text-sm text-[#606060]">Danh sách tài sản trong phòng bạn đang ở.</div>
    </div>

    @if (! $phong)
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-yellow-800">
            Bạn chưa được xếp phòng hoặc không có phòng hợp lệ để xem tài sản.
        </div>
        @return
    @endif

    <div class="mb-4 rounded-lg border border-gray-200/70 bg-white p-4">
        <div class="font-semibold text-[#121212]">Phòng: {{ $phong->tenphong }}</div>
        <div class="text-sm text-[#606060]">Giới tính: {{ $phong->gioitinh ?? 'N/A' }}</div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Tài sản</th>
                        <th class="px-6 py-3">Số lượng</th>
                        <th class="px-6 py-3">Tình trạng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taisan as $item)
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-3">{{ $item->tentaisan }}</td>
                            <td class="px-6 py-3">{{ $item->soluong }}</td>
                            <td class="px-6 py-3">{{ $item->tinhtrang }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4 text-center text-[#606060]" colspan="3">Chưa có tài sản nào trong phòng.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
