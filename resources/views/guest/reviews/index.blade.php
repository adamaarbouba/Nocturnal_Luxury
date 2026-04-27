@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>
    <div class="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="max-w-6xl mx-auto px-6 py-12 relative z-10">
        {{-- Elegant Navigation --}}
        <div class="mb-12">
            <x-breadcrumbs :links="[
                ['label' => 'Guest Dashboard', 'url' => route('guest.dashboard')],
                ['label' => 'Review Gallery', 'url' => '#'],
            ]" />
        </div>

        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Perspectives</p>
                <h1 class="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">My Review Gallery</h1>
                <p class="text-xs uppercase mt-4" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                    Your curated reflections on past stays
                </p>
            </div>
            <a href="{{ route('guest.bookings.index') }}"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-xl shrink-0"
                style="background-color: #A0717F; color: #FFFFFF;"
                onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                <x-icon name="building" size="sm" class="w-4 h-4" />
                Review More Stays
            </a>
        </div>

        @if ($reviews->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($reviews as $review)
                    <div class="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl shadow-xl hover:shadow-2xl hover:border-[#A0717F]/50 transition-all duration-500 overflow-hidden relative flex flex-col">
                        
                        {{-- Decorative Accent --}}
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none transition-all duration-500 group-hover:from-[#A0717F]/20"></div>
                        
                        <div class="p-8 flex-1 flex flex-col">
                            <!-- Establishment & Rating Header -->
                            <div class="flex justify-between items-start mb-6">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Establishment</p>
                                    <h3 class="text-2xl font-bold font-serif text-[#EAD3CD]">{{ $review->hotel->name }}</h3>
                                    <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70">
                                        {{ $review->hotel->city }}, {{ $review->hotel->country }}
                                    </p>
                                </div>
                                <div class="text-right flex flex-col items-end gap-2">
                                    <div class="flex gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-icon name="star" size="sm" class="{{ $i <= $review->rating ? 'text-[#A0717F] fill-[#A0717F]' : 'text-[#4E3B46] fill-transparent' }} transition-colors duration-300" />
                                        @endfor
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#EAD3CD]">{{ $review->rating }}.0 Excellence</span>
                                </div>
                            </div>

                            <!-- Narrative Snippet -->
                            <div class="mb-8 flex-1">
                                <p class="text-[#CFCBCA] text-sm leading-relaxed italic relative pl-4 border-l-2 border-[#A0717F]/30">
                                    "{{ Str::limit($review->comment, 150) }}"
                                </p>
                            </div>

                            <!-- Details Footer -->
                            <div class="flex items-end justify-between pt-6 border-t border-[#4E3B46]/50">
                                <div class="space-y-2">
                                    @if ($review->room)
                                        <div class="flex items-center gap-2">
                                            <x-icon name="building" size="xs" class="text-[#A0717F]" />
                                            <p class="text-[10px] font-medium text-[#CFCBCA] uppercase tracking-widest">
                                                Room {{ $review->room->room_number }} <span class="opacity-50">({{ $review->room->room_type }})</span>
                                            </p>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <x-icon name="calendar" size="xs" class="text-[#A0717F]" />
                                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] opacity-60">
                                            Published {{ $review->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <a href="{{ route('guest.reviews.show', $review) }}"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#383537] border border-[#4E3B46] text-[#A0717F] group-hover:bg-[#A0717F] group-hover:text-white transition-all duration-300 transform group-hover:scale-110 shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($reviews->hasPages())
                <div class="mt-12">
                    {{ $reviews->links() }}
                </div>
            @endif
        @else
            <div class="rounded-3xl shadow-2xl p-16 text-center border border-[rgba(234,211,205,0.1)] mt-12 relative overflow-hidden" style="background-color: #2A2729;">
                <div class="absolute inset-0 opacity-5 pointer-events-none">
                    <x-icon name="sparkles" class="w-full h-full text-[#A0717F]" />
                </div>
                
                <div class="relative z-10">
                    <div class="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                        <x-icon name="star" class="w-8 h-8 text-[#A0717F]" />
                    </div>
                    <h3 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">Your Gallery is Empty</h3>
                    <p class="text-xs uppercase tracking-widest mb-10 leading-loose" style="color: rgba(207, 203, 202, 0.5); max-w-md mx-auto;">
                        You have not yet contributed any reflections on your experiences. 
                        Your insights help refine the Nocturnal Luxury standard.
                    </p>
                    <a href="{{ route('guest.bookings.index') }}"
                        class="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1"
                        style="background-color: #A0717F;"
                        onmouseover="this.style.backgroundColor='#b58290';"
                        onmouseout="this.style.backgroundColor='#A0717F';">
                        Curate a Past Stay
                    </a>
                </div>
            </div>
        @endif

    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
@endsection
