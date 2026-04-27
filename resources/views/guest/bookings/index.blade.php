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
                ['label' => 'My Itinerary', 'url' => '#'],
            ]" />
        </div>

        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Reservations</p>
                <h1 class="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">My Itinerary</h1>
                <p class="text-xs uppercase mt-4" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                    Your upcoming escapes and past journeys
                </p>
            </div>
            <a href="{{ route('guest.hotels.index') }}"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-xl shrink-0"
                style="background-color: #A0717F; color: #FFFFFF;"
                onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                <x-icon name="building" size="sm" class="w-4 h-4" />
                Reserve New Stay
            </a>
        </div>

        <!-- Current Bookings Section -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
                <span class="w-8 h-[1px] bg-[#A0717F]"></span>
                Current & Upcoming
                <span class="text-xs font-bold font-sans uppercase tracking-[0.2em] text-[#A0717F] opacity-60 bg-[#A0717F]/10 px-3 py-1 rounded-full">{{ $currentBookings->count() }}</span>
            </h2>

            @if ($currentBookings->count() > 0)
                <div class="grid grid-cols-1 gap-8">
                    @foreach ($currentBookings as $booking)
                        @php
                            $firstItem = $booking->bookingItems->first();
                            $room = $firstItem?->room;
                            $nights = $booking->check_in_date->diff($booking->check_out_date)->days;
                        @endphp
                        <div class="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl shadow-xl hover:shadow-2xl hover:border-[#A0717F]/50 transition-all duration-500 overflow-hidden relative">
                            {{-- Decorative Accent --}}
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none transition-all duration-500 group-hover:from-[#A0717F]/20"></div>

                            <div class="p-8 lg:p-10">
                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                                    
                                    <!-- Identity -->
                                    <div class="lg:col-span-1 space-y-4 border-b lg:border-b-0 lg:border-r border-[#4E3B46]/50 pb-6 lg:pb-0 lg:pr-6">
                                        <div>
                                            <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Establishment</p>
                                            <h3 class="text-2xl font-bold font-serif text-[#EAD3CD]">{{ $booking->hotel->name }}</h3>
                                            @if ($booking->hotel->city)
                                                <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70 mt-1">{{ $booking->hotel->city }}</p>
                                            @endif
                                        </div>
                                        
                                        @if ($room)
                                            <div class="pt-4">
                                                <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Accommodation</p>
                                                <p class="text-sm font-semibold text-[#EAD3CD]">Room {{ $room->room_number }}</p>
                                                <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70 mt-1">{{ $room->room_type }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Itinerary -->
                                    <div class="lg:col-span-1 border-b lg:border-b-0 lg:border-r border-[#4E3B46]/50 pb-6 lg:pb-0 lg:px-6 flex flex-col justify-center">
                                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-4">Journey</p>
                                        <div class="space-y-4">
                                            <div>
                                                <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-60">Arrival</p>
                                                <p class="text-sm font-semibold text-[#EAD3CD]">{{ $booking->check_in_date->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-60">Departure</p>
                                                <p class="text-sm font-semibold text-[#EAD3CD]">{{ $booking->check_out_date->format('M d, Y') }}</p>
                                            </div>
                                            <div class="inline-block px-3 py-1 bg-[#383537] rounded text-[10px] uppercase tracking-widest text-[#A0717F] font-bold">
                                                {{ $nights }} Nights Duration
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Finances & Status -->
                                    <div class="lg:col-span-1 space-y-6 pb-6 lg:pb-0 lg:px-6 flex flex-col justify-center border-b lg:border-b-0 lg:border-r border-[#4E3B46]/50">
                                        <div>
                                            <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Investment</p>
                                            <p class="text-3xl font-bold font-serif text-[#EAD3CD]">${{ number_format($booking->total_amount, 2) }}</p>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-[9px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mb-1">Reservation Status</p>
                                                <span class="inline-block px-3 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest border
                                                    @if ($booking->status === 'pending') bg-yellow-500/10 text-yellow-400 border-yellow-500/30
                                                    @elseif($booking->status === 'confirmed') bg-green-500/10 text-green-400 border-green-500/30
                                                    @elseif($booking->status === 'checked_in') bg-blue-500/10 text-blue-400 border-blue-500/30 @endif">
                                                    {{ str_replace('_', ' ', $booking->status) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-[9px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mb-1">Ledger Status</p>
                                                <span class="inline-block px-3 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest border
                                                    @if ($booking->payment_status === 'pending') bg-orange-500/10 text-orange-400 border-orange-500/30
                                                    @elseif($booking->payment_status === 'paid') bg-green-500/10 text-green-400 border-green-500/30
                                                    @elseif($booking->payment_status === 'partial') bg-yellow-500/10 text-yellow-400 border-yellow-500/30
                                                    @elseif($booking->payment_status === 'refunded') bg-[#2A2729] text-[#CFCBCA] border-[#4E3B46] @endif">
                                                    {{ $booking->payment_status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="lg:col-span-1 flex flex-col justify-center gap-4 lg:pl-6">
                                        <a href="{{ route('guest.bookings.confirmation', $booking) }}"
                                            class="w-full text-center px-6 py-4 rounded-xl border border-[#4E3B46] text-[#CFCBCA] hover:text-[#EAD3CD] hover:bg-[#383537] transition-all duration-300 text-[10px] font-bold uppercase tracking-widest">
                                            View Dossier
                                        </a>

                                        @if($booking->remainingBalance() > 0)
                                            <a href="{{ route('guest.payments.form', $booking) }}"
                                                class="w-full text-center bg-[#A0717F] hover:bg-[#8F6470] text-white text-[10px] font-bold uppercase tracking-widest px-6 py-4 rounded-xl transition-all duration-300 shadow-xl transform hover:-translate-y-1">
                                                Settle Balance
                                            </a>
                                        @else
                                            <a href="{{ route('guest.payments.form', $booking) }}"
                                                class="w-full text-center px-6 py-4 rounded-xl border border-[#4E3B46] text-[#A0717F] hover:bg-[#A0717F] hover:text-white transition-all duration-300 text-[10px] font-bold uppercase tracking-widest">
                                                Manage Ledger
                                            </a>
                                        @endif
                                    </div>
                                    
                                </div>
                                
                                @if ($booking->special_requests)
                                    <div class="mt-8 pt-6 border-t border-[#4E3B46]/30">
                                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-2">Special Concierge Requests</p>
                                        <p class="text-[#CFCBCA] text-sm italic border-l-2 border-[#A0717F]/30 pl-4">"{{ $booking->special_requests }}"</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl p-16 text-center border border-[rgba(234,211,205,0.1)] relative overflow-hidden" style="background-color: #2A2729;">
                    <div class="relative z-10">
                        <div class="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                            <x-icon name="calendar" class="w-8 h-8 text-[#A0717F]" />
                        </div>
                        <h3 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">No Current Itineraries</h3>
                        <p class="text-xs uppercase tracking-widest mb-10 leading-loose" style="color: rgba(207, 203, 202, 0.5); max-w-md mx-auto;">
                            Your upcoming calendar is clear. Embark on a new journey of luxury.
                        </p>
                        <a href="{{ route('guest.hotels.index') }}"
                            class="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1"
                            style="background-color: #A0717F;">
                            Explore Destinations
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Past Bookings Section -->
        <div>
            <h2 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
                <span class="w-8 h-[1px] bg-[#A0717F]"></span>
                Historical Stays
                <span class="text-xs font-bold font-sans uppercase tracking-[0.2em] text-[#A0717F] opacity-60 bg-[#A0717F]/10 px-3 py-1 rounded-full">{{ $pastBookings->count() }}</span>
            </h2>

            @if ($pastBookings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($pastBookings as $booking)
                        @php
                            $firstItem = $booking->bookingItems->first();
                            $room = $firstItem?->room;
                            $nights = $booking->check_in_date->diff($booking->check_out_date)->days;
                        @endphp
                        <div class="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 hover:border-[#A0717F]/50 transition-all duration-500 flex flex-col relative overflow-hidden">
                            {{-- Accent --}}
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none transition-all duration-500 group-hover:from-[#A0717F]/10"></div>
                            
                            <div class="flex-1 space-y-6 relative z-10">
                                <div>
                                    <h3 class="text-xl font-bold font-serif text-[#EAD3CD] mb-1">{{ $booking->hotel->name }}</h3>
                                    @if ($booking->hotel->city)
                                        <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70">{{ $booking->hotel->city }}</p>
                                    @endif
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-[10px] uppercase tracking-widest text-[#CFCBCA] opacity-60">Duration</span>
                                        <span class="font-medium text-[#EAD3CD]">{{ $booking->check_in_date->format('M Y') }} ({{ $nights }} Nights)</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-[10px] uppercase tracking-widest text-[#CFCBCA] opacity-60">Status</span>
                                        <span class="text-[10px] font-bold uppercase tracking-widest 
                                            @if($booking->status === 'completed' || $booking->status === 'checked_out') text-[#A0717F]
                                            @elseif($booking->status === 'cancelled') text-red-400 @endif">
                                            {{ str_replace('_', ' ', $booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-[#4E3B46]/50 flex flex-col gap-3 relative z-10">
                                <a href="{{ route('guest.bookings.confirmation', $booking) }}"
                                    class="w-full text-center px-4 py-3 rounded-xl border border-[#4E3B46] text-[#EAD3CD] hover:bg-[#383537] transition-all duration-300 text-[9px] font-bold uppercase tracking-[0.2em]">
                                    Review Receipt
                                </a>
                                @if ($booking->status === 'completed' || $booking->status === 'checked_out')
                                    <a href="{{ route('guest.reviews.create', $booking) }}"
                                        class="w-full text-center bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] px-4 py-3 rounded-xl transition-all duration-300 text-[9px] font-bold uppercase tracking-[0.2em] shadow-lg flex items-center justify-center gap-2">
                                        <x-icon name="star" size="xs" class="text-[#A0717F]" />
                                        Share Reflections
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 px-6 border border-[#4E3B46]/30 rounded-3xl">
                    <p class="text-[#CFCBCA] text-[10px] uppercase tracking-widest opacity-60">Your historical ledger is currently unwritten.</p>
                </div>
            @endif
        </div>

    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
@endsection
