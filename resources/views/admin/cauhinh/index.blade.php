@extends('admin.layouts.quantri')

@section('noidung')
    <div class="mb-6">
        <div class="text-2xl font-bold text-gray-900">Cấu hình hệ thống</div>
        <div class="text-sm text-gray-500">Cập nhật giá điện, giá nước và hotline.</div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.capnhatcauhinh') }}" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Giá điện (đồng/kWh)</label>
                    <input type="number" min="0" step="0.01" name="gia_dien"
                           value="{{ old('gia_dien', $cauhinh['gia_dien']->giatri ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Giá nước (đồng/m3)</label>
                    <input type="number" min="0" step="0.01" name="gia_nuoc"
                           value="{{ old('gia_nuoc', $cauhinh['gia_nuoc']->giatri ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Hotline</label>
                    <input type="text" name="hotline"
                           value="{{ old('hotline', $cauhinh['hotline']->giatri ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
                </div>
            </div>

            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Lưu cấu hình
            </button>
        </form>
    </div>
@endsection
