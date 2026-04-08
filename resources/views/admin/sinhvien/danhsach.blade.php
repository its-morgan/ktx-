@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <div class="text-2xl font-bold text-[#121212]">Quản lý sinh viên</div>
            <div class="text-sm text-[#606060]">Danh sách sinh viên và chuyển phòng nhanh.</div>
        </div>

        <form method="GET" action="{{ route('admin.quanlysinhvien') }}" class="flex items-center gap-2">
            <input name="q" value="{{ old('q', $tuKhoa ?? '') }}" type="text" placeholder="Tìm theo mã sinh viên"
                   class="rounded-lg border border-gray-200/80 p-2 text-sm" />
            <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">Tìm</button>
        </form>
    </div>

    @php
        $maptenphong = $danhsachphong->keyBy('id');
    @endphp

    <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white shadow-none">
        <div class="overflow-x-auto">
            <table class="table-fixed w-full min-w-full divide-y divide-gray-200 text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
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
                    <tr class="border-t border-gray-200/70 bg-white hover:bg-[#F7F7F8]">
                        <td class="px-6 py-4 font-semibold text-[#121212]">{{ $sinhvien->masinhvien }}</td>
                        <td class="px-6 py-4 font-medium text-[#121212]">{{ optional($sinhvien->taikhoan)->name ?? 'Chưa có' }}</td>
                        @php
                            $gioitinh = optional($sinhvien->taikhoan)->gioitinh ?? 'Khác';
                            $badgeClass = $gioitinh === 'Nữ' ? 'bg-[#F7F7F8] text-[#606060]' : 'bg-[#F7F7F8] text-[#606060]';
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
                                <span class="text-[#9A9A9A]">Chưa có</span>
                            @endif
                        </td>
                        <td class="w-52 px-6 py-4">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <button type="button" data-modal-target="modal-suasinhvien-{{ $sinhvien->id }}" data-modal-toggle="modal-suasinhvien-{{ $sinhvien->id }}" class="h-8 min-w-[46px] rounded-lg border border-gray-200/70 bg-[#F7F7F8] px-2 text-xs font-semibold text-[#606060] hover:bg-[#F1F1F2]">Sửa</button>

                                <form method="POST" action="{{ route('admin.chuyenphong', ['id' => $sinhvien->id]) }}" class="flex items-center gap-2 min-w-max">
                                    @csrf
                                    <select name="phong_id" class="h-8 w-28 rounded-lg border border-gray-200/80 px-2 text-xs text-[#121212] focus:border-gray-900 focus:ring-gray-900">
                                        <option value="0" {{ $sinhvien->phong_id ? '' : 'selected' }}>Chưa có</option>
                                        @foreach ($danhsachphong as $phong)
                                            <option value="{{ $phong->id }}" {{ (int) $sinhvien->phong_id === (int) $phong->id ? 'selected' : '' }}>{{ $phong->tenphong }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="linear-btn-primary px-3 py-1.5 text-xs">Lưu</button>
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
                    <tr class="border-t border-gray-200/70">
                        <td class="px-6 py-4" colspan="7">
                            <x-empty-state
                                title="Chưa có sinh viên"
                                description="Danh sách sinh viên sẽ hiển thị tại đây khi có dữ liệu."
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
    @push('modals')
        @foreach ($danhsachsinhvien as $sinhvien)
            <div id="modal-suasinhvien-{{ $sinhvien->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                <div class="w-full max-w-lg rounded-lg bg-white p-5">
                    <h3 class="mb-4 text-lg font-semibold">Sửa thông tin sinh viên</h3>
                    <form method="POST" action="{{ route('admin.capnhatsinhvien', ['id' => $sinhvien->id]) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-[#606060]">Họ và tên</label>
                            <input type="text" name="name" value="{{ optional($sinhvien->taikhoan)->name }}" class="mt-1 block w-full rounded-lg border border-gray-200/80 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#606060]">Mã sinh viên</label>
                            <input type="text" name="masinhvien" value="{{ $sinhvien->masinhvien }}" class="mt-1 block w-full rounded-lg border border-gray-200/80 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#606060]">Lớp</label>
                            <input type="text" name="lop" value="{{ $sinhvien->lop }}" class="mt-1 block w-full rounded-lg border border-gray-200/80 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#606060]">Số điện thoại</label>
                            <input type="text" name="sodienthoai" value="{{ $sinhvien->sodienthoai }}" class="mt-1 block w-full rounded-lg border border-gray-200/80 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#606060]">Giới tính</label>
                            <select name="gioitinh" class="mt-1 block w-full rounded-lg border border-gray-200/80 px-3 py-2 text-sm" required>
                                <option value="Nam" {{ optional($sinhvien->taikhoan)->gioitinh === 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ optional($sinhvien->taikhoan)->gioitinh === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" data-modal-hide="modal-suasinhvien-{{ $sinhvien->id }}" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm">Hủy</button>
                            <button type="submit" class="rounded-lg bg-black px-3 py-2 text-sm font-medium text-white hover:bg-[#1C1C1C]">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endpush
@endsection

