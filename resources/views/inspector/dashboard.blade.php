@extends('layouts.app')

@php
    $pageTitle = 'Inspection Dashboard';
@endphp

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>
    <div class="fixed bottom-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="relative z-10">
        {{-- Breadcrumbs --}}
        <x-breadcrumbs :links="[
            ['label' => 'Inspector Dashboard', 'url' => route('inspector.dashboard')]
        ]" />

        {{-- Page Header --}}
        <div class="mb-10">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                Quality Control
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Inspection Hub
            </h1>
            <p class="mt-4 text-[#CFCBCA] opacity-70">Review and adjudicate rooms processed by the cleaning staff</p>
        </div>

    <!-- Success/Error Messages -->
    @if ($message = Session::get('success'))
        <div class="mb-6 p-4 rounded-lg border border-green-500 bg-green-500/20 text-green-400">
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="mb-6 p-4 rounded-lg border border-red-500 bg-red-500/20 text-red-400">
            {{ $message }}
        </div>
    @endif

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        {{-- Pending Rooms Card --}}
        <div class="group bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl p-8 relative overflow-hidden transition-all hover:border-[#A0717F]/50">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <x-icon name="clipboard" class="w-16 h-16 text-[#A0717F]" />
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-bold font-serif text-[#A0717F] mb-1 leading-none">{{ $roomsNeedingInspection->count() }}</div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] opacity-60">Pending Inspections</p>
            </div>
        </div>

        {{-- Processed Efficiency --}}
        <div class="group bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl p-8 relative overflow-hidden transition-all hover:border-[#EAD3CD]/30">
             <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <x-icon name="check-circle" class="w-16 h-16 text-[#EAD3CD]" />
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-bold font-serif text-[#EAD3CD] mb-1 leading-none">
                    {{ $roomsNeedingInspection->count() > 0 ? 'Action Required' : 'Cleared' }}
                </div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] opacity-60">Current Priority</p>
            </div>
        </div>

        {{-- User Identity Card --}}
        <div class="group bg-[#2A2729] border border-[#4E3B46] rounded-2xl shadow-xl p-8 relative overflow-hidden transition-all">
             <div class="absolute -bottom-2 -right-2 p-4 opacity-5">
                <x-icon name="user" class="w-20 h-20 text-[#EAD3CD]" />
            </div>
            <div class="relative z-10">
                <div class="text-xl font-bold font-serif text-[#EAD3CD] mb-1 truncate">{{ Auth::user()->name }}</div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F]">Active Inspector</p>
            </div>
        </div>
    </div>

    <!-- Rooms Table -->
    <div class="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-2xl overflow-hidden mb-12">
        <div class="bg-[#2A2729] border-b border-[#4E3B46] px-8 py-5 flex justify-between items-center">
            <h3 class="text-sm font-bold text-[#EAD3CD] uppercase tracking-widest">Inspection Queue</h3>
            @if($roomsNeedingInspection->count() > 0)
                <span class="bg-[#A0717F]/20 text-[#A0717F] text-[10px] font-bold px-3 py-1 rounded-full border border-[#A0717F]/30 uppercase tracking-tighter">
                    {{ $roomsNeedingInspection->count() }} Pending
                </span>
            @endif
        </div>

        @if ($roomsNeedingInspection->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-[#2A2729]/50 border-b border-[#4E3B46]">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Property</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Dossier</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Classification</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Artisan</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Status</th>
                            <th class="px-8 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#4E3B46]">
                        @foreach ($roomsNeedingInspection as $room)
                            <tr class="hover:bg-[#2A2729]/40 transition-colors group">
                                <td class="px-8 py-5">
                                    <p class="text-sm font-semibold text-[#EAD3CD]">{{ $room->hotel->name }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-bold text-[#EAD3CD]">Room #{{ $room->room_number }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-xs text-[#CFCBCA] opacity-70">{{ $room->room_type }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    @php
                                        $lastCleaning = $room->cleaningLogs()->where('type', 'cleaning')->latest()->with('user')->first();
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-[#4E3B46] flex items-center justify-center text-[10px] text-[#A0717F]">
                                            {{ substr($lastCleaning?->user?->name ?: 'U', 0, 1) }}
                                        </div>
                                        <p class="text-xs text-[#EAD3CD]">
                                            {{ $lastCleaning?->user?->name ?: 'Unassigned' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase tracking-widest">
                                        Pending Review
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <a href="{{ route('inspector.rooms.inspect-form', $room) }}"
                                        class="inline-block bg-[#4E3B46]/50 hover:bg-[#A0717F] text-[#EAD3CD] text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all border border-[#4E3B46] hover:border-[#A0717F]">
                                        Begin Audit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-8 py-20 text-center">
                <div class="w-20 h-20 bg-[#2A2729] rounded-full flex items-center justify-center mx-auto mb-6 border border-[#4E3B46]">
                    <x-icon name="sun" class="w-10 h-10 text-[#A0717F] opacity-50" />
                </div>
                <h4 class="text-xl font-serif text-[#EAD3CD] mb-2 font-bold decoration-[#A0717F]">All Clear</h4>
                <p class="text-xs text-[#CFCBCA] opacity-50 max-w-xs mx-auto">There are currently no rooms awaiting inspection. Everything is in order throughout the properties.</p>
            </div>
        @endif
    </div>
@endsection




