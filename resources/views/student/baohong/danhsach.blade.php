@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6 flex items-end justify-between">
        <div>
            <div class="text-2xl font-bold text-gray-900">Báo hỏng</div>
            <div class="text-sm text-gray-500">Sinh viên gửi yêu cầu báo hỏng (kèm ảnh nếu có).</div>
        </div>

        <button type="button"
                data-modal-target="modal-thembaohong" data-modal-toggle="modal-thembaohong"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Thêm báo hỏng
        </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Mô tả</th>
                    <th class="px-6 py-3">Ảnh</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3">Ngày tạo</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($danhsachbaohong as $baohong)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $baohong->mota }}</td>
                        <td class="px-6 py-4">
                            @if ($baohong->anhminhhoa)
                                <a class="text-gray-900 underline" href="{{ asset($baohong->anhminhhoa) }}" target="_blank">Xem ảnh</a>
                            @else
                                <span class="text-gray-400">Không có</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $baohong->trangthai }}</td>
                        <td class="px-6 py-4">{{ $baohong->created_at }}</td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 text-gray-500" colspan="4">Em chưa gửi báo hỏng nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-thembaohong" tabindex="-1" aria-hidden="true"
         class="hidden fixed left-0 right-0 top-0 z-50 h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0">
        <div class="relative max-h-full w-full max-w-lg">
            <div class="relative rounded-lg bg-white shadow">
                <div class="flex items-start justify-between rounded-t border-b p-4">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm báo hỏng</h3>
                    <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900"
                            data-modal-hide="modal-thembaohong">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                        </svg>
                    </button>
                </div>

                <form class="p-4" method="POST" action="{{ route('student.thembaohong') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-900">Mô tả lỗi</label>
                            <textarea name="mota" rows="4"
                                      class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900"
                                      placeholder="Ví dụ: Bóng đèn hỏng, vòi nước rỉ...">{{ old('mota') }}</textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-900">Ảnh minh họa (nếu có)</label>
                            <input type="file" name="anhminhhoa"
                                   class="block w-full cursor-pointer rounded-lg border border-gray-300 text-sm text-gray-900" />
                        </div>
                    </div>

                    <button type="submit" class="mt-4 w-full rounded-lg bg-gray-900 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-gray-800">
                        Gửi báo hỏng
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

