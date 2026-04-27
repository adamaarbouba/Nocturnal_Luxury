@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>
    <div class="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="max-w-7xl mx-auto px-6 py-12 relative z-10">
        {{-- Elegant Navigation --}}
        <div class="mb-12">
            <x-breadcrumbs :links="[
                ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'User Directory', 'url' => '#'],
            ]" />
        </div>

        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">System Registry</p>
                <h1 class="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">User Directory</h1>
                <p class="text-[10px] uppercase mt-4 text-[#CFCBCA]/60 tracking-[0.15em]">
                    Manage access, roles, and privileges across the Nocturnal Luxury network
                </p>
            </div>
            
            <div class="text-right">
                <p class="text-[9px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/60 mb-2">Total Registered</p>
                <p class="text-3xl font-bold font-serif text-[#EAD3CD]">{{ $users->total() }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-8 p-6 bg-[#2A2729] border-l-4 border-[#A0717F] rounded-r-2xl shadow-xl flex items-start gap-4">
                <x-icon name="check-circle" class="w-6 h-6 text-[#A0717F] shrink-0" />
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">System Notification</p>
                    <p class="text-sm text-[#EAD3CD]">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Search & Filter Card -->
        <div class="bg-[#2A2729] rounded-3xl shadow-xl p-8 mb-12 border border-[#4E3B46] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none"></div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="relative z-10" id="search-form">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-5">
                        <label for="search" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Identity Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <x-icon name="magnifying-glass" size="sm" class="text-[#A0717F]/50" />
                            </div>
                            <input type="text" id="search" name="search" placeholder="Query by name, email..."
                                value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 placeholder-[#CFCBCA]/30">
                        </div>
                    </div>

                    <!-- Role Filter -->
                    <div class="lg:col-span-4">
                        <label for="role" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Classification</label>
                        <select id="role" name="role"
                            class="w-full px-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 appearance-none cursor-pointer">
                            <option value="">Global (All Roles)</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->slug }}" @if (request('role') === $role->slug) selected @endif>
                                    {{ ucfirst($role->slug) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="lg:col-span-3 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.2em] text-[10px] py-4 rounded-xl shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            Execute Query
                        </button>
                        @if (request('search') || request('role'))
                            <a href="{{ route('admin.users.index') }}"
                                class="w-full text-center py-3 text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-all duration-300">
                                Clear Parameters
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <script>
                document.getElementById('search-form').addEventListener('submit', function(e) {
                    const roleField = this.querySelector('select[name="role"]');
                    if (!roleField.value || roleField.value === '') {
                        roleField.removeAttribute('name');
                    }
                });
            </script>
        </div>

        <!-- Directory Table -->
        <div class="bg-[#2A2729] border border-[#4E3B46] rounded-3xl shadow-2xl overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none"></div>

            @if ($users->count() > 0)
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-[#4E3B46]/50 bg-[#383537]/50">
                                <th class="px-8 py-6 text-[9px] font-bold text-[#A0717F] uppercase tracking-[0.2em]">Identity</th>
                                <th class="px-8 py-6 text-[9px] font-bold text-[#A0717F] uppercase tracking-[0.2em]">Contact</th>
                                <th class="px-8 py-6 text-[9px] font-bold text-[#A0717F] uppercase tracking-[0.2em]">Classification</th>
                                <th class="px-8 py-6 text-[9px] font-bold text-[#A0717F] uppercase tracking-[0.2em]">Registration</th>
                                <th class="px-8 py-6 text-[9px] font-bold text-[#A0717F] uppercase tracking-[0.2em]">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#4E3B46]/30">
                            @foreach ($users as $user)
                                <tr class="hover:bg-[#383537]/50 transition-colors duration-300 group">
                                    <td class="px-8 py-6">
                                        <a href="{{ route('admin.users.show', $user) }}" class="flex flex-col">
                                            <span class="font-serif text-lg font-bold text-[#EAD3CD] group-hover:text-[#A0717F] transition-colors">{{ $user->name }}</span>
                                            @if ($user->banned_at)
                                                <span class="inline-flex items-center gap-1 mt-1 text-[9px] font-bold uppercase tracking-[0.2em] text-red-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Sanctioned
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 mt-1 text-[9px] font-bold uppercase tracking-[0.2em] text-green-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Active
                                                </span>
                                            @endif
                                        </a>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-[#CFCBCA]">{{ $user->email }}</span>
                                            <span class="text-[10px] text-[#CFCBCA]/60 mt-1 tracking-widest">{{ $user->phone ?? 'Unregistered' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="inline-block px-3 py-1 bg-[#383537] border border-[#4E3B46] rounded-md text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA]">
                                            {{ ucfirst($user->role->slug) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-sm text-[#EAD3CD]">{{ $user->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 border border-[#4E3B46] rounded-lg text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] hover:bg-[#383537] transition-all duration-300">
                                            Dossier →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($users->hasPages())
                    <div class="px-8 py-6 border-t border-[#4E3B46]/50 bg-[#383537]/20 flex justify-center relative z-10">
                        <style>
                            .pagination { display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; }
                            .pagination li { list-style: none; }
                            .pagination a, .pagination span {
                                display: flex; align-items: center; justify-content: center;
                                min-width: 2.5rem; height: 2.5rem; padding: 0 0.75rem;
                                font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em;
                                border: 1px solid #4E3B46; border-radius: 0.75rem;
                                transition: all 0.3s ease; color: #CFCBCA; background-color: #2A2729;
                            }
                            .pagination a:hover { background-color: #A0717F; color: #FFFFFF; border-color: #A0717F; transform: translateY(-2px); }
                            .pagination .active span { background-color: #A0717F; color: #FFFFFF; border-color: #A0717F; box-shadow: 0 4px 12px rgba(160, 113, 127, 0.3); }
                            .pagination .disabled span { opacity: 0.3; cursor: not-allowed; background-color: transparent; }
                        </style>
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="p-16 text-center relative z-10">
                    <div class="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                        <x-icon name="users" class="w-8 h-8 text-[#A0717F]" />
                    </div>
                    <h3 class="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">No Identities Found</h3>
                    <p class="text-xs uppercase tracking-widest mb-10 leading-loose" style="color: rgba(207, 203, 202, 0.5); max-w-md mx-auto;">
                        The system registry query returned no results matching your exact classification criteria.
                    </p>
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1 border border-[#4E3B46]"
                        style="background-color: #383537;"
                        onmouseover="this.style.backgroundColor='#4E3B46';"
                        onmouseout="this.style.backgroundColor='#383537';">
                        Clear Parameters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
@endsection
