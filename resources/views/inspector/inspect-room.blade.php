@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>
    <div class="fixed bottom-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="container mx-auto px-4 py-8 max-w-5xl relative z-10">
        {{-- Breadcrumbs --}}
        <x-breadcrumbs :links="[
            ['label' => 'Inspector Dashboard', 'url' => route('inspector.dashboard')],
            ['label' => 'Inspection: Room ' . $room->room_number, 'url' => '#'],
        ]" />

        {{-- Page Header --}}
        <div class="mb-10">
            <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                {{ $room->hotel->name }}
            </p>
            <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                Quality Inspection
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Room Info & Form -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Room Details Card -->
                <div class="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-[#4E3B46] bg-[#2A2729]">
                        <h3 class="text-xl font-bold text-[#EAD3CD] font-serif">Room {{ $room->room_number }} Dossier</h3>
                    </div>
                    <div class="p-8 grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Classification</p>
                            <p class="text-lg font-semibold text-[#EAD3CD]">{{ $room->room_type }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Capacity</p>
                            <p class="text-lg font-semibold text-[#EAD3CD]">{{ $room->capacity }} Guests</p>
                        </div>
                    </div>
                </div>

                <!-- Inspection Form Card -->
                <div class="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-[#4E3B46] bg-[#2A2729]">
                        <h3 class="text-xl font-bold text-[#EAD3CD] font-serif">Final Adjudication</h3>
                    </div>
                    <div class="p-8">
                        <form method="POST" action="{{ route('inspector.rooms.inspect', $room) }}" class="space-y-8">
                            @csrf

                            <!-- Result Selection -->
                            <div>
                                <label class="block text-sm font-semibold text-[#EAD3CD] mb-4">Inspection Result</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="action" value="approved" class="peer sr-only" required>
                                        <div class="p-4 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-green-500 peer-checked:bg-green-500/10 transition-all text-center">
                                            <x-icon name="check" class="w-6 h-6 mx-auto mb-2 text-green-500" />
                                            <span class="block text-xs font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-green-400">Approve</span>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="action" value="rejected" class="peer sr-only" required>
                                        <div class="p-4 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-red-500 peer-checked:bg-red-500/10 transition-all text-center">
                                            <x-icon name="x" class="w-6 h-6 mx-auto mb-2 text-red-500" />
                                            <span class="block text-xs font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-red-400">Reject</span>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="action" value="maintenance" class="peer sr-only" required>
                                        <div class="p-4 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-orange-500 peer-checked:bg-orange-500/10 transition-all text-center">
                                            <x-icon name="tool" class="w-6 h-6 mx-auto mb-2 text-orange-500" />
                                            <span class="block text-xs font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-orange-400">Maintenance</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Severity (Hidden initially) -->
                            <div id="severityField" class="hidden animate-fade-in">
                                <label for="severity" class="block text-sm font-semibold text-[#EAD3CD] mb-3">Issue Severity</label>
                                <select name="severity" id="severity"
                                    class="w-full px-4 py-3 bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#A0717F] transition-all">
                                    <option value="minor">Minor - Surface issues</option>
                                    <option value="moderate">Moderate - Noticeable flaws</option>
                                    <option value="severe">Severe - Deep cleaning required</option>
                                </select>
                            </div>

                            <!-- Priority (Hidden initially) -->
                            <div id="priorityField" class="hidden animate-fade-in">
                                <label for="priority" class="block text-sm font-semibold text-[#EAD3CD] mb-3">Maintenance Priority</label>
                                <select name="priority" id="priority"
                                    class="w-full px-4 py-3 bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#A0717F] transition-all">
                                    <option value="low">Low - Routine</option>
                                    <option value="normal">Normal - Standard fix</option>
                                    <option value="urgent">Urgent - Immediate attention</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-semibold text-[#EAD3CD] mb-3">Professional Remarks</label>
                                <textarea id="notes" name="notes" rows="4"
                                    placeholder="Enter detailed observations or specific requirements..."
                                    class="w-full px-4 py-3 bg-[#2A2729] text-[#EAD3CD] border border-[#4E3B46] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#A0717F] transition-all"></textarea>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button type="submit" class="flex-1 bg-[#A0717F] hover:bg-[#b58290] text-white font-bold py-4 rounded-xl transition-all shadow-xl uppercase text-xs tracking-widest">
                                    Submit Adjudication
                                </button>
                                <a href="{{ route('inspector.dashboard') }}" class="px-8 py-4 bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] font-bold rounded-xl transition-all uppercase text-xs tracking-widest flex items-center">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: History -->
            <div class="space-y-8">
                <!-- Cleaning History Timeline -->
                <div class="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
                        <h4 class="text-sm font-bold text-[#EAD3CD] uppercase tracking-widest">Cleaning Audit</h4>
                    </div>
                    <div class="p-6">
                        @if ($cleaningHistory->count() > 0)
                            <div class="space-y-6 relative before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[1px] before:bg-[#4E3B46]">
                                @foreach ($cleaningHistory as $log)
                                    <div class="relative pl-8">
                                        <div class="absolute left-0 top-1 w-[22px] h-[22px] rounded-full border-2 border-[#4E3B46] bg-[#383537] flex items-center justify-center z-10">
                                            <div class="w-2 h-2 rounded-full bg-[#A0717F]"></div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-[#EAD3CD]">{{ $log->user->name }}</p>
                                            <p class="text-[10px] text-[#CFCBCA] opacity-50 mb-1">{{ $log->created_at->format('M d, H:i') }}</p>
                                            <p class="text-xs text-[#CFCBCA] leading-relaxed italic">"{{ $log->notes ?: 'No notes provided' }}"</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-[#CFCBCA] opacity-50 text-center py-4 italic">No cleaning records found.</p>
                        @endif
                    </div>
                </div>

                <!-- Previous Inspections -->
                <div class="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
                        <h4 class="text-sm font-bold text-[#EAD3CD] uppercase tracking-widest">Inspection Logs</h4>
                    </div>
                    <div class="p-6">
                        @if ($inspectionHistory->count() > 0)
                            <div class="space-y-4">
                                @foreach ($inspectionHistory as $inspection)
                                    <div class="p-3 rounded-lg border border-[#4E3B46] bg-[#2A2729]/50">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-[10px] font-bold uppercase py-0.5 px-2 rounded-md
                                                @if($inspection->status === 'approved') bg-green-500/10 text-green-400 border border-green-500/30
                                                @else bg-red-500/10 text-red-400 border border-red-500/30 @endif">
                                                {{ $inspection->status }}
                                            </span>
                                            <span class="text-[10px] text-[#CFCBCA] opacity-50">{{ $inspection->created_at->format('M d') }}</span>
                                        </div>
                                        <p class="text-[11px] text-[#CFCBCA] line-clamp-2">{{ $inspection->issue_description }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-[#CFCBCA] opacity-50 text-center py-4 italic">No previous inspections.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide severity and priority fields based on action selection
        document.querySelectorAll('input[name="action"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const severityField = document.getElementById('severityField');
                const priorityField = document.getElementById('priorityField');

                if (this.value === 'rejected') {
                    severityField.classList.remove('hidden');
                    priorityField.classList.add('hidden');
                } else if (this.value === 'maintenance') {
                    priorityField.classList.remove('hidden');
                    severityField.classList.add('hidden');
                } else {
                    severityField.classList.add('hidden');
                    priorityField.classList.add('hidden');
                }
            });
        });
    </script>
@endsection




