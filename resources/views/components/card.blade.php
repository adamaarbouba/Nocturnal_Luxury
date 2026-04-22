{{-- Reusable Card Component using Color.xml Palette --}}
@php
    $cardClass = $class ?? '';
@endphp

<div class=" rounded-[1.75rem] border border-[#4E3B46] bg-[#383537] shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden {{ $cardClass }}">
    @if (isset($image))
        <div class="relative overflow-hidden h-48 bg-gradient-to-br from-[#4E3B46] to-[#383537]">
            <img src="{{ $image }}" alt="{{ $title ?? 'Card' }}"
                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
        </div>
    @endif

    <div class="p-6">
        @if (isset($badge))
            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-2"
                style="background-color: #A0717F; color: white;">
                {{ $badge }}
            </span>
        @endif

        @if (isset($title))
            <h3 class="text-lg font-bold text-[#EAD3CD] mb-2">{{ $title }}</h3>
        @endif

        @if (isset($subtitle))
            <p class="text-sm text-[#CFCBCA] mb-3 flex items-center gap-2">{{ $subtitle }}</p>
        @endif

        @if (isset($content))
            <p class="text-sm text-[#CFCBCA] leading-relaxed mb-4">{{ $content }}</p>
        @else
            <div class="text-sm text-[#CFCBCA] leading-relaxed">
                {{ $slot }}
            </div>
        @endif

        @if (isset($footer))
            <div class="mt-4 pt-4 border-t border-[#4E3B46] flex justify-between items-center">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>




