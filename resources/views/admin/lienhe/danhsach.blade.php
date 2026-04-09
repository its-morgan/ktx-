@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Quan ly lien he</div>
        <div class="text-sm text-[#606060]">Theo doi cac cau hoi gui tu landing page.</div>
    </div>

    <div class="mb-4 grid gap-3 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200/70 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Tong so</div>
            <div class="mt-1 text-2xl font-semibold text-[#121212]">{{ number_format($thongKe['tong_so']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200/70 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Chua xu ly</div>
            <div class="mt-1 text-2xl font-semibold text-amber-700">{{ number_format($thongKe['chua_xu_ly']) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200/70 bg-white p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Da xu ly</div>
            <div class="mt-1 text-2xl font-semibold text-emerald-700">{{ number_format($thongKe['da_xu_ly']) }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.quanlylienhe') }}" class="mb-4 rounded-lg border border-gray-200/70 bg-white p-4">
        <div class="grid gap-3 sm:grid-cols-4">
            <input
                type="text"
                name="q"
                value="{{ $tuKhoa }}"
                placeholder="Tim theo ten, email, noi dung..."
                class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm sm:col-span-2"
            >
            <select name="trang_thai" class="rounded-lg border border-gray-200/80 px-3 py-2 text-sm">
                <option value="tatca" {{ $trangThai === 'tatca' ? 'selected' : '' }}>Tat ca trang thai</option>
                <option value="{{ \App\Models\Lienhe::TRANG_THAI_CHUA_XU_LY }}" {{ $trangThai === \App\Models\Lienhe::TRANG_THAI_CHUA_XU_LY ? 'selected' : '' }}>Chua xu ly</option>
                <option value="{{ \App\Models\Lienhe::TRANG_THAI_DA_XU_LY }}" {{ $trangThai === \App\Models\Lienhe::TRANG_THAI_DA_XU_LY ? 'selected' : '' }}>Da xu ly</option>
            </select>
            <button type="submit" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-[#1C1C1C]">
                Loc
            </button>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Nguoi gui</th>
                        <th class="px-6 py-3">Noi dung</th>
                        <th class="px-6 py-3">Thoi gian</th>
                        <th class="px-6 py-3">Trang thai</th>
                        <th class="px-6 py-3 text-right">Cap nhat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachLienHe as $item)
                        <tr class="border-t border-gray-200/70 align-top">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-[#121212]">{{ $item->ho_ten }}</div>
                                <div class="text-xs text-gray-500">{{ $item->email }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-[560px]">
                                <div class="line-clamp-4">{{ $item->noi_dung }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ optional($item->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeType = $item->trang_thai === \App\Models\Lienhe::TRANG_THAI_DA_XU_LY ? 'success' : 'warning';
                                @endphp
                                <x-badge type="{{ $badgeType }}" :text="$item->trang_thai" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.capnhattrangthailienhe', ['id' => $item->id]) }}" class="inline-flex items-center gap-2">
                                    @csrf
                                    <select name="trang_thai" class="rounded-lg border border-gray-200/80 px-2 py-1.5 text-xs">
                                        <option value="{{ \App\Models\Lienhe::TRANG_THAI_CHUA_XU_LY }}" {{ $item->trang_thai === \App\Models\Lienhe::TRANG_THAI_CHUA_XU_LY ? 'selected' : '' }}>Chua xu ly</option>
                                        <option value="{{ \App\Models\Lienhe::TRANG_THAI_DA_XU_LY }}" {{ $item->trang_thai === \App\Models\Lienhe::TRANG_THAI_DA_XU_LY ? 'selected' : '' }}>Da xu ly</option>
                                    </select>
                                    <button type="submit" class="rounded-lg border border-gray-200/80 bg-white px-3 py-1.5 text-xs font-medium text-[#606060] hover:bg-[#F7F7F8]">
                                        Luu
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td colspan="5" class="px-6 py-4">
                                <x-empty-state
                                    title="Chua co lien he nao"
                                    description="Khi khach gui cau hoi tu landing page, du lieu se hien thi tai day."
                                    actionLabel="Tai lai trang"
                                    :actionHref="request()->fullUrl()"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $danhSachLienHe->links() }}
    </div>
@endsection

