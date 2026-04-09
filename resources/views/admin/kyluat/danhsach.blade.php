@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Quản lý kỷ luật</div>
        <div class="text-sm text-[#606060]">Admin thêm và theo dõi các vi phạm của sinh viên.</div>
    </div>

    <form x-data method="GET" action="{{ route('admin.quanlykyluat') }}" class="mb-4 flex flex-wrap items-center gap-3">
        <select name="sinhvien_id" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" @change="$el.form.submit()">
            <option value="">--Chọn sinh viên--</option>
            @foreach($sinhviens as $s)
                <option value="{{ $s->id }}" {{ $selectedSinhvien == $s->id ? 'selected' : '' }}>{{ $s->masinhvien }} - {{ optional($s->taikhoan)->name ?? 'N/A' }}</option>
            @endforeach
        </select>
        <select name="mucdo" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" @change="$el.form.submit()">
            <option value="">--Tất cả mức độ--</option>
            @foreach(\App\Enums\DisciplineLevel::values() as $m)
                <option value="{{ $m }}" {{ $selectedMucDo == $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white" type="submit">Lọc</button>
    </form>

    <div class="mb-6 rounded-lg border border-gray-200/70 bg-white p-4">
        <form method="POST" action="{{ route('admin.themkyluat') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <select name="sinhvien_id" required class="col-span-1 rounded-lg border border-gray-200/80 px-3 py-2 text-sm">
                    <option value="">Chọn sinh viên</option>
                    @foreach($sinhviens as $s)
                        <option value="{{ $s->id }}">{{ $s->masinhvien }} - {{ optional($s->taikhoan)->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
                <input name="noidung" required type="text" placeholder="Nội dung vi phạm" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
                <input name="ngayvipham" required type="date" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" />
                <select name="mucdo" required class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm">
                    <option value="{{ \App\Enums\DisciplineLevel::LOW->value }}">{{ \App\Enums\DisciplineLevel::LOW->value }}</option>
                    <option value="{{ \App\Enums\DisciplineLevel::MEDIUM->value }}" selected>{{ \App\Enums\DisciplineLevel::MEDIUM->value }}</option>
                    <option value="{{ \App\Enums\DisciplineLevel::HIGH->value }}">{{ \App\Enums\DisciplineLevel::HIGH->value }}</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-black px-4 py-2 text-white">Thêm vi phạm</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Mã SV</th>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Nội dung</th>
                        <th class="px-6 py-3">Ngày vi phạm</th>
                        <th class="px-6 py-3">Mức độ</th>
                        <th class="px-6 py-3 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kyluat as $item)
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4">{{ optional($item->sinhvien)->masinhvien ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($item->sinhvien->taikhoan)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item->noidung }}</td>
                            <td class="px-6 py-4">{{ $item->ngayvipham }}</td>
                            <td class="px-6 py-4">{{ $item->mucdo }}</td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" data-modal-target="modal-suakyluat-{{ $item->id }}" data-modal-toggle="modal-suakyluat-{{ $item->id }}" class="gap-2 rounded-lg border border-gray-200/80 bg-white px-3 py-2 text-sm font-medium text-[#606060] hover:bg-[#F7F7F8]">Sửa</button>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4" colspan="6">
                                <x-empty-state
                                    title="Chưa có bản ghi kỷ luật"
                                    description="Vi phạm của sinh viên sẽ hiển thị tại đây sau khi thêm mới."
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
        @foreach($kyluat as $item)
            <div id="modal-suakyluat-{{ $item->id }}" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                <div class="w-full max-w-lg rounded-lg bg-white p-5">
                    <div class="mb-4 flex items-center justify-between border-b pb-2">
                        <h3 class="text-lg font-semibold">Sửa kỷ luật</h3>
                        <button type="button" class="text-[#606060] hover:text-[#121212]" data-modal-hide="modal-suakyluat-{{ $item->id }}">Đóng</button>
                    </div>
                    <form method="POST" action="{{ route('admin.capnhatkyluat', ['id' => $item->id]) }}">
                        @csrf
                        <div class="grid grid-cols-1 gap-3">
                            <input class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" name="noidung" value="{{ $item->noidung }}" required />
                            <input class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" type="date" name="ngayvipham" value="{{ $item->ngayvipham }}" required />
                            <select name="mucdo" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm" required>
                                <option value="{{ \App\Enums\DisciplineLevel::LOW->value }}" {{ $item->mucdo === \App\Enums\DisciplineLevel::LOW->value ? 'selected' : '' }}>{{ \App\Enums\DisciplineLevel::LOW->value }}</option>
                                <option value="{{ \App\Enums\DisciplineLevel::MEDIUM->value }}" {{ $item->mucdo === \App\Enums\DisciplineLevel::MEDIUM->value ? 'selected' : '' }}>{{ \App\Enums\DisciplineLevel::MEDIUM->value }}</option>
                                <option value="{{ \App\Enums\DisciplineLevel::HIGH->value }}" {{ $item->mucdo === \App\Enums\DisciplineLevel::HIGH->value ? 'selected' : '' }}>{{ \App\Enums\DisciplineLevel::HIGH->value }}</option>
                            </select>
                            <button type="submit" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endpush
@endsection
