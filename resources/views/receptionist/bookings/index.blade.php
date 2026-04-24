@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 right-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.04); z-index: 0;"></div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        <x-breadcrumbs :links="[
            ['label' => 'Receptionist Dashboard', 'url' => route('receptionist.dashboard')],
            ['label' => 'Bookings', 'url' => '#']
        ]" />

        {{-- Page Header --}}
        <div class="mb-10">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                {{ $hotel->name }}
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Booking Management
            </h1>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl" style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3);">
                <p class="text-green-400 font-medium">✓ {{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);">
                <p class="text-red-400 font-medium">✗ {{ session('error') }}</p>
            </div>
        @endif

        <!-- Booking Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <a href="{{ route('receptionist.bookings.index') }}"
                class="rounded-2xl overflow-hidden shadow-lg transition-all duration-500 cursor-pointer"
                style="background-color: #383537; border-top: {{ !isset($currentStatus) ? '2px solid #A0717F' : '1px solid rgba(234, 211, 205, 0.1)' }};"
                onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 25px 50px rgba(160,113,127,0.15)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)';">
                <div class="p-6">
                    <h3 class="text-xs font-medium uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Pending Bookings</h3>
                    <p class="text-4xl font-bold mt-2" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $pendingBookings }}</p>
                </div>
            </a>
            <a href="{{ route('receptionist.bookings.filter', 'checked_in') }}"
                class="rounded-2xl overflow-hidden shadow-lg transition-all duration-500 cursor-pointer"
                style="background-color: #383537; border-top: {{ isset($currentStatus) && $currentStatus === 'checked_in' ? '2px solid #A0717F' : '1px solid rgba(234, 211, 205, 0.1)' }};"
                onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 25px 50px rgba(160,113,127,0.15)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)';">
                <div class="p-6">
                    <h3 class="text-xs font-medium uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Checked In</h3>
                    <p class="text-4xl font-bold mt-2" style="color: rgba(34, 197, 94, 0.8); font-family: 'Georgia', serif;">{{ $checkedInBookings }}</p>
                </div>
            </a>
            <a href="{{ route('receptionist.bookings.filter', 'checked_out') }}"
                class="rounded-2xl overflow-hidden shadow-lg transition-all duration-500 cursor-pointer"
                style="background-color: #383537; border-top: {{ isset($currentStatus) && $currentStatus === 'checked_out' ? '2px solid #A0717F' : '1px solid rgba(234, 211, 205, 0.1)' }};"
                onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 25px 50px rgba(160,113,127,0.15)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)';">
                <div class="p-6">
                    <h3 class="text-xs font-medium uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Checked Out</h3>
                    <p class="text-4xl font-bold mt-2" style="color: rgba(168, 85, 247, 0.8); font-family: 'Georgia', serif;">{{ $checkedOutBookings ?? 0 }}</p>
                </div>
            </a>
        </div>

        <!-- Filter Buttons -->
        <div class="mb-8 flex flex-wrap gap-3">
            <a href="{{ route('receptionist.bookings.index') }}"
                class="px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300"
                style="letter-spacing: 0.1em; {{ !isset($currentStatus) ? 'background-color: #A0717F; color: #FFFFFF;' : 'background-color: #383537; color: #CFCBCA; border: 1px solid rgba(234, 211, 205, 0.1);' }}"
                onmouseover="if('{{ !isset($currentStatus) }}' != '1') { this.style.backgroundColor='#4E3B46'; } else { this.style.backgroundColor='#b58290'; }"
                onmouseout="if('{{ !isset($currentStatus) }}' != '1') { this.style.backgroundColor='#383537'; } else { this.style.backgroundColor='#A0717F'; }">
                All Bookings
            </a>
            @php
                $filters = ['pending' => 'Pending', 'checked_in' => 'Checked In', 'checked_out' => 'Checked Out', 'cancelled' => 'Cancelled'];
            @endphp
            @foreach($filters as $val => $label)
                <a href="{{ route('receptionist.bookings.filter', $val) }}"
                    class="px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300"
                    style="letter-spacing: 0.1em; {{ isset($currentStatus) && $currentStatus === $val ? 'background-color: #A0717F; color: #FFFFFF;' : 'background-color: #383537; color: #CFCBCA; border: 1px solid rgba(234, 211, 205, 0.1);' }}"
                    onmouseover="if('{{ isset($currentStatus) && $currentStatus === $val }}' != '1') { this.style.backgroundColor='#4E3B46'; } else { this.style.backgroundColor='#b58290'; }"
                    onmouseout="if('{{ isset($currentStatus) && $currentStatus === $val }}' != '1') { this.style.backgroundColor='#383537'; } else { this.style.backgroundColor='#A0717F'; }">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($bookings->count() > 0)
            
            <!-- Mobile/Tablet Card Layout (Visible up to ~1280px) -->
            <div class="block xl:hidden space-y-5">
                @foreach ($bookings as $booking)
                    <div class="bg-[#383537] rounded-2xl shadow-xl p-6 border-t-[3px]"
                        style="border-color: @if($booking->status === 'pending') rgba(234, 179, 8, 0.6)
                                              @elseif($booking->status === 'confirmed') rgba(59, 130, 246, 0.6)
                                              @elseif($booking->status === 'checked_in') rgba(34, 197, 94, 0.6)
                                              @elseif($booking->status === 'checked_out') rgba(168, 85, 247, 0.6)
                                              @else rgba(239, 68, 68, 0.6) @endif;">
                        
                        <div class="flex justify-between items-start mb-5 border-b border-[rgba(234,211,205,0.05)] pb-5">
                            <div>
                                <h3 class="text-xl font-bold font-serif text-[#EAD3CD] mb-1">{{ $booking->user->name }}</h3>
                                <p class="text-sm text-[#CFCBCA]">{{ $booking->user->email }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2 shrink-0 ml-4">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border"
                                    @if ($booking->status === 'pending') style="background: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.3); color: #FACC15;"
                                    @elseif($booking->status === 'confirmed') style="background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); color: #60A5FA;"
                                    @elseif($booking->status === 'checked_in') style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #4ADE80;"
                                    @elseif($booking->status === 'checked_out') style="background: rgba(168, 85, 247, 0.1); border-color: rgba(168, 85, 247, 0.3); color: #C084FC;"
                                    @else style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #F87171;" @endif>
                                    {{ str_replace('_', ' ', $booking->status) }}
                                </span>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border"
                                    @if ($booking->payment_status === 'paid') style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #4ADE80;"
                                    @elseif($booking->payment_status === 'pending') style="background: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.3); color: #FACC15;"
                                    @else style="background: rgba(207, 203, 202, 0.05); border-color: rgba(207, 203, 202, 0.2); color: #CFCBCA;" @endif>
                                    {{ $booking->payment_status }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Stay Duration</p>
                                <p class="text-sm font-semibold text-[#EAD3CD] flex items-center gap-2">
                                    {{ $booking->check_in_date->format('M d') }}
                                    <span class="text-[#A0717F]">→</span>
                                    {{ $booking->check_out_date->format('M d') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Units Reserved</p>
                                <p class="text-sm font-semibold text-[#EAD3CD]">
                                    {{ $booking->bookingItems->count() }} {{ $booking->bookingItems->count() === 1 ? 'room' : 'rooms' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-5 border-t border-[rgba(234,211,205,0.05)]">
                            <div>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Total Due</p>
                                <p class="text-xl font-bold text-[#A0717F]" style="font-family: 'Georgia', serif;">${{ number_format($booking->total_amount, 2) }}</p>
                            </div>
                            <a href="{{ route('receptionist.bookings.show', $booking) }}"
                                class="px-6 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300"
                                style="background-color: transparent; border: 1px solid #A0717F; color: #A0717F;"
                                onmouseover="this.style.backgroundColor='#A0717F'; this.style.color='#FFFFFF';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#A0717F';">
                                View Folio
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View Uncompressed Table (Visible 1280px and above) -->
            <div class="hidden xl:block rounded-2xl shadow-2xl overflow-hidden bg-[#383537] border-t border-[rgba(234,211,205,0.1)]">
                <div class="w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead style="background-color: #2E2530; border-bottom: 1px solid rgba(160, 113, 127, 0.2);">
                            <tr>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Guest Profile</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Check-In</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Check-Out</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Rooms</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Amount Due</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Status</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Ledger</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[rgba(234,211,205,0.05)]">
                            @foreach ($bookings as $booking)
                                <tr class="transition-colors duration-300" 
                                    onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.05)';"
                                    onmouseout="this.style.backgroundColor='transparent';">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-[#EAD3CD] font-serif tracking-wide">{{ $booking->user->name }}</p>
                                        <p class="text-sm text-[#CFCBCA]">{{ $booking->user->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-[#CFCBCA]">{{ $booking->check_in_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-[#CFCBCA]">{{ $booking->check_out_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-[#CFCBCA]">
                                        {{ $booking->bookingItems->count() }} {{ $booking->bookingItems->count() === 1 ? 'room' : 'rooms' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-lg font-bold" style="color: #A0717F; font-family: 'Georgia', serif;">${{ number_format($booking->total_amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border"
                                        @if ($booking->status === 'pending') style="background: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.3); color: #FACC15;"
                                        @elseif($booking->status === 'confirmed') style="background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); color: #60A5FA;"
                                        @elseif($booking->status === 'checked_in') style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #4ADE80;"
                                        @elseif($booking->status === 'checked_out') style="background: rgba(168, 85, 247, 0.1); border-color: rgba(168, 85, 247, 0.3); color: #C084FC;"
                                        @else style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #F87171;" @endif>
                                            {{ str_replace('_', ' ', $booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border"
                                        @if ($booking->payment_status === 'paid') style="background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #4ADE80;"
                                        @elseif($booking->payment_status === 'pending') style="background: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.3); color: #FACC15;"
                                        @else style="background: rgba(207, 203, 202, 0.05); border-color: rgba(207, 203, 202, 0.2); color: #CFCBCA;" @endif>
                                            {{ $booking->payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('receptionist.bookings.show', $booking) }}"
                                            class="inline-block px-5 py-2 rounded-md text-[10px] font-bold uppercase transition-all duration-300 tracking-widest"
                                            style="background-color: transparent; border: 1px solid #A0717F; color: #A0717F;"
                                            onmouseover="this.style.backgroundColor='#A0717F'; this.style.color='#FFFFFF';"
                                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#A0717F';">
                                            Open
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Global Pagination -->
            <div class="mt-8 rounded-2xl p-5 bg-[#383537] shadow-xl border border-[rgba(234,211,205,0.05)]">
                {{ $bookings->links() }}
            </div>

        @else
            <div class="rounded-2xl shadow-xl p-12 text-center" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                <x-icon name="calendar" class="w-12 h-12 mx-auto mb-4" style="color: rgba(160, 113, 127, 0.4);" />
                <p class="text-sm font-medium uppercase tracking-widest" style="color: rgba(207, 203, 202, 0.5);">
                    No bookings found{{ isset($currentStatus) ? ' with status: ' . str_replace('_', ' ', $currentStatus) : '' }}.
                </p>
            </div>
        @endif

        <div class="mt-10">
            <a href="{{ route('receptionist.dashboard') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-widest transition-all duration-300"
                style="color: #CFCBCA; border: 1px solid rgba(207, 203, 202, 0.2);"
                onmouseover="this.style.backgroundColor='rgba(207, 203, 202, 0.05)';"
                onmouseout="this.style.backgroundColor='transparent';">
                <x-icon name="arrow-left" class="w-4 h-4" /> Back to Dashboard
            </a>
        </div>
    </div>
@endsection
