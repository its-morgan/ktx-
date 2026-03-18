@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <div class="text-2xl font-bold text-gray-900">Quản lý sinh viên</div>
            <div class="text-sm text-gray-500">Danh sách sinh viên và chuyển phòng nhanh.</div>
        </div>

        <form method="GET" action="{{ route('admin.quanlysinhvien') }}" class="flex items-center gap-2">
            <input name="q" value="{{ old('q', $tuKhoa ?? '') }}" type="text" placeholder="Tìm theo mã sinh viên"
                   class="rounded-lg border border-gray-300 p-2 text-sm" />
            <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">Tìm</button>
        </form>
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
                            <div class="flex flex-col gap-2 items-end">
                                <form method="POST" action="{{ route('admin.chuyenphong', ['id' => $sinhvien->id]) }}" class="flex items-center gap-2">
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

                                @if($sinhvien->phong_id)
                                    <form method="POST" action="{{ route('admin.choroiophong', ['id' => $sinhvien->id]) }}" onsubmit="return confirm('Bạn có chắc muốn cho sinh viên này rời phòng?')">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-red-600 hover:underline">
                                            Cho rời phòng
                                        </button>
                                    </form>
                                @endif
                            </div>
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

