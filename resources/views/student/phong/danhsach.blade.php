@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <div class="text-2xl font-bold text-gray-900">Danh sách phòng trống</div>
            <div class="text-sm text-gray-500">Sinh viên chọn phòng và gửi đăng ký.</div>
        </div>

        <form method="GET" action="{{ route('student.danhsachphong') }}" class="flex items-center gap-2">
            <input name="q" value="{{ old('q', $tuKhoa ?? '') }}" type="text" placeholder="Tìm theo tên phòng"
                   class="rounded-lg border border-gray-300 p-2 text-sm" />
            <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">Tìm</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Tên phòng</th>
                    <th class="px-6 py-3">Giá phòng</th>
                    <th class="px-6 py-3">Số lượng tối đa</th>
                    <th class="px-6 py-3 text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachphong as $phong)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $phong->tenphong }}</td>
                        <td class="px-6 py-4">{{ number_format($phong->giaphong) }} đ</td>
                        <td class="px-6 py-4">{{ $phong->soluongtoida }}</td>
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
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-gray-500" colspan="4">Hiện chưa có phòng trống.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

