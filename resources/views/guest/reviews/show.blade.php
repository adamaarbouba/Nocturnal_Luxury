@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-12">
        {{-- Elegant Navigation --}}
        <div class="mb-12">
            <x-breadcrumbs :links="[
                ['label' => 'My Reviews', 'url' => route('guest.reviews.index')],
                ['label' => 'Review Details', 'url' => '#'],
            ]" />
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            {{-- Right Aspect: Meta Information (Moved to top on Mobile) --}}
            <div class="lg:col-span-4 space-y-8 order-2 lg:order-1">
                <div class="p-8 rounded-2xl bg-[#383537] border border-[#4E3B46] shadow-xl space-y-8 sticky top-8">
                    <header class="space-y-2">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Establishment</p>
                        <h1 class="text-2xl font-bold font-serif text-[#EAD3CD] leading-tight">{{ $review->hotel->name }}</h1>
                        <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest">{{ $review->hotel->city }}, {{ $review->hotel->country }}</p>
                    </header>

                    <div class="space-y-6 pt-6 border-t border-[#4E3B46]">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase tracking-widest text-[#CFCBCA]">Reservation</span>
                            <span class="text-xs font-medium text-[#EAD3CD]">#{{ $review->booking_id }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase tracking-widest text-[#CFCBCA]">Stay Period</span>
                            <span class="text-xs font-medium text-[#EAD3CD]">{{ $review->booking->check_in_date->format('M d') }} — {{ $review->booking->check_out_date->format('d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase tracking-widest text-[#CFCBCA]">Status</span>
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest 
                                @if($review->booking->status === 'completed') bg-green-500/10 text-green-400 border border-green-500/20 
                                @else bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] @endif">
                                {{ $review->booking->status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5 rounded-xl bg-[#2A2729] border border-[#4E3B46] space-y-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA]/60">Room Segment</p>
                        @if($review->room)
                            <div class="flex items-center gap-3">
                                <x-icon name="building" size="sm" class="text-[#A0717F]" />
                                <p class="text-xs text-[#EAD3CD] font-medium">Room {{ $review->room->room_number }} ({{ $review->room->room_type }})</p>
                            </div>
                        @else
                            <p class="text-xs text-[#CFCBCA] italic">Not specified</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Left Aspect: The Review Narrative --}}
            <div class="lg:col-span-8 space-y-12 order-1 lg:order-2">
                {{-- Overall Experience Card --}}
                <div class="p-10 lg:p-12 rounded-3xl bg-[#2A2729] border border-[#4E3B46] shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <x-icon name="sparkles" size="2xl" class="text-[#A0717F]" />
                    </div>

                    <div class="space-y-12">
                        <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="space-y-2">
                                <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#A0707F]">Reflections</p>
                                <h2 class="text-4xl font-bold font-serif text-[#EAD3CD]">Overall Impression</h2>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <div class="flex gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <x-icon name="star" size="md" class="{{ $i <= $review->rating ? 'text-[#A0717F] fill-[#A0717F]' : 'text-[#4E3B46] fill-transparent' }}" />
                                    @endfor
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA] opacity-60">{{ $review->rating }} / 5 Excellence</p>
                            </div>
                        </header>

                        {{-- Narrative --}}
                        <div class="space-y-6">
                            <p class="text-xl lg:text-2xl font-serif italic text-[#EAD3CD] leading-relaxed relative">
                                <span class="absolute -left-6 -top-4 text-4xl text-[#A0717F]/20 font-serif">“</span>
                                {{ $review->comment }}
                                <span class="text-4xl text-[#A0717F]/20 font-serif inline-block translate-y-2">”</span>
                            </p>
                            <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-[#CFCBCA]/30">Narrative Published on {{ $review->created_at->format('M d, Y') }}</p>
                        </div>


                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="flex items-center gap-6 pt-6">
                    <a href="{{ route('guest.reviews.index') }}" 
                        class="px-10 py-5 rounded-2xl bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.3em] text-xs transition-all duration-500 shadow-xl transform hover:-translate-y-1">
                        Return to Gallery
                    </a>
                    <a href="{{ route('guest.bookings.index') }}" 
                        class="px-10 py-5 rounded-2xl border border-[#4E3B46] text-[#CFCBCA] hover:text-[#EAD3CD] hover:bg-[#383537] transition-all duration-500 text-xs font-bold uppercase tracking-widest">
                        Manage Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
@endsection
