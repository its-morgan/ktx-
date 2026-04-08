@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-md border-l-2 border-black bg-[#F7F7F8] px-3 py-2 text-start text-sm font-medium text-[#121212] transition duration-150'
            : 'block w-full rounded-md border-l-2 border-transparent px-3 py-2 text-start text-sm font-medium text-[#606060] transition duration-150 hover:border-gray-200/80 hover:bg-[#F7F7F8] hover:text-[#121212]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
