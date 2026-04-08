@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1 block text-xs font-medium uppercase tracking-wide text-[#606060]']) }}>
    {{ $value ?? $slot }}
</label>
