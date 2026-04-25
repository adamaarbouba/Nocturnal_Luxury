@extends('layouts.app')


@section('content')

    <div class="fixed top-0 left-64 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.04); z-index: 0;"></div>
    <div class="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="relative z-10">

        <x-breadcrumbs :links="[
            ['label' => 'Receptionist Dashboard', 'url' => route('receptionist.dashboard')]
        ]" />


        <div class="mb-10">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                Overview
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Reception Atelier
            </h1>
        </div>

        <!-- Receptionist Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

            <div class="rounded-2xl overflow-hidden shadow-lg transition-all duration-500"
                style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);"
                onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 25px 50px rgba(160,113,127,0.15)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)';">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-medium uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Today's Check-ins</h3>
                        <div class="p-2 rounded-lg" style="background-color: #2A2729;">
                            <x-icon name="check" class="w-5 h-5" style="color: #A0717F;" />
                        </div>
                    </div>
                    <p class="text-4xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $todayCheckIns }}</p>
                </div>
            </div>


            <div class="rounded-2xl overflow-hidden shadow-lg transition-all duration-500"
                style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);"
                onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 25px 50px rgba(160,113,127,0.15)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)';">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-medium uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Today's Check-outs</h3>
                        <div class="p-2 rounded-lg" style="background-color: #2A2729;">
                            <x-icon name="arrow-right" class="w-5 h-5" style="color: #A0717F;" />
                        </div>
                    </div>
                    <p class="text-4xl font-bold" style="color: #A0717F; font-family: 'Georgia', serif;">{{ $todayCheckOuts }}</p>
                </div>
            </div>


            <div class="rounded-2xl overflow-hidden shadow-lg transition-all duration-500"
                style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);"
                onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 25px 50px rgba(160,113,127,0.15)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)';">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-medium uppercase" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Occupied Rooms</h3>
                        <div class="p-2 rounded-lg" style="background-color: #2A2729;">
                            <x-icon name="building" class="w-5 h-5" style="color: #A0717F;" />
                        </div>
                    </div>
                    <p class="text-4xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $occupiedRooms }}</p>
                </div>
            </div>
        </div>

        <!-- Receptionist Functions & Shift -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Functions Box -->
                <div class="rounded-2xl shadow-2xl p-8"
                    style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">

                    @if($pendingRefundRequests->count() > 0)
                    <div class="mb-10 rounded-2xl bg-[#2A2729] border border-[#A0717F] p-8 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <x-icon name="sparkles" size="2xl" />
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-2 h-2 rounded-full bg-[#A0717F] animate-pulse"></div>
                                <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-[#A0717F]">Action Required</h3>
                            </div>
                            <h4 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-6">Pending Refund Requests</h4>
                            
                            <div class="space-y-4">
                                @foreach($pendingRefundRequests as $request)
                                <div class="p-5 rounded-xl bg-[#383537] border border-[#4E3B46] hover:border-[#A0717F] transition-all group/item">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[#EAD3CD] font-bold">Booking #{{ $request->booking_id }}</span>
                                                <span class="text-[10px] text-[#4E3B46]">•</span>
                                                <span class="text-[#CFCBCA] text-sm">{{ $request->booking->user->name }}</span>
                                            </div>
                                            <p class="text-xs text-[#4E3B46] italic mb-3">"{{ Str::limit($request->reason, 60) }}"</p>
                                            <p class="text-lg font-bold text-[#A0717F]">${{ number_format($request->amount, 2) }}</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <form method="POST" action="{{ route('receptionist.refund-requests.approve', $request) }}">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-[#A0717F] hover:bg-[#8F6470] text-white text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('receptionist.refund-requests.deny', $request) }}">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-[#4E3B46] text-[#CFCBCA] hover:bg-[#4E3B46] text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                                                    Deny
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">Capabilities</p>
                    <h3 class="text-2xl font-bold mb-8" style="color: #EAD3CD; font-family: 'Georgia', serif;">Front Desk Operations</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <a href="{{ route('receptionist.check-in.index') }}" 
                           class="p-5 rounded-xl transition-all duration-300"
                           style="background-color: #4E3B46; border: 1px solid rgba(160, 113, 127, 0.15);"
                           onmouseover="this.style.backgroundColor='#2E2530'; this.style.borderColor='rgba(160, 113, 127, 0.4)'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.backgroundColor='#4E3B46'; this.style.borderColor='rgba(160, 113, 127, 0.15)'; this.style.transform='translateY(0)';">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 rounded-lg" style="background-color: #383537;">
                                    <x-icon name="check" size="md" style="color: #A0717F;" />
                                </div>
                                <h4 class="text-lg font-semibold" style="color: #EAD3CD; font-family: 'Georgia', serif;">Check-in Guest</h4>
                            </div>
                            <p class="text-sm" style="color: #CFCBCA;">Process incoming guest arrivals.</p>
                            @if ($todayCheckIns > 0)
                                <p class="text-xs font-semibold mt-3 uppercase" style="color: #A0717F; letter-spacing: 0.1em;">
                                    {{ $todayCheckIns }} Pending
                                </p>
                            @endif
                        </a>

                        <a href="{{ route('receptionist.check-out.index') }}" 
                           class="p-5 rounded-xl transition-all duration-300"
                           style="background-color: #4E3B46; border: 1px solid rgba(160, 113, 127, 0.15);"
                           onmouseover="this.style.backgroundColor='#2E2530'; this.style.borderColor='rgba(160, 113, 127, 0.4)'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.backgroundColor='#4E3B46'; this.style.borderColor='rgba(160, 113, 127, 0.15)'; this.style.transform='translateY(0)';">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 rounded-lg" style="background-color: #383537;">
                                    <x-icon name="arrow-right" size="md" style="color: #EAD3CD;" />
                                </div>
                                <h4 class="text-lg font-semibold" style="color: #A0717F; font-family: 'Georgia', serif;">Check-out Guest</h4>
                            </div>
                            <p class="text-sm" style="color: #CFCBCA;">Finalize guest billings and departures.</p>
                            @if ($todayCheckOuts > 0)
                                <p class="text-xs font-semibold mt-3 uppercase" style="color: #A0717F; letter-spacing: 0.1em;">
                                    {{ $todayCheckOuts }} Pending
                                </p>
                            @endif
                        </a>

                        <a href="{{ route('receptionist.bookings.index') }}" 
                           class="p-5 rounded-xl transition-all duration-300"
                           style="background-color: #4E3B46; border: 1px solid rgba(160, 113, 127, 0.15);"
                           onmouseover="this.style.backgroundColor='#2E2530'; this.style.borderColor='rgba(160, 113, 127, 0.4)'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.backgroundColor='#4E3B46'; this.style.borderColor='rgba(160, 113, 127, 0.15)'; this.style.transform='translateY(0)';">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 rounded-lg" style="background-color: #383537;">
                                    <x-icon name="calendar" size="md" style="color: #A0717F;" />
                                </div>
                                <h4 class="text-lg font-semibold" style="color: #EAD3CD; font-family: 'Georgia', serif;">View Bookings</h4>
                            </div>
                            <p class="text-sm" style="color: #CFCBCA;">See current reservations and history.</p>
                        </a>

                        <a href="{{ route('receptionist.bookings.create') }}" 
                           class="p-5 rounded-xl transition-all duration-300"
                           style="background-color: #4E3B46; border: 1px solid rgba(160, 113, 127, 0.15);"
                           onmouseover="this.style.backgroundColor='#2E2530'; this.style.borderColor='rgba(160, 113, 127, 0.4)'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.backgroundColor='#4E3B46'; this.style.borderColor='rgba(160, 113, 127, 0.15)'; this.style.transform='translateY(0)';">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 rounded-lg" style="background-color: #383537;">
                                    <x-icon name="plus" size="md" style="color: #A0717F;" />
                                </div>
                                <h4 class="text-lg font-semibold" style="color: #EAD3CD; font-family: 'Georgia', serif;">Create Booking</h4>
                            </div>
                            <p class="text-sm" style="color: #CFCBCA;">Book rooms for walk-ins or calls.</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="space-y-6">
                <div class="rounded-2xl shadow-2xl p-8"
                    style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                    <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">Schedule</p>
                    <h3 class="text-2xl font-bold mb-6" style="color: #EAD3CD; font-family: 'Georgia', serif;">Your Shift</h3>
                    
                    <div class="p-5 rounded-xl mb-6" style="background-color: #2E2530; border-left: 3px solid #A0717F;">
                        <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Current Status</p>
                        <div class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full" style="background-color: @if ($receptionistStatus === 'active') #A0717F @else #CFCBCA @endif;"></div>
                            <span class="text-lg font-semibold capitalize" style="color: #EAD3CD;">{{ $receptionistStatus }}</span>
                        </div>
                    </div>

                    @if ($shiftStart && $shiftEnd)
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Shift Time</p>
                                <p class="text-base" style="color: #CFCBCA;">{{ $shiftStart->format('h:i A') }} — {{ $shiftEnd->format('h:i A') }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm italic" style="color: rgba(207, 203, 202, 0.5);">Shift times are not configured for today.</p>
                    @endif
            </div>
        </div>
    </div>
@endsection
