@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <div class="text-2xl font-bold text-[#121212]">Danh sách phòng trống</div>
            <div class="text-sm text-[#606060]">Sinh viên chọn phòng và gửi đăng ký.</div>
        </div>

        <form method="GET" action="{{ route('student.danhsachphong') }}" class="flex items-center gap-2">
            <input name="q" value="{{ old('q', $tuKhoa ?? '') }}" type="text" placeholder="Tìm theo tên phòng"
                   class="rounded-lg border border-gray-200/80 p-2 text-sm" />
            <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">Tìm</button>
        </form>
    </div>

    <div id="so-do-tang" class="overflow-hidden rounded-lg border border-gray-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                <tr>
                    <th class="px-6 py-3">Tên phòng</th>
                    <th class="px-6 py-3">Giá phòng</th>
                    <th class="px-6 py-3">Số người đang ở</th>
                    <th class="px-6 py-3 text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachphong as $phong)
                    <tr class="border-t border-gray-200/70">
                        <td class="px-6 py-4 font-medium text-[#121212]">{{ $phong->tenphong }}</td>
                        <td class="px-6 py-4">{{ number_format($phong->giaphong) }} đ</td>
                        <td class="px-6 py-4">
                            @php
                                $soNguoiDangO = $soluongdango_theophong[$phong->id] ?? 0;
                                $soChoConLai = $phong->succhuamax - $soNguoiDangO;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-[#121212]">{{ $soNguoiDangO }}/{{ $phong->succhuamax }}</span>
                                <span class="text-xs px-2 py-1 rounded-full {{ $soChoConLai <= 1 ? 'bg-red-100 text-red-700' : ($soChoConLai <= 2 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $soChoConLai }} chỗ trống
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('student.dangkyphong') }}">
                                @csrf
                                <input type="hidden" name="phong_id" value="{{ $phong->id }}">
                                <button type="submit"
                                        class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                    Gửi đăng ký
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-200/70">
                        <td class="px-6 py-4" colspan="4">
                            <x-empty-state
                                title="Chưa có phòng trống"
                                description="Khi có phòng trống, danh sách sẽ hiển thị tại đây để em đăng ký."
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
@endsection
