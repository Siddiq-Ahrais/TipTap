@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#0B4A85] focus:ring-[#0B4A85] rounded-md shadow-sm']) }}>
