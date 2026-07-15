@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Hotel Hero -->
        <div class="relative overflow-hidden rounded-2xl mb-12"
            style="background: linear-gradient(135deg, #A0717F 0%, #2A2729 100%); height: 320px;">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="relative z-10 h-full flex flex-col items-start justify-end p-8">
                <h1 class="text-5xl font-black text-[#EAD3CD] mb-2">{{ $hotel->name }}</h1>
                <div class="flex items-center gap-4">
                    <p class="text-[#CFCBCA]">{{ $hotel->city }}, {{ $hotel->country }}</p>
                    @if ($hotel->rating)
                        <div class="flex items-center gap-1 bg-[#1A1515]/50 px-3 py-1 rounded-lg">
                            <span class="text-[#EAD3CD]">★</span>
                            <span class="text-[#EAD3CD] font-semibold">{{ number_format($hotel->rating, 1) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hotel Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Main Info -->
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 shadow-lg">
                    <h2 class="text-2xl font-bold text-[#EAD3CD] mb-4">About This Hotel</h2>
                    <p class="text-[#EAD3CD] leading-relaxed mb-6">{{ $hotel->description }}</p>

                    <!-- Hotel Details Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pt-6 border-t border-[#4E3B46]">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Address</h3>
                            <p class="text-[#EAD3CD] mt-1">{{ $hotel->address }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">City</h3>
                            <p class="text-[#EAD3CD] mt-1">{{ $hotel->city }}, {{ $hotel->country }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Phone</h3>
                            <p class="text-[#EAD3CD] mt-1">{{ $hotel->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Email</h3>
                            <p class="text-[#EAD3CD] mt-1">{{ $hotel->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Guest Reviews -->
                @if ($hotel->reviews->count() > 0)
                    <div class="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 mt-8 shadow-lg">
                        <h2 class="text-2xl font-bold text-[#EAD3CD] mb-6">Guest Reviews</h2>
                        <div class="space-y-4">
                            @foreach ($hotel->reviews->take(8) as $review)
                                <div class="border-b border-[#4E3B46] pb-4 last:border-b-0">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="font-semibold text-[#EAD3CD]">{{ $review->user->name }}</h3>
                                            <p class="text-xs text-[#CFCBCA]">{{ $review->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex text-[#A0717F]">
                                            @for ($i = 0; $i < $review->rating; $i++)
                                                ★
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-[#EAD3CD] text-sm">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Stats -->
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 sticky top-4 shadow-lg">
                    <h3 class="text-xl font-bold text-[#EAD3CD] mb-4">Room Overview</h3>

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-[#2A2729] rounded-lg p-3 border border-[#4E3B46]">
                            <p class="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold">Available</p>
                            <p class="text-2xl font-bold text-[#EAD3CD] mt-1">{{ $availableRooms->count() }}</p>
                        </div>
                        <div class="bg-[#2A2729] rounded-lg p-3 border border-[#4E3B46]">
                            <p class="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold">Room Types</p>
                            <p class="text-2xl font-bold text-[#EAD3CD] mt-1">
                                {{ $availableRooms->groupBy('room_type')->count() }}</p>
                        </div>
                    </div>

                    @if ($availableRooms->count() > 0)
                        @php
                            $priceRange = $availableRooms->min('price_per_night');
                            $maxPrice = $availableRooms->max('price_per_night');
                        @endphp
                        <div class="bg-[#2A2729] rounded-lg p-4 border border-[#4E3B46]">
                            <p class="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold mb-2">Price Range</p>
                            @if ($priceRange === $maxPrice)
                                <p class="text-2xl font-bold text-[#A0717F]">${{ number_format($priceRange, 2) }}<span class="text-sm font-normal text-[#CFCBCA]">/night</span></p>
                            @else
                                <p class="text-lg font-bold text-[#A0717F]">${{ number_format($priceRange, 2) }} -
                                    ${{ number_format($maxPrice, 2) }}<span class="text-sm font-normal text-[#CFCBCA]">/night</span></p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Rooms Section -->
        <div class="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 shadow-lg">
            <h2 class="text-2xl font-bold text-[#EAD3CD] mb-8">Available Rooms</h2>

            @if ($availableRooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($availableRooms as $room)
                        <div
                            class="border border-[#4E3B46] bg-[#2A2729] rounded-xl overflow-hidden hover:border-[#A0717F] hover:shadow-lg transition group">
                            <!-- Room Image Placeholder -->
                            <div
                                class="h-40 bg-gradient-to-br from-[#1A1515]/40 to-[#1A1515]/20 flex items-center justify-center overflow-hidden group-hover:from-[#1A1515]/60 group-hover:to-[#1A1515]/40 transition">
                                <x-icon name="building" size="2xl" class="text-[#EAD3CD]/50" />
                            </div>

                            <!-- Room Info -->
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="text-lg font-bold text-[#EAD3CD]">{{ $room->room_type }}</h3>
                                        <p class="text-xs text-[#CFCBCA]">Room {{ $room->room_number }}</p>
                                    </div>
                                </div>

                                <!-- Room Details -->
                                <div class="text-sm text-[#EAD3CD] mb-4 space-y-1 pb-4 border-b border-[#4E3B46]">
                                    <p>Capacity: {{ $room->capacity }} {{ $room->capacity > 1 ? 'guests' : 'guest' }}
                                    </p>
                                    <p>Floor {{ $room->floor ?? 'N/A' }}</p>
                                </div>

                                <!-- Price -->
                                <div class="mb-4">
                                    <p class="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold">Price per Night
                                    </p>
                                    <p class="text-2xl font-bold text-[#A0717F] mt-1">
                                        ${{ number_format($room->price_per_night, 2) }}</p>
                                </div>

                                <!-- Book Button -->
                                <a href="{{ route('guest.bookings.create', $room) }}"
                                    class="w-full block text-center bg-[#A0717F] hover:bg-[#8F6470] text-[#EAD3CD] py-2 rounded-lg font-semibold transition border border-[#A0717F]">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <p class="text-[#EAD3CD] text-lg">No rooms currently available at this hotel.</p>
                </div>
            @endif
        </div>
    </div>
@endsection




