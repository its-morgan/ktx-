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

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="table-fixed w-full min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="w-24 px-6 py-3 text-left font-semibold">Mã SV</th>
                    <th class="w-40 px-6 py-3 text-left font-semibold">Họ và tên</th>
                    <th class="w-20 px-6 py-3 text-left font-semibold">Giới tính</th>
                    <th class="w-24 px-6 py-3 text-left font-semibold">Lớp</th>
                    <th class="w-36 px-6 py-3 text-left font-semibold">Số điện thoại</th>
                    <th class="w-32 px-6 py-3 text-left font-semibold">Phòng</th>
                    <th class="w-52 px-6 py-3 text-right font-semibold">Chỉnh sửa</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachsinhvien as $sinhvien)
                    <tr class="border-t border-gray-200 bg-white hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $sinhvien->masinhvien }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ optional($sinhvien->taikhoan)->name ?? 'Chưa có' }}</td>
                        @php
                            $gioitinh = optional($sinhvien->taikhoan)->gioitinh ?? 'Khác';
                            $badgeClass = $gioitinh === 'Nữ' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700';
                        @endphp
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $gioitinh }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $sinhvien->lop }}</td>
                        <td class="px-6 py-4">{{ $sinhvien->sodienthoai }}</td>
                        <td class="w-32 px-6 py-4">
                            @if ($sinhvien->phong_id && isset($maptenphong[$sinhvien->phong_id]))
                                {{ $maptenphong[$sinhvien->phong_id]->tenphong }}
                            @else
                                <span class="text-gray-400">Chưa có</span>
                            @endif
                        </td>
                        <td class="w-52 px-6 py-4">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <button type="button" data-modal-target="modal-suasinhvien-{{ $sinhvien->id }}" data-modal-toggle="modal-suasinhvien-{{ $sinhvien->id }}" class="h-8 min-w-[46px] rounded-lg border border-blue-200 bg-blue-50 px-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">Sửa</button>

                                <form method="POST" action="{{ route('admin.chuyenphong', ['id' => $sinhvien->id]) }}" class="flex items-center gap-2 min-w-max">
                                    @csrf
                                    <select name="phong_id" class="h-8 w-28 rounded-lg border border-gray-300 px-2 text-xs text-gray-900 focus:border-gray-900 focus:ring-gray-900">
                                        <option value="0" {{ $sinhvien->phong_id ? '' : 'selected' }}>Chưa có</option>
                                        @foreach ($danhsachphong as $phong)
                                            <option value="{{ $phong->id }}" {{ (int) $sinhvien->phong_id === (int) $phong->id ? 'selected' : '' }}>{{ $phong->tenphong }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Lưu</button>
                                </form>

                                @if($sinhvien->phong_id)
                                    <form method="POST" action="{{ route('admin.choroiophong', ['id' => $sinhvien->id]) }}" onsubmit="return confirm('Bạn có chắc muốn cho sinh viên này rời phòng?')" class="inline">
                                        @csrf
                                        <button type="submit" class="h-8 min-w-[98px] rounded-lg bg-red-600 px-2 text-xs font-semibold text-white hover:bg-red-700" title="Cho rời phòng">Cho rời phòng</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-gray-500" colspan="7">Chưa có sinh viên.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @foreach ($danhsachsinhvien as $sinhvien)
                <div id="modal-suasinhvien-{{ $sinhvien->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div class="w-full max-w-lg rounded-lg bg-white p-5">
                        <h3 class="mb-4 text-lg font-semibold">Sửa thông tin sinh viên</h3>
                        <form method="POST" action="{{ route('admin.capnhatsinhvien', ['id' => $sinhvien->id]) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                                <input type="text" name="name" value="{{ optional($sinhvien->taikhoan)->name }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mã sinh viên</label>
                                <input type="text" name="masinhvien" value="{{ $sinhvien->masinhvien }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lớp</label>
                                <input type="text" name="lop" value="{{ $sinhvien->lop }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                <input type="text" name="sodienthoai" value="{{ $sinhvien->sodienthoai }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Giới tính</label>
                                <select name="gioitinh" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                                    <option value="Nam" {{ optional($sinhvien->taikhoan)->gioitinh === 'Nam' ? 'selected' : '' }}>Nam</option>
                                    <option value="Nữ" {{ optional($sinhvien->taikhoan)->gioitinh === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" data-modal-hide="modal-suasinhvien-{{ $sinhvien->id }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">Hủy</button>
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection

