@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <a href="{{ route('cleaner.dashboard') }}" class="text-[#CFCBCA] hover:text-[#EAD3CD] mb-6 inline-block">
            ← Back to Dashboard
        </a>

        <div class="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-[#EAD3CD] mb-2">Room {{ $room->room_number }} - Cleaning Completion</h2>
            <p class="text-[#CFCBCA] mb-8">Room Type: {{ $room->room_type }} | Capacity: {{ $room->capacity }} guests</p>

            <!-- Previous Cleaning History -->
            @if ($cleaningHistory->count() > 0)
                <div class="mb-8 bg-[#2A2729] border border-[#4E3B46] rounded-lg p-4">
                    <h3 class="font-semibold text-[#EAD3CD] mb-4">Previous History</h3>
                    <div class="space-y-3 max-h-60 overflow-y-auto">
                        @foreach ($cleaningHistory as $log)
                            <div
                                class="@if ($log->type === 'inspection') bg-[#1A1515] border border-red-500 @else border border-[#4E3B46] @endif rounded p-3 text-sm">
                                <div class="flex justify-between items-start mb-1">
                                    <div>
                                        <span class="font-semibold text-[#EAD3CD]">{{ $log->user->name }}</span>
                                        @if ($log->type === 'inspection')
                                            <span class="ml-2 text-xs border border-red-500 text-red-400 px-2 py-1 rounded bg-[#2A2729]">Inspector
                                                Feedback</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-[#CFCBCA]">{{ $log->created_at->format('M d, H:i') }}</span>
                                </div>
                                @if ($log->action)
                                    <p class="text-xs text-[#CFCBCA] mb-1">
                                        <strong>Action:</strong>
                                        @if ($log->action === 'finished')
                                            <span class="text-green-400">✓ Finished Cleaning</span>
                                        @elseif ($log->action === 're-clean')
                                            <span class="text-yellow-400">⟳ Marked for Re-cleaning</span>
                                        @endif
                                    </p>
                                @endif
                                @if ($log->notes)
                                    <p class="text-[#EAD3CD] mt-1"><strong>Notes:</strong> {{ $log->notes }}</p>
                                @else
                                    <p class="text-[#CFCBCA] italic">No notes provided</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('cleaner.rooms.complete', $room) }}" class="space-y-6">
                @csrf

                <!-- Notes Section -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-[#EAD3CD] mb-2">
                        Cleaning Notes
                    </label>
                    <p class="text-xs text-[#CFCBCA] mb-3">
                        Add any notes about the cleaning. These will be visible to the Inspector and Receptionist.
                    </p>
                    <textarea id="notes" name="notes" rows="5"
                        placeholder="e.g., Minor stain on carpet, requested maintenance attention. Or: All good, room is clean and ready."
                        class="w-full px-4 py-2 bg-[#383537] text-[#EAD3CD] border border-[#4E3B46] rounded-lg focus:outline-none focus:border-[#A0717F]">
                        </textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Room Status Info -->
                <div class="bg-[#2A2729] border-l-4 border-[#A0717F] p-4 rounded">
                    <p class="text-sm text-[#EAD3CD]">
                        <strong>Current Status:</strong> Cleaning
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <!-- Finished Cleaning Button -->
                    <button type="submit" name="action" value="finished"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                        ✓ Finished Cleaning
                    </button>

                    <!-- For Cleaning Again Button -->
                    <button type="submit" name="action" value="re-clean"
                        class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                        ⟳ For Cleaning Again
                    </button>

                    <!-- Cancel Button -->
                    <a href="{{ route('cleaner.dashboard') }}"
                        class="flex-1 bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] font-semibold px-6 py-3 rounded-lg transition text-center border border-[#4E3B46]">
                        Cancel
                    </a>
                </div>

                <!-- Action Descriptions -->
                <div class="grid grid-cols-2 gap-4 text-xs text-[#CFCBCA]">
                    <div class="bg-[#1A1515] border border-green-500 p-3 rounded">
                        <p class="font-semibold text-green-400">Finished Cleaning</p>
                        <p>Room moves to Inspection for Inspector review</p>
                    </div>
                    <div class="bg-[#1A1515] border border-yellow-500 p-3 rounded">
                        <p class="font-semibold text-yellow-400">For Cleaning Again</p>
                        <p>Room stays in Cleaning. Your notes are left as a remark.</p>
                    </div>
                </div>
            </form>
        </div>
</div>@endsection




