@extends('layouts.app')

@php
    $pageTitle = 'Cleaning Tasks';
@endphp

@section('content')
    @if ($staffRoles->count() > 0)
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg border border-green-500 bg-green-500/20 text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg border border-red-500 bg-red-500/20 text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <!-- Cleaning Tasks Stats -->
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-6 mb-8 border-t-4 border-t-[#A0717F]">
            <h3 class="text-sm font-semibold text-[#EAD3CD]">Rooms Needing Cleaning</h3>
            <p class="text-3xl font-bold mt-2 text-[#A0717F]">{{ $roomsNeedsCleaning->count() }}</p>
        </div>

        <!-- Tasks List -->
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden">
            <div class="bg-[#2A2729] border-b border-[#4E3B46] px-6 py-4">
                <h3 class="text-lg font-semibold text-[#EAD3CD]">Assigned Cleaning Tasks</h3>
            </div>

            @if ($roomsNeedsCleaning->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#2A2729] border-b border-[#4E3B46]">
                            <tr>
                                <th class="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Hotel</th>
                                <th class="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Room Number</th>
                                <th class="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Room Type</th>
                                <th class="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Capacity</th>
                                <th class="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Last Guest</th>
                                <th class="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Status</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-[#CFCBCA]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roomsNeedsCleaning as $room)
                                <tr class="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                                    <td class="px-6 py-4 text-[#A0717F] font-semibold">{{ $room->hotel->name }}</td>
                                    <td class="px-6 py-4 font-semibold text-[#EAD3CD]">{{ $room->room_number }}</td>
                                    <td class="px-6 py-4 text-[#CFCBCA]">{{ $room->room_type }}</td>
                                    <td class="px-6 py-4 text-[#CFCBCA]">{{ $room->capacity }} guests</td>
                                    <td class="px-6 py-4 text-[#CFCBCA]">
                                        @if ($room->bookingItems->last()?->booking?->user?->name)
                                            {{ $room->bookingItems->last()->booking->user->name }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border border-[#A0717F] text-[#A0717F] bg-[#2A2729]">
                                            Cleaning
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('cleaner.rooms.complete-form', $room) }}"
                                            class="bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] px-4 py-2 rounded transition inline-block">
                                            Mark as Complete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-[#EAD3CD] text-lg">All rooms are clean. Great job! 🎉</p>
                </div>
            @endif
        </div>
    @else
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow p-6 text-center">
            <p class="text-[#CFCBCA]">You have not been assigned to any hotel yet.</p>
        </div>
    @endif
@endsection




