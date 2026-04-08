@extends('student.layouts.chinh')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Đánh giá phòng</div>
        <div class="text-sm text-[#606060]">Chia sẻ trải nghiệm của bạn về phòng ở.</div>
    </div>

    @if($daDanhGia)
        <div class="rounded-xl border border-green-200 bg-green-50 p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-green-800">Cảm ơn bạn đã đánh giá!</h3>
            <p class="text-sm text-green-600">Bạn đã đánh giá phòng trong tháng này. Vui lòng quay lại vào tháng sau.</p>
            <a href="{{ route('student.phongcuatoi') }}" class="mt-4 inline-block rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                Quay lại Phòng của tôi
            </a>
        </div>
    @else
        <div class="rounded-xl border border-gray-200/70 bg-white p-6">
            @if($phong)
                <div class="mb-6 rounded-lg bg-gray-50 p-4">
                    <div class="text-sm text-[#606060]">Đánh giá cho phòng</div>
                    <div class="text-lg font-semibold text-[#121212]">{{ $phong->tenphong }}</div>
                    <div class="text-sm text-[#606060]">Tầng {{ $phong->tang }}</div>
                </div>

                <form method="POST" action="{{ route('student.themdanhgia') }}">
                    @csrf
                    
                    {{-- Điểm đánh giá --}}
                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-medium text-[#121212]">Số sao đánh giá</label>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="diem" value="{{ $i }}" class="peer sr-only" {{ old('diem') == $i ? 'checked' : '' }} required>
                                    <svg class="h-8 w-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </label>
                            @endfor
                        </div>
                        @error('diem')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nội dung --}}
                    <div class="mb-6">
                        <label for="noidung" class="mb-2 block text-sm font-medium text-[#121212]">Nội dung đánh giá (tùy chọn)</label>
                        <textarea name="noidung" id="noidung" rows="4" class="w-full rounded-lg border border-gray-200/80 p-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="Chia sẻ trải nghiệm của bạn về phòng ở...">{{ old('noidung') }}</textarea>
                        @error('noidung')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3">
                        <button type="submit" class="rounded-lg bg-gray-900 px-6 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            Gửi đánh giá
                        </button>
                        <a href="{{ route('student.phongcuatoi') }}" class="rounded-lg border border-gray-200/80 px-6 py-2 text-sm font-medium text-[#606060] hover:bg-gray-50">
                            Hủy
                        </a>
                    </div>
                </form>
            @else
                <div class="text-center">
                    <p class="text-sm text-[#606060]">Bạn chưa có phòng để đánh giá.</p>
                    <a href="{{ route('student.danhsachphong') }}" class="mt-4 inline-block rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Tìm phòng
                    </a>
                </div>
            @endif
        </div>
    @endif
@endsection
