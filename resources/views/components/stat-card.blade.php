{{-- Dashboard Stat Card Component --}}
<div class="rounded-lg border border-[#1A1515]  p-6 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-4">
        @if ($icon ?? false)
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg"
                style="background-color: #D9B5C4; color: white;">
                {{ $icon }}
            </div>
        @endif

        @if ($trend ?? false)
            <span
                class="text-xs font-semibold px-2 py-1 rounded-full {{ $trend['positive'] ?? false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $trend['text'] }}
            </span>
        @endif
    </div>

    <h3 class="text-sm font-semibold text-[#1A1515] mb-1">{{ $label }}</h3>
    <p class="text-3xl font-bold text-[#1A1515]">{{ $value }}</p>

    @if ($footer ?? false)
        <p class="text-xs text-[#1A1515] mt-3">{{ $footer }}</p>
    @endif
</div>




