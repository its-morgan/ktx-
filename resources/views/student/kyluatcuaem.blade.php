@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Lịch sử kỷ luật</div>
        <div class="text-sm text-[#606060]">Danh sách vi phạm của bạn.</div>
    </div>

    <div class="space-y-4 md:hidden">
        @forelse ($kyluat as $item)
            <div class="rounded-lg border border-gray-200/70 bg-white p-4 shadow-none">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm font-semibold text-[#121212]">{{ $item->mucdo }}</div>
                    <div class="rounded-full bg-[#F7F7F8] px-3 py-1 text-xs font-medium text-[#606060]">{{ $item->ngayvipham }}</div>
                </div>
                <div class="mt-3 text-sm text-[#606060]">{{ $item->noidung }}</div>
            </div>
        @empty
            <div class="rounded-lg border border-gray-200/70 bg-white p-4 text-center text-sm text-[#606060]">
                Chưa có kỷ luật.
            </div>
        @endforelse
    </div>

    <div class="hidden overflow-hidden rounded-lg border border-gray-200/70 bg-white md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#606060]">
                <thead class="bg-[#F7F7F8] text-xs uppercase text-[#606060]">
                    <tr>
                        <th class="px-6 py-3">Nội dung</th>
                        <th class="px-6 py-3">Ngày vi phạm</th>
                        <th class="px-6 py-3">Mức độ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kyluat as $item)
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-3">{{ $item->noidung }}</td>
                            <td class="px-6 py-3">{{ $item->ngayvipham }}</td>
                            <td class="px-6 py-3">{{ $item->mucdo }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200/70">
                            <td class="px-6 py-4 text-center text-[#606060]" colspan="3">Chưa có kỷ luật.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
