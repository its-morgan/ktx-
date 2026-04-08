@extends('layouts.app')

@section('title', 'Danh sach phong - He thong quan ly KTX')

@section('content')
<div class="min-h-screen bg-slate-50/50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="linear-page-title">Danh sach phong ky tuc xa</h1>
            <p class="linear-page-subtitle">Xem thong tin phong va vat tu truoc khi dang ky thue</p>
        </div>

        {{-- Filters --}}
        <div class="linear-panel mb-6 p-4">
            <form method="GET" action="{{ route('public.danhsachphong') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tim kiem</label>
                    <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Nhap ten phong..." class="linear-input mt-1 w-full">
                </div>
                <div class="w-40">
                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tang</label>
                    <select name="tang" class="linear-select mt-1 w-full">
                        <option value="">-- Tat ca --</option>
                        @foreach($danhsachtang as $tang)
                            <option value="{{ $tang }}" {{ $tangLoc == $tang ? 'selected' : '' }}>Tang {{ $tang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Gioi tinh</label>
                    <select name="gioitinh" class="linear-select mt-1 w-full">
                        <option value="">-- Tat ca --</option>
                        <option value="Nam" {{ $gioiTinhLoc == 'Nam' ? 'selected' : '' }}>Nam</option>
                        <option value="Nu" {{ $gioiTinhLoc == 'Nu' ? 'selected' : '' }}>Nu</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="linear-btn-primary">Loc ket qua</button>
                    <a href="{{ route('public.danhsachphong') }}" class="linear-btn-secondary">Xoa loc</a>
                </div>
            </form>
        </div>

        {{-- Room List by Floor --}}
        @forelse($phongTheoTang as $tang => $danhsachphong)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-8 w-8 rounded-lg bg-brand-100 flex items-center justify-center">
                        <span class="text-sm font-bold text-brand-700">{{ $tang }}</span>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-900">Tang {{ $tang }}</h2>
                    <span class="text-sm text-zinc-500">({{ $danhsachphong->count() }} phong)</span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($danhsachphong as $phong)
                        @php
                            $soluongdango = $soluongdango_theophong[$phong->id] ?? 0;
                            $sochocontrong = $phong->succhuamax - $soluongdango;
                            $daydu = $soluongdango >= $phong->succhuamax;
                            $phantram = $phong->succhuamax > 0 ? min(100, round($soluongdango / $phong->succhuamax * 100)) : 0;
                            $isFemale = $phong->gioitinh === 'Nu';
                        @endphp

                        <article class="linear-card group">
                            <div class="p-4">
                                {{-- Header --}}
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-bold text-slate-900">{{ $phong->tenphong }}</h3>
                                        <span class="text-lg {{ $isFemale ? 'text-pink-500' : 'text-blue-500' }}">
                                            {{ $isFemale ? '♀' : '♂' }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $daydu ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $daydu ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                                        {{ $daydu ? 'Day' : 'Con cho' }}
                                    </span>
                                </div>

                                {{-- Capacity Info --}}
                                <div class="mb-4 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-zinc-600">So nguoi dang o:</span>
                                        <span class="font-semibold text-slate-900">{{ $soluongdango }}/{{ $phong->succhuamax }}</span>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $daydu ? 'bg-red-500' : 'bg-brand-500' }}" style="width: {{ $phantram }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-zinc-500">Day: {{ $phantram }}%</span>
                                        <span class="font-semibold {{ $daydu ? 'text-red-600' : 'text-emerald-600' }}">
                                            Con trong: {{ $sochocontrong }} cho
                                        </span>
                                    </div>
                                </div>

                                {{-- Price & Info --}}
                                <div class="space-y-1 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-zinc-600">Gia phong:</span>
                                        <span class="font-semibold text-slate-900">{{ number_format($phong->giaphong, 0, ',', '.') }}đ/thang</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-zinc-600">Gioi tinh:</span>
                                        <span class="font-medium {{ $isFemale ? 'text-pink-600' : 'text-blue-600' }}">{{ $phong->gioitinh }}</span>
                                    </div>
                                </div>

                                @if($phong->mota)
                                    <p class="text-xs text-zinc-500 line-clamp-2 mb-4">{{ $phong->mota }}</p>
                                @endif

                                {{-- Actions --}}
                                <a href="{{ route('public.chitietvattu', $phong->id) }}" class="linear-btn-secondary w-full justify-center text-sm">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Xem vat tu phong
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="linear-panel py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-slate-900">Khong tim thay phong</h3>
                <p class="mt-1 text-sm text-zinc-500">Vui long thu lai voi dieu kien tim kiem khac.</p>
            </div>
        @endforelse

        {{-- Login CTA --}}
        <div class="linear-card mt-8 p-6 text-center">
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Muon dang ky phong?</h3>
            <p class="text-sm text-zinc-600 mb-4">Dang nhap de dang ky phong va quan ly hop dong cua ban.</p>
            <div class="flex justify-center gap-3">
                <a href="{{ route('login') }}" class="linear-btn-primary">Dang nhap</a>
                <a href="{{ route('register') }}" class="linear-btn-secondary">Dang ky tai khoan</a>
            </div>
        </div>
    </div>
</div>
@endsection
