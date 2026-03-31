@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Lịch sử kỷ luật</div>
        <div class="text-sm text-gray-500">Danh sách vi phạm của bạn.</div>
    </div>

    <div class="space-y-4 md:hidden">
        @forelse ($kyluat as $item)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm font-semibold text-gray-900">{{ $item->mucdo }}</div>
                    <div class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">{{ $item->ngayvipham }}</div>
                </div>
                <div class="mt-3 text-sm text-gray-600">{{ $item->noidung }}</div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-4 text-center text-sm text-gray-500">
                Chưa có kỷ luật.
            </div>
        @endforelse
    </div>

    <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Nội dung</th>
                        <th class="px-6 py-3">Ngày vi phạm</th>
                        <th class="px-6 py-3">Mức độ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kyluat as $item)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-3">{{ $item->noidung }}</td>
                            <td class="px-6 py-3">{{ $item->ngayvipham }}</td>
                            <td class="px-6 py-3">{{ $item->mucdo }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-center text-gray-500" colspan="3">Chưa có kỷ luật.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
