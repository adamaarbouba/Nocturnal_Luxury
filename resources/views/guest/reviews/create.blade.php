@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-12">
        {{-- Elegant Navigation --}}
        <div class="mb-12">
            <x-breadcrumbs :links="[
                ['label' => 'My Bookings', 'url' => route('guest.bookings.index')],
                ['label' => 'Curate Your Experience', 'url' => '#'],
            ]" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            {{-- Left Identity --}}
            <div class="lg:col-span-4 space-y-8 lg:sticky lg:top-8">
                <header class="space-y-4">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F]">Perspective</p>
                    <h1 class="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">Curate Your <br>Stay Experience</h1>
                    <p class="text-[#CFCBCA] leading-relaxed max-w-xs text-sm">
                        Refine the standards of luxury at <span class="text-[#EAD3CD] font-semibold underline decoration-[#A0717F]/30">{{ $booking->hotel->name }}</span> through your unique lens.
                    </p>
                </header>

                <div class="p-8 rounded-2xl bg-[#383537] border border-[#4E3B46] shadow-xl space-y-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#A0717F] mb-1">Reservation Details</p>
                        <h3 class="text-xl font-bold font-serif text-[#EAD3CD]">{{ $booking->hotel->name }}</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-6 border-t border-[#4E3B46]">
                        <div class="space-y-1">
                            <p class="text-[9px] uppercase tracking-widest text-[#CFCBCA]">Booking ID</p>
                            <p class="text-xs text-[#EAD3CD]">#{{ $booking->id }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] uppercase tracking-widest text-[#CFCBCA]">Period</p>
                            <p class="text-xs text-[#EAD3CD]">{{ $booking->check_in_date->format('M d') }} — {{ $booking->check_out_date->format('d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Interactive Review Suite --}}
            <div class="lg:col-span-8 space-y-12">
                <form action="{{ route('guest.reviews.store', $booking) }}" method="POST" class="space-y-16">
                    @csrf

                    {{-- Step 1: Overall Impression --}}
                    <section class="space-y-10">
                        <div class="flex items-center gap-4">
                            <span class="w-8 h-[1px] bg-[#A0717F]"></span>
                            <h2 class="text-xl font-bold font-serif text-[#EAD3CD]">Overall Impression</h2>
                        </div>

                        <div class="p-10 rounded-2xl bg-[#2A2729] border border-[#4E3B46] shadow-2xl transition-all duration-500 hover:border-[#A0717F]/30">
                            <div class="rating-v2 flex flex-col items-center gap-8" data-category="rating">
                                <div class="stars-preview flex gap-4">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-trigger group transform transition-all duration-300 hover:scale-110" data-value="{{ $i }}">
                                            <svg class="w-12 h-12 text-[#4E3B46] fill-transparent stroke-[1.5] transition-all duration-500 pointer-events-none" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <div class="experience-indicator text-2xl font-serif italic text-[#EAD3CD] opacity-40 transition-all duration-500">Select Rating</div>
                                <input type="hidden" name="rating" id="input-rating" value="{{ old('rating') }}" required>
                            </div>
                        </div>
                        @error('rating') <p class="text-xs text-red-400 italic mt-2">{{ $message }}</p> @enderror
                    </section>


                    {{-- Step 3: Detailed Feedback --}}
                    <section class="space-y-10">
                        <div class="flex items-center gap-4">
                            <span class="w-8 h-[1px] bg-[#A0717F]"></span>
                            <h2 class="text-xl font-bold font-serif text-[#EAD3CD]">Detailed Feedback</h2>
                        </div>

                        <div class="space-y-4">
                            <textarea name="comment" rows="8" 
                                class="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-2xl p-8 focus:outline-none focus:border-[#A0717F] transition-all duration-500 placeholder-[#CFCBCA]/20 text-lg leading-relaxed shadow-inner"
                                placeholder="Reflect on the nuances of your journey...">{{ old('comment') }}</textarea>
                            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/40">
                                <span id="char-counter">0 / 1000 characters</span>
                                @error('comment') <span class="text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    {{-- Actions --}}
                    <div class="pt-8 flex flex-col sm:flex-row items-center gap-6">
                        <button type="submit" 
                            class="w-full sm:w-auto bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.4em] text-xs px-16 py-5 rounded-xl shadow-xl transition-all duration-500 transform hover:-translate-y-1 active:scale-95">
                            Submit Review
                        </button>
                        <a href="{{ route('guest.bookings.index') }}" 
                            class="text-[10px] font-bold uppercase tracking-[0.4em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-all duration-500">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .star-filled {
            color: #A0717F !important;
            fill: #A0717F !important;
            filter: drop-shadow(0 0 10px rgba(160, 113, 127, 0.4));
            stroke-width: 0 !important;
        }
        .pillar-star-filled {
            color: #A0717F !important;
            fill: #A0717F !important;
            opacity: 1 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusLabels = {
                1: "Substandard",
                2: "Balanced",
                3: "Distinguished",
                4: "Curated",
                5: "Exquisite"
            };

            // Main Star Feedback
            const mainGroup = document.querySelector('[data-category="rating"]');
            const mainTriggers = mainGroup.querySelectorAll('.star-trigger');
            const mainInput = document.getElementById('input-rating');
            const mainIndicator = mainGroup.querySelector('.experience-indicator');

            function updateMainDisplay(val, permanent = false) {
                mainTriggers.forEach(btn => {
                    const btnVal = btn.dataset.value;
                    const svg = btn.querySelector('svg');
                    if (btnVal <= val) {
                        svg.classList.add('star-filled');
                    } else {
                        svg.classList.remove('star-filled');
                    }
                });
                
                if (val > 0) {
                    mainIndicator.textContent = statusLabels[val];
                    mainIndicator.style.opacity = '1';
                } else {
                    mainIndicator.textContent = "Select Rating";
                    mainIndicator.style.opacity = '0.4';
                }
            }

            mainTriggers.forEach(btn => {
                btn.addEventListener('mouseenter', () => updateMainDisplay(btn.dataset.value));
                btn.addEventListener('mouseleave', () => updateMainDisplay(mainInput.value));
                btn.addEventListener('click', () => {
                    mainInput.value = btn.dataset.value;
                    updateMainDisplay(mainInput.value);
                });
            });

            if (mainInput.value) updateMainDisplay(mainInput.value);


            // Character Counter
            const counter = document.getElementById('char-counter');
            const textarea = document.querySelector('textarea');
            textarea.addEventListener('input', () => {
                counter.textContent = `${textarea.value.length} / 1000 characters`;
            });
            counter.textContent = `${textarea.value.length} / 1000 characters`;
        });
    </script>
@endsection
