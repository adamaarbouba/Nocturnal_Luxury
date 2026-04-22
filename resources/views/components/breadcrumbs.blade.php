@props(['links' => []])

@if(!empty($links))
    <nav class="flex items-center flex-wrap gap-2 text-[10px] font-bold uppercase tracking-widest mb-6" aria-label="Breadcrumb">
        @foreach($links as $index => $link)
            @if(!$loop->last)
                <a href="{{ $link['url'] }}" class="text-[#A0717F] hover:text-[#EAD3CD] transition-colors duration-200"
                    style="letter-spacing: 0.15em;">
                    {{ $link['label'] }}
                </a>
                <span class="text-[#4E3B46] mx-1">/</span>
            @else
                <span class="text-[#CFCBCA] opacity-70" aria-current="page" style="letter-spacing: 0.15em;">
                    {{ $link['label'] }}
                </span>
            @endif
        @endforeach
    </nav>
@endif
