@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>
    <div class="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="max-w-6xl mx-auto px-6 py-12 relative z-10">
        {{-- Elegant Navigation --}}
        <div class="mb-12">
            <x-breadcrumbs :links="[
                ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'User Directory', 'url' => route('admin.users.index')],
                ['label' => 'User Dossier', 'url' => '#'],
            ]" />
        </div>

        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Identity Dossier</p>
                <div class="flex items-center gap-4">
                    <h1 class="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">{{ $user->name }}</h1>
                    @if ($user->banned_at)
                        <span class="px-3 py-1 bg-red-900/30 text-red-400 border border-red-900/50 rounded-full text-[9px] font-bold uppercase tracking-[0.2em]">Sanctioned</span>
                    @else
                        <span class="px-3 py-1 bg-green-900/20 text-green-400 border border-green-900/30 rounded-full text-[9px] font-bold uppercase tracking-[0.2em]">Active</span>
                    @endif
                </div>
                <p class="text-[10px] uppercase mt-4 text-[#CFCBCA]/60 tracking-[0.15em]">
                    System Member Since {{ $user->created_at->format('M d, Y') }}
                </p>
            </div>
            
            <div class="flex gap-4">
                @if ($user->banned_at)
                    <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] transition-all duration-300 shadow-xl"
                            style="background-color: #4E3B46; color: #EAD3CD;"
                            onmouseover="this.style.backgroundColor='#68525F'; this.style.transform='translateY(-2px)';"
                            onmouseout="this.style.backgroundColor='#4E3B46'; this.style.transform='translateY(0)';">
                            <x-icon name="shield-check" size="sm" class="w-4 h-4 text-[#A0717F]" />
                            Restore Access
                        </button>
                    </form>
                @else
                    <button type="button" onclick="openBanModal()"
                        class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] transition-all duration-300 shadow-xl"
                        style="background-color: #A0717F; color: #FFFFFF;"
                        onmouseover="this.style.backgroundColor='#8F6470'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                        <x-icon name="shield-exclamation" size="sm" class="w-4 h-4" />
                        Sanction User
                    </button>
                    <form id="banForm" action="{{ route('admin.users.ban', $user) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Core Identity -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 relative overflow-hidden group hover:border-[#A0717F]/50 transition-colors duration-500">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none"></div>
                        <p class="text-[9px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/60 mb-2">Classification</p>
                        <p class="text-3xl font-bold font-serif text-[#EAD3CD] group-hover:text-[#A0717F] transition-colors duration-500">{{ ucfirst($user->role->slug) }}</p>
                        <x-icon name="star" class="absolute bottom-6 right-6 w-8 h-8 text-[#A0717F]/20 group-hover:scale-110 transition-transform duration-500" />
                    </div>

                    <div class="bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 relative overflow-hidden group hover:border-[#A0717F]/50 transition-colors duration-500">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none"></div>
                        <p class="text-[9px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/60 mb-2">Record Identifier</p>
                        <p class="text-3xl font-bold font-serif text-[#EAD3CD]">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <x-icon name="identification" class="absolute bottom-6 right-6 w-8 h-8 text-[#A0717F]/20 group-hover:scale-110 transition-transform duration-500" />
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 lg:p-12 relative overflow-hidden shadow-xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none"></div>
                    
                    <h2 class="text-xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
                        <span class="w-8 h-[1px] bg-[#A0717F]"></span>
                        Contact Registry
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 relative z-10">
                        <div class="space-y-1">
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Electronic Mail</p>
                            <p class="text-sm font-medium text-[#EAD3CD] break-words">{{ $user->email }}</p>
                        </div>
                        
                        <div class="space-y-1">
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Telephone Number</p>
                            <p class="text-sm font-medium text-[#EAD3CD]">{{ $user->phone ?? 'Unregistered' }}</p>
                        </div>
                        
                        <div class="space-y-1 md:col-span-2">
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Physical Address</p>
                            <p class="text-sm font-medium text-[#EAD3CD]">{{ $user->address ?? 'Unregistered' }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Associated Data -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Role-Based Sections -->
                @if ($user->role->slug === 'owner')
                    <div class="bg-[#383537] border border-[#4E3B46] rounded-3xl p-8 shadow-xl">
                        <h2 class="text-lg font-bold font-serif text-[#EAD3CD] mb-6 flex items-center gap-3">
                            <x-icon name="building-office" class="w-5 h-5 text-[#A0717F]" />
                            Portfolio
                        </h2>
                        
                        @if ($hotelInfo && $hotelInfo->count() > 0)
                            <div class="space-y-4">
                                @foreach ($hotelInfo as $hotel)
                                    <div class="group bg-[#2A2729] border border-[#4E3B46] rounded-xl p-5 hover:border-[#A0717F]/50 transition-all duration-300 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#A0717F] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <p class="font-bold text-[#EAD3CD] text-sm group-hover:text-[#A0717F] transition-colors">{{ $hotel->name }}</p>
                                                <p class="text-[9px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mt-1">{{ $hotel->rooms->count() }} Suites</p>
                                            </div>
                                            <span class="inline-block px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest border
                                                {{ $hotel->verified ? 'bg-green-500/10 text-green-400 border-green-500/30' : 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30' }}">
                                                {{ $hotel->verified ? 'Verified' : 'Pending' }}
                                            </span>
                                        </div>
                                        <a href="{{ route('admin.hotels.show', $hotel) }}" class="inline-flex text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-colors">
                                            View Property →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 px-4 border border-[#4E3B46]/30 rounded-2xl bg-[#2A2729]/50">
                                <p class="text-[10px] uppercase tracking-[0.1em] text-[#CFCBCA]/60">No properties registered to this portfolio.</p>
                            </div>
                        @endif
                    </div>
                @elseif ($user->role->slug === 'receptionist' || $user->role->slug === 'cleaner' || $user->role->slug === 'inspector')
                    <div class="bg-[#383537] border border-[#4E3B46] rounded-3xl p-8 shadow-xl">
                        <h2 class="text-lg font-bold font-serif text-[#EAD3CD] mb-6 flex items-center gap-3">
                            <x-icon name="briefcase" class="w-5 h-5 text-[#A0717F]" />
                            Assignment
                        </h2>
                        
                        @if ($hotelInfo)
                            <div class="group bg-[#2A2729] border border-[#4E3B46] rounded-xl p-6 hover:border-[#A0717F]/50 transition-all duration-300 relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#A0717F] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Stationed At</p>
                                <p class="font-bold text-[#EAD3CD] text-lg font-serif mb-1 group-hover:text-[#A0717F] transition-colors">{{ $hotelInfo->name }}</p>
                                <p class="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mb-4">{{ $hotelInfo->location ?? $hotelInfo->city }}</p>
                                
                                <a href="{{ route('admin.hotels.show', $hotelInfo) }}" class="inline-flex text-[9px] font-bold uppercase tracking-[0.2em] text-[#EAD3CD] bg-[#4E3B46] hover:bg-[#68525F] px-4 py-2 rounded-lg transition-colors w-full justify-center">
                                    View Property
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8 px-4 border border-[#4E3B46]/30 rounded-2xl bg-[#2A2729]/50">
                                <p class="text-[10px] uppercase tracking-[0.1em] text-[#CFCBCA]/60">This staff member is currently unassigned.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ban Confirmation Modal -->
    <div id="banModal" class="hidden fixed inset-0 bg-[#1A1515]/90 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity"
        onclick="closeBanModal(event)">
        <div class="rounded-3xl shadow-2xl p-10 max-w-md w-full mx-4 bg-[#2A2729] border border-[#4E3B46] transform transition-transform scale-100 relative overflow-hidden" onclick="event.stopPropagation()">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <x-icon name="shield-exclamation" class="w-24 h-24 text-[#A0717F]" />
            </div>
            
            <h2 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-4 relative z-10">Sanction Member</h2>
            <p class="text-sm text-[#CFCBCA] mb-8 leading-relaxed relative z-10">
                Are you certain you wish to revoke access for <strong class="text-[#EAD3CD]">{{ $user->name }}</strong>? This action will immediately suspend their privileges within the Nocturnal Luxury network.
            </p>
            <div class="flex gap-4 relative z-10">
                <button type="button" onclick="closeBanModal()"
                    class="flex-1 px-6 py-4 border border-[#4E3B46] text-[#CFCBCA] rounded-xl hover:bg-[#383537] hover:text-[#EAD3CD] transition-all duration-300 text-[10px] font-bold uppercase tracking-widest">
                    Cancel
                </button>
                <button type="button" onclick="submitBanForm()"
                    class="flex-1 px-6 py-4 text-white rounded-xl transition-all duration-300 text-[10px] font-bold uppercase tracking-widest shadow-lg" 
                    style="background-color: #A0717F;"
                    onmouseover="this.style.backgroundColor='#8F6470'" onmouseout="this.style.backgroundColor='#A0717F'">
                    Confirm Ban
                </button>
            </div>
        </div>
    </div>

    <script>
        function openBanModal() {
            document.getElementById('banModal').classList.remove('hidden');
        }

        function closeBanModal(event) {
            if (!event || event.target.id === 'banModal') {
                document.getElementById('banModal').classList.add('hidden');
            }
        }

        function submitBanForm() {
            document.getElementById('banForm').submit();
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeBanModal();
            }
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
@endsection

