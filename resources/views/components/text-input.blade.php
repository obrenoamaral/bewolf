@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-600 rounded-md shadow-sm bg-transparent text-gray-100']) }}>
