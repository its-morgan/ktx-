@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b border-black px-1 py-4 text-sm font-medium text-[#121212] transition duration-150'
            : 'inline-flex items-center border-b border-transparent px-1 py-4 text-sm font-medium text-[#606060] transition duration-150 hover:border-gray-200/80 hover:text-[#121212]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
