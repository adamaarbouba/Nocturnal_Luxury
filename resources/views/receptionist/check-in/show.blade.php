@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-3xl relative z-10">
        <div class="mb-10 text-center">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                {{ $hotel->name }}
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Arrival Procedure
            </h1>
        </div>

        <div class="rounded-2xl shadow-2xl p-8 lg:p-12" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
            
            <!-- Guest Profile -->
            <div class="mb-10">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                    <x-icon name="user" class="w-6 h-6" style="color: #A0717F;" /> Guest Profile
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-xl" style="background-color: #2E2530; border: 1px solid rgba(160, 113, 127, 0.1);">
                    <div>
                        <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Name</p>
                        <p class="text-lg font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $booking->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Email</p>
                        <p class="text-base" style="color: #CFCBCA;">{{ $booking->user->email }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Phone</p>
                        <p class="text-base" style="color: #CFCBCA;">{{ $booking->user->phone ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Folio Details -->
            <div class="mb-10">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                    <x-icon name="calendar" class="w-6 h-6" style="color: #A0717F;" /> Folio Details
                </h3>
                <div class="grid grid-cols-2 gap-6 p-6 rounded-xl" style="background-color: #2E2530; border: 1px solid rgba(160, 113, 127, 0.1);">
                    <div>
                        <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Arrival</p>
                        <p class="text-lg font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $booking->check_in_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Departure</p>
                        <p class="text-lg font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $booking->check_out_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-span-2 pt-4 border-t" style="border-color: rgba(160, 113, 127, 0.1);">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Outstanding Balance</p>
                                <p class="text-2xl font-bold" style="color: #A0717F;">${{ number_format($booking->total_amount, 2) }}</p>
                            </div>
                            <span class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border"
                                @if ($booking->payment_status === 'paid') style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #4ADE80;"
                                @elseif($booking->payment_status === 'pending') style="background: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.3); color: #FACC15;"
                                @else style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #F87171;" @endif>
                                {{ $booking->payment_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pre-Cleared Suites -->
            <div class="mb-10">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                    <x-icon name="building" class="w-6 h-6" style="color: #A0717F;" /> Pre-Cleared Suites
                </h3>
                <div class="space-y-4">
                    @foreach ($booking->bookingItems as $item)
                        <div class="flex items-center justify-between p-5 rounded-xl border transition-colors duration-300"
                             style="background-color: #2E2530; border-color: rgba(160, 113, 127, 0.1);"
                             onmouseover="this.style.borderColor='rgba(160, 113, 127, 0.4)';">
                            <div>
                                <p class="text-xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">Suite {{ $item->room->room_number }}</p>
                                <p class="text-sm mt-1" style="color: #CFCBCA;">{{ $item->room->room_type }} &middot; Up to {{ $item->room->capacity }} Guests</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold" style="color: #A0717F;">${{ number_format($item->price_per_night, 0) }}<span class="text-xs font-normal" style="color: rgba(207, 203, 202, 0.5);">/nt</span></p>
                                <span class="inline-block mt-2 px-3 py-1 text-xs font-medium uppercase tracking-wider rounded border"
                                      @if ($item->room->status === 'Reserved') style="background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); color: #60A5FA;"
                                      @else style="background: rgba(207, 203, 202, 0.1); border-color: rgba(207, 203, 202, 0.2); color: #CFCBCA;" @endif>
                                    @if ($item->room->status === 'Reserved') Status: Set to Occupied @else Status: {{ $item->room->status }} @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes Processing -->
            <form method="POST" action="{{ route('receptionist.check-in.process', $booking) }}">
                @csrf
                <div class="mb-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        <x-icon name="check" class="w-6 h-6" style="color: #A0717F;" /> Arrival Formalities
                    </h3>
                    <div class="space-y-4">
                        <label class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                            Administrative Notes (Optional)
                        </label>
                        <textarea name="notes" maxlength="500" rows="3"
                            placeholder="Luggage assistance required, late arrival verified, etc."
                            class="w-full px-5 py-4 rounded-xl focus:outline-none transition-all duration-300"
                            style="background-color: #2E2530; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2); resize: none;"
                            onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';">{{ old('notes', $booking->notes) }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6" style="border-top: 1px solid rgba(160, 113, 127, 0.1);">
                    <button type="submit"
                        class="flex-1 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 text-center"
                        style="background-color: #A0717F; color: #FFFFFF; letter-spacing: 0.12em; box-shadow: 0 4px 15px rgba(160, 113, 127, 0.3);"
                        onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                        Confirm Check-In
                    </button>
                    <a href="{{ route('receptionist.check-in.index') }}"
                        class="flex-1 py-4 rounded-full text-sm font-semibold uppercase transition-all duration-300 text-center"
                        style="background-color: transparent; border: 1px solid rgba(207, 203, 202, 0.2); color: #CFCBCA; letter-spacing: 0.12em;"
                        onmouseover="this.style.backgroundColor='rgba(207, 203, 202, 0.05)';"
                        onmouseout="this.style.backgroundColor='transparent';">
                        Dismiss
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
