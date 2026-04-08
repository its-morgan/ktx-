@props([
    'lines' => 3,
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @for ($i = 0; $i < $lines; $i++)
        <div class="linear-skeleton h-4 {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
    @endfor
</div>
