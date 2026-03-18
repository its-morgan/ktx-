@php
    $toast_loai = session('toast_loai');
    $toast_noidung = session('toast_noidung');
@endphp

@if ($toast_loai && $toast_noidung)
    @php
        $mau = $toast_loai === 'thanhcong' ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100';
        $tieude = $toast_loai === 'thanhcong' ? 'Thành công' : 'Lỗi';
    @endphp

    <div class="fixed bottom-5 right-5 z-50">
        <div id="toast-thongbao" class="flex w-full max-w-xs items-center rounded-lg bg-white p-4 text-gray-500 shadow" role="alert">
            <div class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg {{ $mau }}">
                <span class="text-xs font-bold">{{ $tieude }}</span>
            </div>
            <div class="ms-3 text-sm font-normal text-gray-700">{{ $toast_noidung }}</div>
            <button type="button"
                    class="-mx-1.5 -my-1.5 ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900"
                    data-dismiss-target="#toast-thongbao"
                    aria-label="Close">
                <span class="sr-only">Đóng</span>
                <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                </svg>
            </button>
        </div>
    </div>
@endif
