@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-[#121212]">Cấu hình hệ thống</div>
        <div class="text-sm text-[#606060]">Cập nhật giá điện, giá nước và hotline.</div>
    </div>

    <div class="rounded-lg border border-gray-200/70 bg-white p-6">
        <form method="POST" action="{{ route('admin.capnhatcauhinh') }}" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#606060]">Giá điện (đồng/kWh)</label>
                    <input type="number" min="0" step="0.01" name="gia_dien"
                           value="{{ old('gia_dien', $cauhinh['gia_dien']->giatri ?? '') }}"
                           class="w-full rounded-lg border border-gray-200/80 px-3 py-2" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#606060]">Giá nước (đồng/m3)</label>
                    <input type="number" min="0" step="0.01" name="gia_nuoc"
                           value="{{ old('gia_nuoc', $cauhinh['gia_nuoc']->giatri ?? '') }}"
                           class="w-full rounded-lg border border-gray-200/80 px-3 py-2" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#606060]">Hotline</label>
                    <input type="text" name="hotline"
                           value="{{ old('hotline', $cauhinh['hotline']->giatri ?? '') }}"
                           class="w-full rounded-lg border border-gray-200/80 px-3 py-2" required>
                </div>
            </div>

            <button type="submit" class="rounded-lg bg-black px-5 py-2 text-sm font-semibold text-white hover:bg-[#1C1C1C]">
                Lưu cấu hình
            </button>
        </form>
    </div>
@endsection
