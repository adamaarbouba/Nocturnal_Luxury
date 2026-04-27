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
                ['label' => 'Destinations', 'url' => '#'],
            ]" />
        </div>

        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Explore</p>
                <h1 class="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">Find Your Sanctuary</h1>
                <p class="text-xs uppercase mt-4" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                    Curated destinations for the discerning traveler
                </p>
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="bg-[#2A2729] rounded-3xl shadow-xl p-8 lg:p-10 mb-12 border border-[#4E3B46] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none"></div>

            <form method="GET" action="{{ route('guest.hotels.index') }}" class="relative z-10" id="filter-form">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-4">
                        <label for="search" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Destination or Hotel</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <x-icon name="search" size="sm" class="text-[#A0717F]/50" />
                            </div>
                            <input type="text" id="search" name="search" placeholder="Where to?"
                                value="{{ $search ?? '' }}"
                                class="w-full pl-12 pr-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 placeholder-[#CFCBCA]/30">
                        </div>
                    </div>

                    <!-- City Filter -->
                    <div class="lg:col-span-3">
                        <label for="city" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Location</label>
                        <select id="city" name="city"
                            class="w-full px-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 appearance-none cursor-pointer">
                            <option value="">Global (All Cities)</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" {{ ($selectedCity ?? '') === $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="lg:col-span-3 grid grid-cols-2 gap-4">
                        <div>
                            <label for="min_price" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Min ($)</label>
                            <input type="number" id="min_price" name="min_price" placeholder="Min" value="{{ $minPrice ?? '' }}" min="0"
                                class="w-full px-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 placeholder-[#CFCBCA]/30">
                        </div>
                        <div>
                            <label for="max_price" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Max ($)</label>
                            <input type="number" id="max_price" name="max_price" placeholder="Max" value="{{ $maxPrice ?? '' }}" min="0"
                                class="w-full px-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 placeholder-[#CFCBCA]/30">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="lg:col-span-2 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.2em] text-[10px] py-4 rounded-xl shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            Refine
                        </button>
                        <a href="{{ route('guest.hotels.index') }}"
                            class="w-full text-center py-3 text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-all duration-300">
                            Clear Filters
                        </a>
                    </div>
                </div>
            </form>
            
            <script>
                document.getElementById('filter-form').addEventListener('submit', function(e) {
                    const formData = new FormData(this);
                    const params = new URLSearchParams();

                    for (let [key, value] of formData.entries()) {
                        if (value && value.trim() !== '') {
                            params.append(key, value);
                        }
                    }

                    if (params.toString()) {
                        window.location.href = '{{ route('guest.hotels.index') }}?' + params.toString();
                    } else {
                        window.location.href = '{{ route('guest.hotels.index') }}';
                    }
                    e.preventDefault();
                });
            </script>
        </div>

        <!-- Hotels Grid -->
        <div>
            <h2 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
                <span class="w-8 h-[1px] bg-[#A0717F]"></span>
                Portfolio
                <span class="text-xs font-bold font-sans uppercase tracking-[0.2em] text-[#A0717F] opacity-60 bg-[#A0717F]/10 px-3 py-1 rounded-full">{{ $hotels->total() }}</span>
            </h2>

            @if ($hotels->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($hotels as $hotel)
                        <a href="{{ route('guest.hotels.show', $hotel) }}"
                            class="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl overflow-hidden hover:border-[#A0717F]/50 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col">
                            
                            <!-- Hotel Image Box -->
                            <div class="h-56 bg-[#383537] relative overflow-hidden flex items-center justify-center">
                                <div class="absolute inset-0 bg-gradient-to-tr from-[#A0717F]/20 to-transparent opacity-50 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <x-icon name="home" size="2xl" class="text-[#A0717F]/30 group-hover:scale-110 group-hover:text-[#A0717F]/60 transition-all duration-500 relative z-10 w-16 h-16" />
                                
                                <!-- Location Badge -->
                                <div class="absolute top-4 right-4 bg-[#2A2729]/80 backdrop-blur-sm border border-[#4E3B46] px-3 py-1.5 rounded-full z-20 flex items-center gap-1.5">
                                    <x-icon name="map-pin" size="xs" class="text-[#A0717F] w-3 h-3" />
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#EAD3CD]">{{ $hotel->city }}</span>
                                </div>
                            </div>

                            <!-- Hotel Info -->
                            <div class="p-8 flex flex-col flex-1 relative">
                                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-[#4E3B46] to-transparent"></div>
                                
                                <h3 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-3 group-hover:text-[#A0717F] transition-colors duration-300">{{ $hotel->name }}</h3>
                                <p class="text-[11px] text-[#CFCBCA] mb-6 line-clamp-2 leading-relaxed italic opacity-80 flex-1">
                                    "{{ Str::limit($hotel->description, 100) }}"
                                </p>

                                @php
                                    $availableRooms = $hotel->rooms()->where('status', 'Available')->count();
                                    $priceRange = $hotel->rooms()->where('status', 'Available')
                                        ->selectRaw('MIN(price_per_night) as min_price, MAX(price_per_night) as max_price')->first();
                                @endphp
                                
                                <div class="pt-6 border-t border-[#4E3B46]/30 flex justify-between items-end">
                                    <div>
                                        <p class="text-[8px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] mb-1 opacity-60">Availability</p>
                                        <span class="inline-block px-3 py-1 rounded text-[9px] font-bold uppercase tracking-widest
                                            {{ $availableRooms > 0 ? 'bg-green-500/10 text-green-400 border border-green-500/30' : 'bg-red-500/10 text-red-400 border border-red-500/30' }}">
                                            {{ $availableRooms }} Suites
                                        </span>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-[8px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] mb-1 opacity-60">From</p>
                                        @if ($priceRange && $priceRange->min_price)
                                            <div class="text-xl font-bold font-serif text-[#EAD3CD]">
                                                ${{ number_format($priceRange->min_price, 0) }}<span class="text-[9px] font-sans font-normal text-[#CFCBCA] uppercase tracking-widest ml-1 opacity-50">/night</span>
                                            </div>
                                        @else
                                            <div class="text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] opacity-50 pt-2">
                                                N/A
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($hotels->hasPages())
                    <div class="mt-16 flex justify-center">
                        <style>
                            .pagination { display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; }
                            .pagination li { list-style: none; }
                            .pagination a, .pagination span {
                                display: flex; align-items: center; justify-content: center;
                                min-width: 2.5rem; height: 2.5rem; padding: 0 0.75rem;
                                font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em;
                                border: 1px solid #4E3B46; border-radius: 0.75rem;
                                transition: all 0.3s ease; color: #CFCBCA; background-color: #2A2729;
                            }
                            .pagination a:hover { background-color: #A0717F; color: #FFFFFF; border-color: #A0717F; transform: translateY(-2px); }
                            .pagination .active span { background-color: #A0717F; color: #FFFFFF; border-color: #A0717F; box-shadow: 0 4px 12px rgba(160, 113, 127, 0.3); }
                            .pagination .disabled span { opacity: 0.3; cursor: not-allowed; background-color: transparent; }
                        </style>
                        {{ $hotels->links() }}
                    </div>
                @endif
            @else
                <div class="rounded-3xl p-16 text-center border border-[rgba(234,211,205,0.1)] relative overflow-hidden mt-8" style="background-color: #2A2729;">
                    <div class="absolute inset-0 opacity-5 pointer-events-none">
                        <x-icon name="map" class="w-full h-full text-[#A0717F]" />
                    </div>
                    
                    <div class="relative z-10">
                        <div class="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                            <x-icon name="search" class="w-8 h-8 text-[#A0717F]" />
                        </div>
                        <h3 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">No Destinations Found</h3>
                        <p class="text-xs uppercase tracking-widest mb-10 leading-loose" style="color: rgba(207, 203, 202, 0.5); max-w-md mx-auto;">
                            Our current portfolio does not match your precise criteria. 
                            Consider refining your search parameters.
                        </p>
                        <a href="{{ route('guest.hotels.index') }}"
                            class="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1"
                            style="background-color: #4E3B46;"
                            onmouseover="this.style.backgroundColor='#68525F';"
                            onmouseout="this.style.backgroundColor='#4E3B46';">
                            Clear Criteria
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
@endsection
