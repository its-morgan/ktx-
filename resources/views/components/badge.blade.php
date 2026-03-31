@props(['type' => 'default', 'text' => null])

@php
    $colorClass = match ($type) {
        'success' => 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800',
        'warning' => 'inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800',
        'danger' => 'inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800',
        'info' => 'inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800',
        default => 'inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700',
    };

    $label = $text ?? ucfirst($type);
@endphp

<span {{ $attributes->merge(['class' => $colorClass]) }}>{{ $label }}</span>
