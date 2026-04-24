@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 left-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        <x-breadcrumbs :links="[
            ['label' => 'Receptionist Dashboard', 'url' => route('receptionist.dashboard')],
            ['label' => 'Check-ins', 'url' => '#']
        ]" />

        {{-- Page Header --}}
        <div class="mb-10">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                {{ $hotel->name }}
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Pending Arrivals
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

        @if ($pendingCheckIns->count() > 0)
            
            <!-- Mobile/Tablet Card Layout (Visible up to ~1280px) -->
            <div class="block xl:hidden space-y-5">
                @foreach ($pendingCheckIns as $booking)
                    <div class="bg-[#383537] rounded-2xl shadow-xl p-6 border-t-[3px]"
                        style="border-color: rgba(160, 113, 127, 0.6);">
                        
                        <div class="flex justify-between items-start mb-5 border-b border-[rgba(234,211,205,0.05)] pb-5">
                            <div>
                                <h3 class="text-xl font-bold font-serif text-[#EAD3CD] mb-1">{{ $booking->user->name }}</h3>
                                <p class="text-sm text-[#CFCBCA]">{{ $booking->user->email }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Arrival Date</p>
                                <p class="text-sm font-semibold text-[#EAD3CD]">
                                    {{ $booking->check_in_date->format('M d, Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Assigned Suites</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($booking->bookingItems as $item)
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] bg-[#2A2729] border border-[rgba(160,113,127,0.3)] text-[#EAD3CD]">
                                            {{ $item->room->room_number }} <span style="color: rgba(207, 203, 202, 0.5);">({{ $item->room->room_type }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-5 border-t border-[rgba(234,211,205,0.05)]">
                            <div>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Ledger Balance</p>
                                <p class="text-xl font-bold text-[#A0717F]" style="font-family: 'Georgia', serif;">${{ number_format($booking->total_amount, 2) }}</p>
                            </div>
                            <a href="{{ route('receptionist.check-in.show', $booking) }}"
                                class="px-6 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-md"
                                style="background-color: #A0717F; color: #FFFFFF;"
                                onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                                Process Arrival
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
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Arrival Date</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Allocated Suites</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style="color: #A0717F;">Folio Balance</th>
                                <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-right" style="color: #A0717F;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[rgba(234,211,205,0.05)]">
                            @foreach ($pendingCheckIns as $booking)
                                <tr class="transition-colors duration-300"
                                    onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.05)';"
                                    onmouseout="this.style.backgroundColor='transparent';">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-[#EAD3CD] font-serif tracking-wide">{{ $booking->user->name }}</p>
                                        <p class="text-sm text-[#CFCBCA]">{{ $booking->user->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-[#CFCBCA]">{{ $booking->check_in_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($booking->bookingItems as $item)
                                                <span class="inline-block px-3 py-1 rounded text-xs bg-[#2A2729] border border-[rgba(160,113,127,0.3)] text-[#EAD3CD]">
                                                    {{ $item->room->room_number }} <span style="color: rgba(207, 203, 202, 0.5);">({{ $item->room->room_type }})</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-lg font-bold" style="color: #A0717F; font-family: 'Georgia', serif;">${{ number_format($booking->total_amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('receptionist.check-in.show', $booking) }}"
                                            class="inline-block px-6 py-2.5 rounded-md text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-md"
                                            style="background-color: #A0717F; color: #FFFFFF;"
                                            onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                                            onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                                            Commence
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
                {{ $pendingCheckIns->links() }}
            </div>

        @else
            <div class="rounded-2xl shadow-xl p-12 text-center" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                <x-icon name="check" class="w-12 h-12 mx-auto mb-4" style="color: rgba(160, 113, 127, 0.4);" />
                <p class="text-sm font-medium uppercase tracking-widest" style="color: rgba(207, 203, 202, 0.5);">
                    No pending arrivals at this time.
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
