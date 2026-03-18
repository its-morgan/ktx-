@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Quản lý sinh viên</div>
        <div class="text-sm text-gray-500">Danh sách sinh viên và chuyển phòng nhanh.</div>
    </div>

    @php
        $maptenphong = $danhsachphong->keyBy('id');
    @endphp

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Mã SV</th>
                    <th class="px-6 py-3">Lớp</th>
                    <th class="px-6 py-3">Số điện thoại</th>
                    <th class="px-6 py-3">Phòng</th>
                    <th class="px-6 py-3 text-right">Chuyển phòng</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachsinhvien as $sinhvien)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $sinhvien->masinhvien }}</td>
                        <td class="px-6 py-4">{{ $sinhvien->lop }}</td>
                        <td class="px-6 py-4">{{ $sinhvien->sodienthoai }}</td>
                        <td class="px-6 py-4">
                            @if ($sinhvien->phong_id && isset($maptenphong[$sinhvien->phong_id]))
                                {{ $maptenphong[$sinhvien->phong_id]->tenphong }}
                            @else
                                <span class="text-gray-400">Chưa có</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.chuyenphong', ['id' => $sinhvien->id]) }}" class="flex items-center justify-end gap-2">
                                @csrf
                                <select name="phong_id" class="rounded-lg border border-gray-300 p-2 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900">
                                    <option value="0" {{ $sinhvien->phong_id ? '' : 'selected' }}>Chưa có</option>
                                    @foreach ($danhsachphong as $phong)
                                        <option value="{{ $phong->id }}" {{ (int) $sinhvien->phong_id === (int) $phong->id ? 'selected' : '' }}>
                                            {{ $phong->tenphong }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                    Lưu
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-gray-500" colspan="5">Chưa có sinh viên.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

