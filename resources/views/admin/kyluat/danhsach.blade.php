@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Quản lý kỷ luật</div>
        <div class="text-sm text-gray-500">Admin thêm và theo dõi các vi phạm của sinh viên.</div>
    </div>

    <form method="GET" action="{{ route('admin.quanlykyluat') }}" class="mb-4 flex flex-wrap items-center gap-3">
        <select name="sinhvien_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">--Chọn sinh viên--</option>
            @foreach($sinhviens as $s)
                <option value="{{ $s->id }}" {{ $selectedSinhvien == $s->id ? 'selected' : '' }}>{{ $s->masinhvien }} - {{ optional($s->taikhoan)->name ?? 'N/A' }}</option>
            @endforeach
        </select>
        <select name="mucdo" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">--Tất cả mức độ--</option>
            @foreach(['Nhẹ', 'Trung bình', 'Nặng'] as $m)
                <option value="{{ $m }}" {{ $selectedMucDo == $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white" type="submit">Lọc</button>
    </form>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4">
        <form method="POST" action="{{ route('admin.themkyluat') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <select name="sinhvien_id" required class="col-span-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Chọn sinh viên</option>
                    @foreach($sinhviens as $s)
                        <option value="{{ $s->id }}">{{ $s->masinhvien }} - {{ optional($s->taikhoan)->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
                <input name="noidung" required type="text" placeholder="Nội dung vi phạm" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <input name="ngayvipham" required type="date" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <select name="mucdo" required class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="Nhẹ">Nhẹ</option>
                    <option value="Trung bình" selected>Trung bình</option>
                    <option value="Nặng">Nặng</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white">Thêm vi phạm</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Mã SV</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Nội dung</th>
                        <th class="px-6 py-3">Ngày vi phạm</th>
                        <th class="px-6 py-3">Mức độ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kyluat as $item)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4">{{ optional($item->sinhvien)->masinhvien ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($item->sinhvien->taikhoan)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item->noidung }}</td>
                            <td class="px-6 py-4">{{ $item->ngayvipham }}</td>
                            <td class="px-6 py-4">{{ $item->mucdo }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-center text-gray-400" colspan="5">Chưa có vi phạm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
