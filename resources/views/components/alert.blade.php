{{-- Reusable Alert Component --}}
@php
    $variants = [
        'success' => 'bg-[#1A1515]/10 text-[#1A1515] border-[#1A1515]/30',
        'error' => 'bg-[#D9B5C4]/10 text-[#D9B5C4] border-[#D9B5C4]/30',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
        'info' => 'bg-[#1A1515]/20 text-[#1A1515] border-[#1A1515]/50',
    ];

    $variant = $variant ?? 'info';
    $dismissible = $dismissible ?? true;
    $id = 'alert-' . uniqid();
@endphp

<div id="{{ $id }}" class="p-4 border rounded-lg {{ $variants[$variant] }} flex items-start gap-3"
    role="alert">
    <div class="flex-1">
        @if ($title ?? false)
            <h4 class="font-semibold mb-1">{{ $title }}</h4>
        @endif

        <p class="text-sm">{{ $slot }}</p>
    </div>

    @if ($dismissible)
        <button type="button" onclick="document.getElementById('{{ $id }}').remove()"
            class="text-sm font-semibold hover:opacity-70 transition">
            ✕
        </button>
    @endif
</div>




