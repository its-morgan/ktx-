@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="linear-page-title">Quản lý bảo trì</h1>
            <p class="linear-page-subtitle">Danh sách lịch sử bảo trì vật tư theo phòng.</p>
        </div>

        <form method="GET" action="{{ route('admin.quanlybaotri') }}" class="flex flex-wrap items-center gap-2">
            <input
                type="text"
                name="q"
                value="{{ old('q', $tuKhoa ?? '') }}"
                placeholder="Tìm theo phòng, vật tư, nội dung"
                class="linear-input w-72"
            />

            <select name="phong_id" class="linear-select w-48">
                <option value="">Tất cả phòng</option>
                @foreach ($danhsachPhong as $phong)
                    <option value="{{ $phong->id }}" {{ (string) ($phongId ?? '') === (string) $phong->id ? 'selected' : '' }}>
                        {{ $phong->tenphong }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="linear-btn-primary">Lọc</button>
        </form>
    </div>

    <div class="mb-6 grid gap-3 md:grid-cols-3">
        <div class="linear-panel p-4">
            <div class="text-xs uppercase tracking-wide text-slate-500">Lượt bảo trì</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ $thongKe['tong_luot'] ?? 0 }}</div>
        </div>
        <div class="linear-panel p-4">
            <div class="text-xs uppercase tracking-wide text-slate-500">Tháng này</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ $thongKe['thang_nay'] ?? 0 }}</div>
        </div>
        <div class="linear-panel p-4">
            <div class="text-xs uppercase tracking-wide text-slate-500">Tổng chi phí</div>
            <div class="mt-1 text-2xl font-bold text-slate-900">{{ number_format((float) ($thongKe['tong_chiphi'] ?? 0), 0, ',', '.') }} đ</div>
        </div>
    </div>

    <div class="linear-table-wrap">
        <div class="overflow-x-auto">
            <table class="linear-table">
                <thead>
                    <tr>
                        <th>Ngày bảo trì</th>
                        <th>Phòng</th>
                        <th>Vật tư</th>
                        <th>Nội dung</th>
                        <th>Đơn vị</th>
                        <th>Chi phí</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($danhsachBaoTri as $item)
                        @php
                            $vattu = $item->vattu;
                            $phong = optional($vattu)->phong;
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->ngaybaotri)->format('d/m/Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $phong->tenphong ?? 'N/A' }}</td>
                            <td>{{ $vattu->tenvattu ?? 'N/A' }}</td>
                            <td class="max-w-sm">{{ $item->noidung }}</td>
                            <td>{{ $item->donvithuchien ?: '-' }}</td>
                            <td class="font-semibold text-slate-900">{{ number_format((float) ($item->chiphi ?? 0), 0, ',', '.') }} đ</td>
                            <td class="text-right">
                                @if ($phong)
                                    <a href="{{ route('admin.chitietphong', ['id' => $phong->id]) }}" class="linear-btn-secondary text-xs">Chi tiết phòng</a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10">
                                <x-empty-state
                                    title="Chưa có dữ liệu bảo trì"
                                    description="Khi có lịch sử bảo trì vật tư, dữ liệu sẽ hiển thị tại đây."
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

    <div class="mt-4">
        {{ $danhsachBaoTri->links() }}
    </div>
@endsection
