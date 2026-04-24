@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-20 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.04); z-index: 0;"></div>

    <div class="max-w-4xl mx-auto px-4 py-8 relative z-10">
        <div class="flex items-center gap-3 mb-8 text-xs font-medium uppercase" style="letter-spacing: 0.1em;">
            <a href="{{ route('receptionist.dashboard') }}" class="transition-colors" style="color: rgba(207, 203, 202, 0.5);" onmouseover="this.style.color='#EAD3CD';" onmouseout="this.style.color='rgba(207, 203, 202, 0.5)';">
                Dashboard
            </a>
            <span style="color: #4E3B46;">/</span>
            <a href="{{ route('receptionist.bookings.index') }}" class="transition-colors" style="color: rgba(207, 203, 202, 0.5);" onmouseover="this.style.color='#EAD3CD';" onmouseout="this.style.color='rgba(207, 203, 202, 0.5)';">
                Bookings
            </a>
            <span style="color: #4E3B46;">/</span>
            <span style="color: #A0717F;">Create New</span>
        </div>

        <div class="mb-10">
            <h2 class="text-3xl lg:text-5xl font-bold mb-2" style="color: #EAD3CD; font-family: 'Georgia', serif;">Create Booking</h2>
            <p class="text-sm italic" style="color: #CFCBCA;">Secure accommodations for discerning guests.</p>
        </div>

        <!-- Form -->
        <div class="rounded-2xl shadow-2xl p-8 lg:p-12" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
            <form method="POST" action="{{ route('receptionist.bookings.store') }}">
                @csrf

                <!-- Guest Information Section -->
                <div class="mb-12">
                    <h3 class="text-2xl font-bold mb-8 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        <span class="p-2 rounded-lg" style="background-color: #4E3B46;">
                            <x-icon name="user" class="w-5 h-5" style="color: #A0717F;" />
                        </span>
                        Guest Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Guest Name -->
                        <div class="space-y-2">
                            <label for="guest_name" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Full Name
                            </label>
                            <input type="text" id="guest_name" name="guest_name" required value="{{ old('guest_name') }}"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('guest_name') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2);"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';"
                                placeholder="e.g. Julian Vanderbilt">
                            @error('guest_name')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Guest Email -->
                        <div class="space-y-2">
                            <label for="guest_email" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Email Address
                            </label>
                            <input type="email" id="guest_email" name="guest_email" required
                                value="{{ old('guest_email') }}"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('guest_email') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2);"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';"
                                placeholder="julian@example.com">
                            @error('guest_email')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Guest Phone -->
                        <div class="space-y-2">
                            <label for="guest_phone" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Private Line
                            </label>
                            <input type="text" id="guest_phone" name="guest_phone" required
                                value="{{ old('guest_phone') }}"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('guest_phone') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2);"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';"
                                placeholder="+1 555-0199">
                            @error('guest_phone')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Guest Address -->
                        <div class="space-y-2">
                            <label for="guest_address" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Origin Estate (Address)
                            </label>
                            <input type="text" id="guest_address" name="guest_address" required
                                value="{{ old('guest_address') }}"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('guest_address') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2);"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';"
                                placeholder="123 Serenity Blvd, NY">
                            @error('guest_address')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="mb-12 border-t" style="border-color: rgba(160, 113, 127, 0.1);" />

                <!-- Booking Dates Section -->
                <div class="mb-12">
                    <h3 class="text-2xl font-bold mb-8 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        <span class="p-2 rounded-lg" style="background-color: #4E3B46;">
                            <x-icon name="calendar" class="w-5 h-5" style="color: #A0717F;" />
                        </span>
                        Duration of Stay
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Check-in Date -->
                        <div class="space-y-2">
                            <label for="check_in_date" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Arrival
                            </label>
                            <input type="date" id="check_in_date" name="check_in_date" required
                                value="{{ old('check_in_date') }}" min="{{ now()->toDateString() }}"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('check_in_date') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2); color-scheme: dark;"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';">
                            @error('check_in_date')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Check-out Date -->
                        <div class="space-y-2">
                            <label for="check_out_date" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Departure
                            </label>
                            <input type="date" id="check_out_date" name="check_out_date" required
                                value="{{ old('check_out_date') }}" min="{{ now()->addDay()->toDateString() }}"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('check_out_date') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2); color-scheme: dark;"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';">
                            @error('check_out_date')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="mb-12 border-t" style="border-color: rgba(160, 113, 127, 0.1);" />

                <!-- Room Selection Section -->
                <div class="mb-12">
                    <h3 class="text-2xl font-bold mb-8 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        <span class="p-2 rounded-lg" style="background-color: #4E3B46;">
                            <x-icon name="building" class="w-5 h-5" style="color: #A0717F;" />
                        </span>
                        Suite Selection
                    </h3>

                    @if ($availableRooms->isEmpty())
                        <div class="rounded-xl p-6 text-center" style="background-color: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2);">
                            <p class="text-red-400 italic font-medium">No suites available for the selected parameters.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                            @foreach ($availableRooms as $room)
                                <label class="block relative rounded-xl p-5 cursor-pointer transition-all duration-300 group"
                                       style="background-color: #4E3B46; border: 1px solid rgba(160, 113, 127, 0.15);"
                                       onmouseover="this.style.borderColor='rgba(160, 113, 127, 0.5)'; this.style.backgroundColor='#2E2530';"
                                       onmouseout="if(!this.querySelector('input').checked) { this.style.borderColor='rgba(160, 113, 127, 0.15)'; this.style.backgroundColor='#4E3B46'; }">
                                    <div class="flex items-start gap-4">
                                        <div class="flex items-center h-6">
                                            <input type="checkbox" name="room_ids[]" value="{{ $room->id }}"
                                                {{ in_array($room->id, old('room_ids', [])) ? 'checked' : '' }}
                                                class="w-5 h-5 rounded"
                                                style="accent-color: #A0717F;"
                                                onchange="if(this.checked) { this.closest('label').style.borderColor='#A0717F'; this.closest('label').style.backgroundColor='#2E2530'; } else { this.closest('label').style.borderColor='rgba(160, 113, 127, 0.15)'; this.closest('label').style.backgroundColor='#4E3B46'; }">
                                        </div>
                                        <div>
                                            <p class="text-xl font-bold mb-1" style="color: #EAD3CD; font-family: 'Georgia', serif;">Suite {{ $room->room_number }}</p>
                                            <p class="text-xs font-medium uppercase mb-3" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.1em;">{{ $room->room_type }}</p>
                                            
                                            <div class="flex items-center gap-4 text-sm mt-3 pt-3" style="border-top: 1px solid rgba(234, 211, 205, 0.05);">
                                                <span style="color: #CFCBCA;">{{ $room->capacity }} Guests cap.</span>
                                                <span class="font-bold" style="color: #A0717F;">${{ number_format($room->price_per_night, 0) }} / night</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('room_ids')
                            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <hr class="mb-12 border-t" style="border-color: rgba(160, 113, 127, 0.1);" />

                <!-- Special Requests & Notes -->
                <div class="mb-12">
                    <h3 class="text-2xl font-bold mb-8" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        Final Preparations
                    </h3>

                    <div class="space-y-8">
                        <!-- Special Requests -->
                        <div class="space-y-2">
                            <label for="special_requests" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Special Requests <span style="font-style: italic; text-transform: none;">(Optional)</span>
                            </label>
                            <textarea id="special_requests" name="special_requests" rows="3"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('special_requests') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2); resize: none;"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';"
                                placeholder="Dietary restrictions, pillow preferences, specific views...">{{ old('special_requests') }}</textarea>
                            @error('special_requests')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2">
                            <label for="notes" class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                                Internal Notes <span style="font-style: italic; text-transform: none;">(Optional)</span>
                            </label>
                            <textarea id="notes" name="notes" rows="2"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300 @error('notes') border-red-500 @enderror"
                                style="background-color: #4E3B46; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2); resize: none;"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';"
                                placeholder="Administrative details...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-wrap items-center gap-4 mt-12">
                    <button type="submit"
                        class="px-10 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300"
                        style="background-color: #A0717F; color: #FFFFFF; letter-spacing: 0.12em; box-shadow: 0 4px 15px rgba(160, 113, 127, 0.3);"
                        onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                        Confirm Reservation
                    </button>
                    <a href="{{ route('receptionist.bookings.index') }}"
                        class="px-8 py-4 rounded-full text-sm font-semibold uppercase transition-all duration-300"
                        style="background-color: transparent; border: 1px solid rgba(207, 203, 202, 0.2); color: #CFCBCA; letter-spacing: 0.12em;"
                        onmouseover="this.style.backgroundColor='rgba(207, 203, 202, 0.05)';"
                        onmouseout="this.style.backgroundColor='transparent';">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
