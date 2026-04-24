@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>
    <div class="fixed bottom-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="container mx-auto px-4 py-8 max-w-4xl relative z-10">
        <x-breadcrumbs :links="[
            ['label' => 'Receptionist Dashboard', 'url' => route('receptionist.dashboard')],
            ['label' => 'Bookings', 'url' => route('receptionist.bookings.index')],
            ['label' => 'Folio #'.str_pad($booking->id, 5, '0', STR_PAD_LEFT), 'url' => '#']
        ]" />

        <div class="mb-10">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                {{ $hotel->name }}
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Folio #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
        </div>

        <div class="space-y-8">
            <!-- Booking Status Card -->
            <div class="rounded-2xl shadow-2xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                <div class="flex flex-col md:flex-row justify-between md:items-center mb-10 gap-6">
                    <div>
                        <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Reservation Status</p>
                        <p class="text-2xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</p>
                    </div>
                    <div>
                        <span class="px-5 py-2 rounded-full text-xs font-semibold uppercase tracking-wider border"
                            @if ($booking->status === 'pending') style="background: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.3); color: #FACC15;"
                            @elseif($booking->status === 'confirmed') style="background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); color: #60A5FA;"
                            @elseif($booking->status === 'checked_in') style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #4ADE80;"
                            @elseif($booking->status === 'checked_out') style="background: rgba(168, 85, 247, 0.1); border-color: rgba(168, 85, 247, 0.3); color: #C084FC;"
                            @else style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #F87171;" @endif>
                            {{ str_replace('_', ' ', $booking->status) }}
                        </span>
                    </div>
                </div>

                <!-- Booking Dates -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 py-8" style="border-top: 1px solid rgba(234, 211, 205, 0.1); border-bottom: 1px solid rgba(234, 211, 205, 0.1);">
                    <div>
                        <p class="text-xs font-medium uppercase mb-2" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Arrival</p>
                        <p class="text-xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $booking->check_in_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase mb-2" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Departure</p>
                        <p class="text-xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $booking->check_out_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase mb-2" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Total Balance</p>
                        <p class="text-2xl font-bold" style="color: #A0717F;">${{ number_format($booking->total_amount, 2) }}</p>
                        <p class="text-xs mt-1" style="color: #CFCBCA; font-style: italic;">{{ ucfirst($booking->payment_status) }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if ($booking->status === 'pending')
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('receptionist.check-in.show', $booking) }}"
                            class="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                            style="background-color: #A0717F; color: #FFFFFF; letter-spacing: 0.12em; box-shadow: 0 4px 15px rgba(160, 113, 127, 0.3);"
                            onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                            onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                            Check-In Guest
                        </a>
                        <form action="{{ route('receptionist.bookings.cancel', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('POST')
                            <button type="button"
                                class="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                                style="background-color: transparent; border: 1px solid rgba(239, 68, 68, 0.5); color: #F87171; letter-spacing: 0.12em;"
                                onmouseover="this.style.backgroundColor='rgba(239, 68, 68, 0.1)';"
                                onmouseout="this.style.backgroundColor='transparent';"
                                onclick="NocturnalUI.confirm('Are you sure you want to cancel this booking?', 'Cancel Booking').then(c => { if(c) this.closest('form').submit(); })">
                                Cancel Booking
                            </button>
                        </form>
                    </div>
                @elseif ($booking->status === 'checked_in')
                    <div class="mt-8">
                        <a href="{{ route('receptionist.check-out.show', $booking) }}"
                            class="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                            style="background-color: #A0717F; color: #FFFFFF; letter-spacing: 0.12em; box-shadow: 0 4px 15px rgba(160, 113, 127, 0.3);"
                            onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                            onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                            Process Check-Out
                        </a>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Guest Information -->
                <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                    <h3 class="text-xl font-bold mb-6" style="color: #EAD3CD; font-family: 'Georgia', serif;">Guest Profile</h3>
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Name</p>
                            <p class="text-lg" style="color: #EAD3CD;">{{ $booking->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Email</p>
                            <p class="text-base" style="color: #CFCBCA;">{{ $booking->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Phone</p>
                            <p class="text-base" style="color: #CFCBCA;">{{ $booking->user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Address</p>
                            <p class="text-base" style="color: #CFCBCA;">{{ $booking->user->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Special Requests & Notes -->
                <div class="space-y-8">
                    @if ($booking->special_requests)
                        <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                            <h3 class="text-xl font-bold mb-4" style="color: #EAD3CD; font-family: 'Georgia', serif;">Special Requests</h3>
                            <p class="text-sm leading-relaxed" style="color: #CFCBCA; font-style: italic;">"{{ $booking->special_requests }}"</p>
                        </div>
                    @endif

                    @if ($booking->notes)
                        <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                            <h3 class="text-xl font-bold mb-4" style="color: #EAD3CD; font-family: 'Georgia', serif;">Internal Notes</h3>
                            <p class="text-sm leading-relaxed" style="color: #CFCBCA;">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Booked Rooms -->
            <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                <h3 class="text-xl font-bold mb-6" style="color: #EAD3CD; font-family: 'Georgia', serif;">Suite Accommodations</h3>
                <div class="space-y-4">
                    @foreach ($booking->bookingItems as $item)
                        <div class="rounded-xl p-6" style="background-color: #4E3B46; border: 1px solid rgba(160, 113, 127, 0.1);">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div>
                                    <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Suite No.</p>
                                    <p class="text-lg font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $item->room->room_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Category</p>
                                    <p class="text-base" style="color: #CFCBCA;">{{ $item->room->room_type }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Capacity</p>
                                    <p class="text-base" style="color: #CFCBCA;">{{ $item->room->capacity }} {{ $item->room->capacity === 1 ? 'Guest' : 'Guests' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Rate</p>
                                    <p class="text-lg font-bold" style="color: #A0717F;">${{ number_format($item->price_per_night, 2) }} <span class="text-xs font-normal" style="color: rgba(207, 203, 202, 0.5);">/ night</span></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-10">
            <a href="{{ route('receptionist.bookings.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300"
                style="color: #CFCBCA; border: 1px solid rgba(207, 203, 202, 0.2); letter-spacing: 0.1em;"
                onmouseover="this.style.backgroundColor='rgba(207, 203, 202, 0.05)';"
                onmouseout="this.style.backgroundColor='transparent';">
                <x-icon name="arrow-left" class="w-4 h-4" /> Back to Directory
            </a>
        </div>
    </div>
@endsection
