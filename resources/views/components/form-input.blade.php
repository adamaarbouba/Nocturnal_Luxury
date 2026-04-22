{{-- Reusable Form Input Component --}}
<div class="mb-6">
    @if ($label ?? false)
        <label for="{{ $name ?? '' }}" class="block text-sm font-semibold text-[#CFCBCA] mb-2">
            {{ $label }}
            @if ($required ?? false)
                <span class="text-[#A0717F]">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea id="{{ $name ?? '' }}" name="{{ $name ?? '' }}" rows="{{ $rows ?? 4 }}"
            placeholder="{{ $placeholder ?? '' }}"
            class="w-full px-4 py-3 border border-[#4E3B46] bg-[#2A2729] rounded-lg text-[#CFCBCA] placeholder:text-[#CFCBCA]/60 focus:outline-none focus:border-[#A0717F] focus:ring-2 focus:ring-[#A0717F]/20 transition"
            {{ $attributes }}>{{ $value ?? '' }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $name ?? '' }}" name="{{ $name ?? '' }}"
            class="w-full px-4 py-3 border border-[#4E3B46] bg-[#2A2729] rounded-lg text-[#CFCBCA] focus:outline-none focus:border-[#A0717F] focus:ring-2 focus:ring-[#A0717F]/20 transition"
            {{ $attributes }}>
            {{ $slot }}
        </select>
    @else
        <input type="{{ $type ?? 'text' }}" id="{{ $name ?? '' }}" name="{{ $name ?? '' }}"
            value="{{ $value ?? '' }}" placeholder="{{ $placeholder ?? '' }}"
            class="w-full px-4 py-3 border border-[#4E3B46] bg-[#2A2729] rounded-lg text-[#CFCBCA] placeholder:text-[#CFCBCA]/60 focus:outline-none focus:border-[#A0717F] focus:ring-2 focus:ring-[#A0717F]/20 transition"
            {{ $attributes }} />
    @endif

    @if ($hint ?? false)
        <p class="mt-1 text-xs text-[#CFCBCA]">{{ $hint }}</p>
    @endif
</div>




