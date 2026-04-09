@extends('admin.layouts.quantri')

@section('noidung')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="linear-title">Danh sách hợp đồng</h1>

    <form x-data action="{{ route('admin.quanlyhopdong') }}" method="get" class="flex flex-wrap gap-2">
        <input type="text" name="timkiem" value="{{ old('timkiem', $timkiem) }}" placeholder="Tìm mã SV" class="linear-input" />
        <select name="trangthai" class="linear-select" @change="$el.form.submit()">
            <option value="Tất cả" {{ $trangthai == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
            <option value="{{ \App\Enums\ContractStatus::ACTIVE->value }}" {{ $trangthai == \App\Enums\ContractStatus::ACTIVE->value ? 'selected' : '' }}>{{ \App\Enums\ContractStatus::ACTIVE->value }}</option>
            <option value="{{ \App\Enums\ContractStatus::EXPIRED->value }}" {{ $trangthai == \App\Enums\ContractStatus::EXPIRED->value ? 'selected' : '' }}>{{ \App\Enums\ContractStatus::EXPIRED->value }}</option>
            <option value="{{ \App\Enums\ContractStatus::TERMINATED->value }}" {{ $trangthai == \App\Enums\ContractStatus::TERMINATED->value ? 'selected' : '' }}>{{ \App\Enums\ContractStatus::TERMINATED->value }}</option>
        </select>
        <button type="submit" class="linear-btn-primary">Lọc</button>
    </form>
</div>

<div class="linear-table-wrap overflow-x-auto">
    <table class="linear-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã SV</th>
                <th>Tên SV</th>
                <th>Phòng</th>
                <th>BĐ</th>
                <th>KT</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($danhsachhopdong as $hopdong)
                <tr>
                    <td>{{ $hopdong->id }}</td>
                    <td>{{ $hopdong->sinhvien->masinhvien ?? '-' }}</td>
                    <td>{{ $hopdong->sinhvien->taikhoan->name ?? '-' }}</td>
                    <td>{{ $hopdong->phong->tenphong ?? '-' }}</td>
                    <td>{{ $hopdong->ngay_bat_dau }}</td>
                    <td>{{ $hopdong->ngay_ket_thuc }}</td>
                    <td>{{ number_format($hopdong->giaphong_luc_ky) }}</td>
                    @php
                        $badgeType = match ($hopdong->trang_thai) {
                            \App\Enums\ContractStatus::ACTIVE->value => 'success',
                            \App\Enums\ContractStatus::EXPIRED->value => 'warning',
                            \App\Enums\ContractStatus::TERMINATED->value => 'danger',
                            default => 'default',
                        };
                    @endphp
                    <td><x-badge type="{{ $badgeType }}" :text="$hopdong->trang_thai" /></td>
                    <td class="space-x-1 whitespace-nowrap">
                        <button type="button" data-modal-target="modal-chi-tiet-{{ $hopdong->id }}" data-modal-toggle="modal-chi-tiet-{{ $hopdong->id }}" class="linear-btn-secondary px-2 py-1 text-xs">Chi tiết</button>

                        @if ($hopdong->trang_thai === \App\Enums\ContractStatus::ACTIVE->value)
                            <button type="button" data-modal-target="modal-gia-han-{{ $hopdong->id }}" data-modal-toggle="modal-gia-han-{{ $hopdong->id }}" class="linear-btn-primary px-2 py-1 text-xs">Gia hạn</button>
                        @endif

                        @if ($hopdong->trang_thai !== \App\Enums\ContractStatus::TERMINATED->value)
                            <form action="{{ route('admin.hopdong.thanhly', $hopdong->id) }}" method="post" class="inline">
                                @csrf
                                <button type="submit" class="linear-btn-danger px-2 py-1 text-xs" onclick="return confirm('Xác nhận thanh lý?')">Thanh lý</button>
                            </form>
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8">
                        <x-empty-state
                            title="Chưa có hợp đồng nào"
                            description="Hợp đồng của sinh viên sẽ hiển thị tại đây khi được tạo."
                            actionLabel="Tải lại trang"
                            :actionHref="request()->fullUrl()"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $danhsachhopdong->withQueryString()->links() }}</div>
@push('modals')
    @foreach ($danhsachhopdong as $hopdong)
        <div id="modal-chi-tiet-{{ $hopdong->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30">
            <div class="w-full max-w-lg rounded-lg border border-gray-200/70 bg-white p-6">
                <h3 class="mb-4 text-xl font-bold text-[#121212]">Thông tin hợp đồng #{{ $hopdong->id }}</h3>
                @php
                    $badgeType = match ($hopdong->trang_thai) {
                        \App\Enums\ContractStatus::ACTIVE->value => 'success',
                        \App\Enums\ContractStatus::EXPIRED->value => 'warning',
                        \App\Enums\ContractStatus::TERMINATED->value => 'danger',
                        default => 'default',
                    };
                @endphp
                <ul class="space-y-2 text-sm text-[#606060]">
                    <li><strong>Sinh viên:</strong> {{ $hopdong->sinhvien->taikhoan->name ?? '-' }} ({{ $hopdong->sinhvien->masinhvien ?? '-' }})</li>
                    <li><strong>Phòng:</strong> {{ $hopdong->phong->tenphong ?? '-' }}</li>
                    <li><strong>Ngày bắt đầu:</strong> {{ $hopdong->ngay_bat_dau }}</li>
                    <li><strong>Ngày kết thúc:</strong> {{ $hopdong->ngay_ket_thuc }}</li>
                    <li><strong>Giá phòng tại ký:</strong> {{ number_format($hopdong->giaphong_luc_ky) }} đ</li>
                    <li><strong>Trạng thái:</strong> <x-badge type="{{ $badgeType }}" :text="$hopdong->trang_thai" /></li>
                    <li><strong>Ghi chú:</strong> {{ $hopdong->ghichu ?? '-' }}</li>
                </ul>
                <div class="mt-5 text-right">
                    <button type="button" data-modal-hide="modal-chi-tiet-{{ $hopdong->id }}" data-modal-toggle="modal-chi-tiet-{{ $hopdong->id }}" class="linear-btn-secondary">Đóng</button>
                </div>
            </div>
        </div>

        <div id="modal-gia-han-{{ $hopdong->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30">
            <div class="w-full max-w-md rounded-lg border border-gray-200/70 bg-white p-6">
                <h3 class="mb-4 text-xl font-bold text-[#121212]">Gia hạn hợp đồng #{{ $hopdong->id }}</h3>
                <form action="{{ route('admin.hopdong.giahan', $hopdong->id) }}" method="post">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="ngay_ket_thuc" value="Ngày kết thúc mới" />
                        <x-text-input id="ngay_ket_thuc" class="mt-1 block w-full" type="date" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc', $hopdong->ngay_ket_thuc) }}" required autofocus />
                        <x-input-error :messages="$errors->get('ngay_ket_thuc')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" data-modal-hide="modal-gia-han-{{ $hopdong->id }}" data-modal-toggle="modal-gia-han-{{ $hopdong->id }}" class="linear-btn-secondary">Hủy</button>
                        <button type="submit" class="linear-btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endpush
@endsection
