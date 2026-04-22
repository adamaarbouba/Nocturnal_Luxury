{{-- Reusable Button Component with Color.xml Palette --}}
@php
    $variants = [
        'primary' => 'bg-[#A0717F] text-white hover:bg-[#8F6470] hover:shadow-lg',
        'secondary' => 'text-[#CFCBCA] border border-[#4E3B46] hover:bg-[#383537]',
        'outline' => 'bg-transparent text-[#A0717F] border border-[#A0717F] hover:bg-[#A0717F] hover:text-white',
        'subtle' => 'bg-transparent text-[#CFCBCA] hover:text-[#EAD3CD]',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'xl' => 'px-8 py-4 text-lg',
    ];

    $variant = $variant ?? 'primary';
    $size = $size ?? 'md';
    $rounded = $rounded ?? 'rounded-lg';
@endphp

<button
    {{ $attributes->merge(['class' => "{$variants[$variant]} {$sizes[$size]} {$rounded} font-medium transition-all duration-200 flex items-center justify-center gap-2"]) }}>
    {{ $slot }}
</button>




