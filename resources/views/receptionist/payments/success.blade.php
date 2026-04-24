@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-20 min-h-[80vh] flex items-center relative z-10">
        {{-- Ambient Glow --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
            style="background: rgba(34, 197, 94, 0.05); z-index: 0;"></div>

        <div class="max-w-xl mx-auto text-center relative z-10 w-full">
            <!-- Icon -->
            <div class="mb-10 flex justify-center">
                <div class="inline-flex items-center justify-center p-6 rounded-full" 
                     style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3);">
                    <x-icon name="checkmark" class="w-16 h-16" style="color: #4ADE80;" />
                </div>
            </div>

            <!-- Message -->
            <h1 class="text-4xl font-bold mb-4" style="color: #EAD3CD; font-family: 'Georgia', serif;">Transaction Finalized</h1>
            <p class="text-lg mb-12" style="color: #CFCBCA;">The payment has been accurately recorded into the ledger.</p>

            <!-- Formal Receipt Card -->
            <div class="rounded-2xl shadow-2xl p-8 mb-10 text-left" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                <div class="flex items-center justify-between pb-6 mb-6" style="border-bottom: 1px solid rgba(234, 211, 205, 0.05);">
                    <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(207, 203, 202, 0.5);">Folio Reference</p>
                    <p class="text-lg font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                
                <div class="flex items-center justify-between pb-6 mb-6" style="border-bottom: 1px solid rgba(234, 211, 205, 0.05);">
                    <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(207, 203, 202, 0.5);">Transacted Amount</p>
                    <p class="text-3xl font-bold" style="color: #A0717F;">${{ number_format($payment->amount, 2) }}</p>
                </div>
                
                <div class="flex items-center justify-between pb-6 mb-6" style="border-bottom: 1px solid rgba(234, 211, 205, 0.05);">
                    <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(207, 203, 202, 0.5);">Processed By</p>
                    <p class="text-sm font-medium uppercase tracking-wider" style="color: #CFCBCA;">{{ $payment->processedBy->name }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(207, 203, 202, 0.5);">Timestamp</p>
                    <p class="text-sm italic" style="color: rgba(207, 203, 202, 0.6);">{{ $payment->payment_date->format('F d, Y — h:i A') }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('receptionist.bookings.show', $booking) }}"
                    class="inline-block px-10 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300"
                    style="background-color: #A0717F; color: #FFFFFF; letter-spacing: 0.12em; box-shadow: 0 4px 15px rgba(160, 113, 127, 0.3);"
                    onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                    onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';">
                    Return to Folio
                </a>
                <a href="{{ route('receptionist.dashboard') }}"
                    class="inline-block px-10 py-4 rounded-full text-sm font-semibold uppercase transition-all duration-300"
                    style="background-color: transparent; border: 1px solid rgba(207, 203, 202, 0.2); color: #CFCBCA; letter-spacing: 0.12em;"
                    onmouseover="this.style.backgroundColor='rgba(207, 203, 202, 0.05)';"
                    onmouseout="this.style.backgroundColor='transparent';">
                    Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
