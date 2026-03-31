@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
