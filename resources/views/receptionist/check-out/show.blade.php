@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-3xl relative z-10">
        <div class="mb-10 text-center">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                {{ $hotel->name }}
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Departure Procedure
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
                                <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Total Billing</p>
                                <p class="text-2xl font-bold" style="color: #EAD3CD;">${{ number_format($booking->total_amount, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Outstanding Balance</p>
                                <p class="text-2xl font-bold {{ $remainingBalance <= 0 ? '' : 'text-red-400' }}" style="{{ $remainingBalance <= 0 ? 'color: #A0717F;' : '' }}">
                                    ${{ number_format($remainingBalance, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($remainingBalance > 0)
                    <div class="mt-4 rounded-xl p-5" style="background-color: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2);">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <p class="text-red-400 font-bold mb-1" style="font-family: 'Georgia', serif;">Payment Required</p>
                                <p class="text-red-300 text-sm">Clear the outstanding balance of ${{ number_format($remainingBalance, 2) }} prior to departure.</p>
                            </div>
                            <a href="{{ route('receptionist.payments.form', $booking) }}"
                                class="inline-block px-5 py-2.5 rounded-full text-xs font-bold uppercase transition-all duration-300 shrink-0"
                                style="background-color: transparent; border: 1px solid #F87171; color: #F87171; letter-spacing: 0.1em;"
                                onmouseover="this.style.backgroundColor='#F87171'; this.style.color='#FFFFFF';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#F87171';">
                                Record Payment
                            </a>
                        </div>
                    </div>
                @else
                    <div class="mt-4 rounded-xl p-5" style="background-color: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2);">
                        <p class="text-green-400 font-bold mb-1" style="font-family: 'Georgia', serif;">Payment Settled</p>
                        <p class="text-green-300 text-sm">Guest account is cleared. Safe to finalize checkout.</p>
                    </div>
                @endif
            </div>

            <!-- Suites To Release -->
            <div class="mb-10">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                    <x-icon name="building" class="w-6 h-6" style="color: #A0717F;" /> Suites to Release
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
                                      @if ($item->room->status === 'Occupied') style="background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2); color: #F87171;"
                                      @else style="background: rgba(207, 203, 202, 0.1); border-color: rgba(207, 203, 202, 0.2); color: #CFCBCA;" @endif>
                                    @if ($item->room->status === 'Occupied') State: To Cleaning @else State: {{ $item->room->status }} @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes Processing -->
            <form method="POST" action="{{ route('receptionist.check-out.process', $booking) }}">
                @csrf
                <div class="mb-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        <x-icon name="user" class="w-6 h-6" style="color: #A0717F;" /> Departure Formalities
                    </h3>
                    
                    @if($booking->notes)
                        <div class="mb-4 rounded-xl p-5" style="background-color: rgba(160, 113, 127, 0.05); border: 1px solid rgba(160, 113, 127, 0.2);">
                            <p class="text-xs font-medium uppercase mb-2" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Existing Notes</p>
                            <p class="text-sm leading-relaxed" style="color: #EAD3CD;">{{ $booking->notes }}</p>
                        </div>
                    @endif

                    <div class="space-y-4 mt-6">
                        <label class="block text-xs font-semibold uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">
                            Add Checkout Notes (Optional)
                        </label>
                        <textarea name="notes" maxlength="500" rows="3"
                            placeholder="Room condition, items to retrieve, feedback gathered..."
                            class="w-full px-5 py-4 rounded-xl focus:outline-none transition-all duration-300"
                            style="background-color: #2E2530; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2); resize: none;"
                            onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';">{{ old('notes', $booking->notes) }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-8" style="border-top: 1px solid rgba(160, 113, 127, 0.1);">
                    @if ($remainingBalance > 0)
                        <button type="submit" disabled
                            class="flex-1 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 text-center cursor-not-allowed opacity-50"
                            style="background-color: #4E3B46; color: #CFCBCA; letter-spacing: 0.12em;">
                            Confirm Departure (Payment Required)
                        </button>
                    @else
                        <button type="submit"
                            class="flex-1 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 text-center"
                            style="background-color: transparent; border: 1px solid #A0717F; color: #EAD3CD; letter-spacing: 0.12em; box-shadow: 0 4px 15px rgba(160, 113, 127, 0.3);"
                            onmouseover="this.style.backgroundColor='#A0717F'; this.style.color='#FFFFFF'; this.style.transform='translateY(-2px)';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#EAD3CD'; this.style.transform='translateY(0)';">
                            Confirm Departure
                        </button>
                    @endif
                    <a href="{{ route('receptionist.check-out.index') }}"
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
