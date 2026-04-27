@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <x-breadcrumbs :links="[
            ['label' => 'Owner Dashboard', 'url' => route('owner.dashboard')],
            ['label' => 'New Hotel Request', 'url' => '#'],
        ]" />

        <div class="relative overflow-hidden rounded-2xl border border-[#4E3B46] bg-[#383537] shadow-2xl">
            {{-- Decorative Accent Header --}}
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#A0717F] to-transparent opacity-50"></div>
            
            <div class="p-8 md:p-12">
                <header class="mb-10 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#4E3B46] mb-6 text-[#A0717F] shadow-inner">
                        <x-icon name="building" size="lg" />
                    </div>
                    <h2 class="text-4xl font-bold font-serif text-[#EAD3CD] mb-4">Request a New Hotel</h2>
                    <p class="text-[#CFCBCA] max-w-lg mx-auto leading-relaxed">
                        Submit your property for review. Our curators will evaluate the proposal to ensure it meets our standards of nocturnal luxury.
                    </p>
                </header>

                @if ($errors->any())
                    <div class="mb-10 p-5 bg-red-950/30 border border-red-900/50 rounded-xl flex gap-4 items-start">
                        <div class="shrink-0 text-red-500 mt-0.5">
                            <x-icon name="close" size="sm" />
                        </div>
                        <div>
                            <h3 class="text-red-400 font-bold text-sm uppercase tracking-wider mb-2">Refinement Required</h3>
                            <ul class="text-red-300/80 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('owner.hotel-requests.store') }}" class="space-y-12">
                    @csrf

                    {{-- Section: Basic Information --}}
                    <section class="space-y-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="h-[1px] flex-1 bg-gradient-to-r from-transparent to-[#4E3B46]"></div>
                            <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-[#A0717F]">Essential Details</h3>
                            <div class="h-[1px] flex-1 bg-gradient-to-l from-transparent to-[#4E3B46]"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                                    Hotel Name <span class="text-[#A0717F]">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required minlength="3" maxlength="255"
                                    class="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                                    placeholder="The Nocturnal Atelier">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                                    Email Address <span class="text-[#A0717F]">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                                    placeholder="concierge@atelier.com">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                                    Contact Phone <span class="text-[#A0717F]">*</span>
                                </label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required
                                    class="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                                    placeholder="+1 (000) 000-0000">
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                                    Property Narrative <span class="text-xs text-[#4E3B46] font-normal">(Optional)</span>
                                </label>
                                <textarea name="description" rows="4"
                                    class="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46] resize-none"
                                    placeholder="Describe the architectural soul and atmosphere of the hotel...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </section>

                    {{-- Section: Location Information --}}
                    <section class="space-y-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="h-[1px] flex-1 bg-gradient-to-r from-transparent to-[#4E3B46]"></div>
                            <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-[#A0717F]">Geographic Atelier</h3>
                            <div class="h-[1px] flex-1 bg-gradient-to-l from-transparent to-[#4E3B46]"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-medium text-[#CFCBCA]">Street Address <span class="text-[#A0717F]">*</span></label>
                                <input type="text" name="address" value="{{ old('address') }}" required
                                    class="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                                    placeholder="Rue de la Paix, 12">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-[#CFCBCA]">City <span class="text-[#A0717F]">*</span></label>
                                <input type="text" name="city" value="{{ old('city') }}" required
                                    class="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                                    placeholder="Paris">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-[#CFCBCA]">Country <span class="text-[#A0717F]">*</span></label>
                                <input type="text" name="country" value="{{ old('country') }}" required
                                    class="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                                    placeholder="France">
                            </div>
                        </div>
                    </section>

                    {{-- Form Actions --}}
                    <div class="pt-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('owner.dashboard') }}"
                            class="flex-1 order-2 sm:order-1 border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-bold uppercase tracking-widest text-[10px] sm:text-xs px-8 py-5 rounded-xl text-center transition-all">
                            Abandon Request
                        </a>
                        <button type="submit"
                            class="flex-[2] order-1 sm:order-2 bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-widest text-[10px] sm:text-xs px-8 py-5 rounded-xl shadow-xl transition-all flex items-center justify-center gap-3">
                            <x-icon name="checkmark" size="sm" />
                            Submit Proposal
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection




