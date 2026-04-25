@extends('layouts.app')

@php
    $pageTitle = 'Staff Dashboard';
@endphp

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow p-6 border-t-4 border-t-[#4E3B46]">
            <h3 class="text-sm font-semibold text-[#EAD3CD]">Assigned Hotels</h3>
            <p class="text-3xl font-bold mt-2 text-[#EAD3CD]">--</p>
        </div>
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow p-6 border-t-4 border-t-[#A0717F]">
            <h3 class="text-sm font-semibold text-[#EAD3CD]">Pending Tasks</h3>
            <p class="text-3xl font-bold mt-2 text-[#A0717F]">--</p>
        </div>
        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow p-6 border-t-4 border-t-[#4E3B46]">
            <h3 class="text-sm font-semibold text-[#EAD3CD]">Completed Today</h3>
            <p class="text-3xl font-bold mt-2 text-[#EAD3CD]">--</p>
        </div>
    </div>


    <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <h3 class="text-xl font-semibold mb-6 text-[#EAD3CD]">My Tasks</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="#" class="p-4 border border-[#4E3B46] rounded-lg transition hover:bg-[#2A2729]">
                <h4 class="font-semibold text-[#EAD3CD]">My Assignments</h4>
                <p class="text-sm mt-1 text-[#CFCBCA]">View assigned tasks and rooms</p>
            </a>
            <a href="#" class="p-4 border border-[#4E3B46] rounded-lg transition hover:bg-[#2A2729]">
                <h4 class="font-semibold text-[#EAD3CD]">Log Activity</h4>
                <p class="text-sm mt-1 text-[#CFCBCA]">Record cleaning or inspection work</p>
            </a>
            <a href="#" class="p-4 border border-[#4E3B46] rounded-lg transition hover:bg-[#2A2729]">
                <h4 class="font-semibold text-[#A0717F]">Room Status</h4>
                <p class="text-sm mt-1 text-[#CFCBCA]">Update room conditions</p>
            </a>
            <a href="#" class="p-4 border border-[#4E3B46] rounded-lg transition hover:bg-[#2A2729]">
                <h4 class="font-semibold text-[#EAD3CD]">Availability</h4>
                <p class="text-sm mt-1 text-[#CFCBCA]">Set your availability schedule</p>
            </a>
        </div>
    </div>


    <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <h3 class="text-xl font-semibold mb-6 text-[#EAD3CD]">Recent Activity</h3>
        <p class="text-[#CFCBCA]">No tasks logged yet. Start by viewing your assignments.</p>
    </div>

    <div class="p-6 rounded-lg bg-[#2A2729] border border-[#4E3B46]">
        <p class="text-[#CFCBCA]"><strong>Note:</strong> This is a staff dashboard for cleaners and inspectors. Use this to
            track your tasks and log activities.</p>
    @endsection




