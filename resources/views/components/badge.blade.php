{{-- Reusable Badge Component --}}
@php
    $variants = [
        'primary' => 'bg-[#D9B5C4]/10 text-[#D9B5C4]',
        'secondary' => 'bg-[#1A1515]/10 text-[#1A1515]',
        'accent' => 'bg-[#1A1515]/20 text-[#1A1515]',
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-yellow-100 text-yellow-700',
        'danger' => 'bg-red-100 text-red-700',
    ];

    $variant = $variant ?? 'primary';
@endphp

<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $variants[$variant] }}">
    {{ $slot }}
</span>




