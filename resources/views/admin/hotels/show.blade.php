@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="mb-8 border-b border-[#4E3B46] flex justify-between items-start"
        style="background: linear-gradient(180deg, rgba(42, 39, 41, 0.5) 0%, transparent 100%);">
        <div class="py-6">
            <h1 class="text-3xl font-semibold text-[#EAD3CD]">{{ $hotel->name }}</h1>
            <p class="text-sm mt-2 text-[#CFCBCA]">Owner: {{ $hotel->owner->name }}</p>
        </div>
        <a href="{{ route('admin.users.show', $hotel->owner) }}"
            class="px-4 py-2 text-sm border border-[#4E3B46] text-[#CFCBCA] rounded-lg hover:bg-[#2A2729] transition">
            View Owner
        </a>
    </div>

    <!-- Hotel Stats - 3 Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="border border-[#4E3B46] rounded-lg p-6 bg-[#383537]">
            <p class="text-xs uppercase tracking-wider text-[#CFCBCA] font-medium">Total Rooms</p>
            <p class="text-3xl font-semibold text-[#EAD3CD] mt-3">{{ $hotel->rooms->count() }}</p>
            <p class="text-xs text-[#CFCBCA] mt-1">Available properties</p>
        </div>

        <div class="border border-[#4E3B46] rounded-lg p-6 bg-[#383537]">
            <p class="text-xs uppercase tracking-wider text-[#CFCBCA] font-medium">Status</p>
            <div class="mt-3">
                @if ($hotel->verified)
                    <span
                        class="inline-block px-2.5 py-1 text-xs bg-green-900/50 text-green-300 rounded font-medium border border-green-800">Verified</span>
                @else
                    <span
                        class="inline-block px-2.5 py-1 text-xs bg-yellow-900/50 text-yellow-300 rounded font-medium border border-yellow-800">Pending</span>
                @endif
            </div>
            <p class="text-xs text-[#CFCBCA] mt-2">Verification state</p>
        </div>

        <div class="border border-[#4E3B46] rounded-lg p-6 bg-[#383537]">
            <p class="text-xs uppercase tracking-wider text-[#CFCBCA] font-medium">Contact</p>
            <p class="text-sm font-medium text-[#EAD3CD] mt-3">{{ $hotel->email ?? 'N/A' }}</p>
            <p class="text-xs text-[#CFCBCA] mt-1">{{ $hotel->phone ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Hotel Information -->
    <div class="border border-[#4E3B46] rounded-lg p-6 mb-8 bg-[#383537]">
        <h3 class="text-sm font-semibold text-[#EAD3CD] uppercase tracking-wider mb-4">Hotel Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border-b border-[#4E3B46] pb-4">
                <p class="text-xs text-[#CFCBCA] font-medium">Location</p>
                <p class="text-sm text-[#EAD3CD] mt-2">{{ $hotel->location ?? 'Not specified' }}</p>
            </div>
            <div class="border-b border-[#4E3B46] pb-4">
                <p class="text-xs text-[#CFCBCA] font-medium">Address</p>
                <p class="text-sm text-[#EAD3CD] mt-2">{{ $hotel->address ?? 'Not specified' }}</p>
            </div>
            <div class="border-b border-[#4E3B46] pb-4">
                <p class="text-xs text-[#CFCBCA] font-medium">Email</p>
                <p class="text-sm text-[#EAD3CD] mt-2">{{ $hotel->email ?? 'N/A' }}</p>
            </div>
            <div class="border-b border-[#4E3B46] pb-4">
                <p class="text-xs text-[#CFCBCA] font-medium">Phone</p>
                <p class="text-sm text-[#EAD3CD] mt-2">{{ $hotel->phone ?? 'N/A' }}</p>
            </div>
        </div>

        @if ($hotel->description)
            <div class="border-t border-[#4E3B46] mt-6 pt-6">
                <p class="text-xs text-[#CFCBCA] font-medium">Description</p>
                <p class="text-sm text-[#EAD3CD] mt-2">{{ $hotel->description }}</p>
            </div>
        @endif
    </div>

    <!-- Rooms Table -->
    <div class="border border-[#4E3B46] rounded-lg overflow-hidden bg-[#383537]">
        <div class="p-6 border-b border-[#4E3B46]" style="background-color: #2A2729;">
            <h3 class="text-sm font-semibold text-[#EAD3CD] uppercase tracking-wider">Room Inventory</h3>
        </div>

        @if ($hotel->rooms->count() > 0)
            <table class="w-full text-sm">
                <thead style="background-color: #2A2729;">
                    <tr class="border-b border-[#4E3B46]">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">Room
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">
                            Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">Price
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#4E3B46]">
                    @foreach ($hotel->rooms as $room)
                        <tr class="hover:bg-[#2A2729] transition">
                            <td class="px-6 py-3 font-medium text-[#EAD3CD]">{{ $room->room_number }}</td>
                            <td class="px-6 py-3 text-[#CFCBCA]">{{ ucfirst($room->room_type) }}</td>
                            <td class="px-6 py-3 text-[#CFCBCA]">{{ $room->capacity }} guests</td>
                            <td class="px-6 py-3 font-medium text-[#A0717F]">
                                ${{ number_format($room->price_per_night, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-12 text-center" style="background-color: #2A2729;">
                <p class="text-[#EAD3CD] font-semibold">No rooms available</p>
                <p class="text-xs text-[#CFCBCA] mt-1">This hotel has no rooms listed yet</p>
            </div>
        @endif
    </div>
@endsection




