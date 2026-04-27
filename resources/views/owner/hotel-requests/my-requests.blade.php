@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-[#EAD3CD]">My Hotel Requests</h2>
            <a href="{{ route('owner.hotel-requests.create') }}"
                class="bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition">
                Request New Hotel
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-900/50 border border-green-800 text-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($requests->count() > 0)
            <div class="space-y-4">
                @foreach ($requests as $request)
                    <div
                        class="rounded-lg shadow-md p-6 bg-[#383537] border-l-4 @if ($request->status === 'pending') border-[#A0717F] @elseif($request->status === 'approved') border-[#4E3B46] @else border-[#4E3B46] @endif">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-2xl font-bold text-[#EAD3CD]">{{ $request->name }}</h3>
                                    <span
                                        class="inline-block px-3 py-1 rounded-full text-white font-semibold text-sm @if ($request->status === 'pending') bg-[#A0717F] @elseif($request->status === 'approved') border border-[#4E3B46] text-[#CFCBCA] bg-[#2A2729] @else border border-[#4E3B46] text-[#CFCBCA] bg-[#2A2729] @endif">
                                        @if ($request->status === 'pending')
                                            Pending Review
                                        @elseif($request->status === 'approved')
                                            Approved
                                        @else
                                            Rejected
                                        @endif
                                    </span>
                                </div>

                                <p class="text-[#CFCBCA] text-sm mb-3">Submitted:
                                    <strong>{{ $request->created_at->format('M d, Y h:i A') }}</strong>
                                </p>

                                <!-- Request Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm my-4">
                                    <div>
                                        <span class="text-[#CFCBCA]">Location:</span>
                                        <p class="text-[#EAD3CD]"><strong>{{ $request->address }}, {{ $request->city }},
                                                {{ $request->country }}</strong></p>
                                    </div>
                                    <div>
                                        <span class="text-[#CFCBCA]">Email:</span>
                                        <p class="text-[#EAD3CD]"><strong>{{ $request->email }}</strong></p>
                                    </div>
                                    <div>
                                        <span class="text-[#CFCBCA]">Phone:</span>
                                        <p class="text-[#EAD3CD]"><strong>{{ $request->phone }}</strong></p>
                                    </div>
                                </div>

                                @if ($request->description)
                                    <div class="my-4 pt-4 border-t border-[#4E3B46]">
                                        <span class="text-[#CFCBCA] text-sm leading-relaxed">Description:</span>
                                        <p class="text-[#EAD3CD] text-sm mt-1">{{ $request->description }}</p>
                                    </div>
                                @endif

                                <!-- Status-specific Information -->
                                @if ($request->status === 'approved')
                                    <div class="mt-4 p-4 bg-green-900/50 border border-green-800 rounded">
                                        <p class="text-green-300 text-sm"><strong>Approved by
                                                {{ $request->reviewer->name }}</strong> on
                                            {{ $request->reviewed_at->format('M d, Y') }}</p>
                                        @if ($request->admin_notes)
                                            <p class="text-green-300 text-sm mt-2"><strong>Admin Notes:</strong>
                                                {{ $request->admin_notes }}</p>
                                        @endif
                                        <p class="text-green-300 text-sm mt-3 font-semibold">Your hotel has been
                                            created! You can now manage it from your dashboard.</p>
                                    </div>
                                @elseif ($request->status === 'rejected')
                                    <div class="mt-4 p-4 bg-red-900/50 border border-red-800 rounded">
                                        <p class="text-red-300 text-sm"><strong>Rejected by
                                                {{ $request->reviewer->name }}</strong> on
                                            {{ $request->reviewed_at->format('M d, Y') }}</p>
                                        @if ($request->admin_notes)
                                            <p class="text-red-300 text-sm mt-2"><strong>Reason:</strong>
                                                {{ $request->admin_notes }}</p>
                                        @endif
                                        <p class="text-red-300 text-sm mt-3">You can submit a new request after
                                            addressing the concerns mentioned above.</p>
                                    </div>
                                @else
                                    <div class="mt-4 p-4 bg-[#2A2729] border border-[#4E3B46] rounded">
                                        <p class="text-[#CFCBCA] text-sm">Your request is under review. An admin will
                                            review it shortly.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @else
            <div class="bg-[#383537] border border-[#4E3B46] p-8 rounded-lg text-center">
                <p class="text-[#CFCBCA] mb-4">You haven't submitted any hotel requests yet.</p>
                <a href="{{ route('owner.hotel-requests.create') }}"
                    class="inline-block bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-3 rounded transition">
                    Submit Your First Hotel Request
                </a>
            </div>
        @endif
</div>@endsection




