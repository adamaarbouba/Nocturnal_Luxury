@extends('layouts.app')

@section('content')
    {{-- Ambient Gradients --}}
    <div class="fixed top-0 right-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(160, 113, 127, 0.05); z-index: 0;"></div>
    <div class="fixed bottom-0 left-0 w-[400px] h-[400px] rounded-full blur-3xl pointer-events-none"
        style="background: rgba(234, 211, 205, 0.03); z-index: 0;"></div>

    <div class="container mx-auto px-4 py-8 max-w-5xl relative z-10">
        <x-breadcrumbs :links="[
            ['label' => 'Receptionist Dashboard', 'url' => route('receptionist.dashboard')],
            ['label' => 'Bookings', 'url' => route('receptionist.bookings.index')],
            ['label' => 'Folio #'.str_pad($booking->id, 5, '0', STR_PAD_LEFT), 'url' => route('receptionist.bookings.show', $booking)],
            ['label' => 'Settle Account', 'url' => '#']
        ]" />

        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <p class="text-xs font-medium uppercase mb-2" style="color: #A0717F; letter-spacing: 0.4em;">
                    Folio #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }} &mdash; {{ $hotel->name }}
                </p>
                <h1 class="text-3xl lg:text-5xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                    Settle Account
                </h1>
            </div>
            <a href="{{ route('receptionist.bookings.show', $booking) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300 shrink-0"
                style="color: #CFCBCA; border: 1px solid rgba(207, 203, 202, 0.2); letter-spacing: 0.1em;"
                onmouseover="this.style.backgroundColor='rgba(207, 203, 202, 0.05)';"
                onmouseout="this.style.backgroundColor='transparent';">
                <x-icon name="arrow-left" class="w-4 h-4" /> Return to Folio
            </a>
        </div>

        @if (session('error'))
            <div class="mb-8 p-4 rounded-xl" style="background-color: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2);">
                <p class="text-red-400 font-medium tracking-wide">✗ {{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Payment Form Area -->
            <div class="lg:col-span-3 space-y-8">
                
                <!-- Guest Brief -->
                <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        <span class="p-2 rounded-lg" style="background-color: #4E3B46;">
                            <x-icon name="user" class="w-5 h-5" style="color: #A0717F;" />
                        </span>
                        Guest Identifiers
                    </h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="col-span-2">
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Guest Name</p>
                            <p class="text-base font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">{{ $booking->user->name }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Email</p>
                            <p class="text-sm" style="color: #CFCBCA;">{{ $booking->user->email }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Arrival</p>
                            <p class="text-sm font-semibold" style="color: #EAD3CD;">{{ $booking->check_in_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Departure</p>
                            <p class="text-sm font-semibold" style="color: #EAD3CD;">{{ $booking->check_out_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Transaction Capture -->
                <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                    <h2 class="text-xl font-bold mb-8 flex items-center gap-3" style="color: #EAD3CD; font-family: 'Georgia', serif;">
                        <span class="p-2 rounded-lg" style="background-color: #4E3B46;">
                            <x-icon name="checkmark" class="w-5 h-5" style="color: #A0717F;" />
                        </span>
                        Transaction Capture
                    </h2>
                    
                    <form method="POST" action="{{ route('receptionist.payments.record', $booking) }}" class="space-y-8">
                        @csrf

                        <!-- Payment Type Selection -->
                        <div class="p-5 rounded-xl border border-[rgba(160,113,127,0.15)]" style="background-color: #2E2530;">
                            <label class="block text-xs font-semibold uppercase mb-4" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Mode of Transaction</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center cursor-pointer p-3 rounded-lg transition-colors border"
                                       style="background-color: #383537; border-color: rgba(160,113,127,0.3);"
                                       onmouseover="this.style.borderColor='#A0717F'; this.style.backgroundColor='#4E3B46';">
                                    <input type="radio" name="type" value="payment" checked 
                                           class="w-4 h-4 rounded-full" style="accent-color: #A0717F;"
                                           onchange="if(this.checked){ this.closest('.grid').querySelectorAll('label').forEach(el=>el.style.borderColor='rgba(160,113,127,0.3)'); this.closest('label').style.borderColor='#A0717F'; }">
                                    <span class="ml-3 text-sm font-bold uppercase tracking-wider" style="color: #EAD3CD;">Record Payment</span>
                                </label>
                                <label class="flex items-center cursor-pointer p-3 rounded-lg transition-colors border"
                                       style="background-color: #383537; border-color: rgba(160,113,127,0.3);"
                                       onmouseover="this.style.borderColor='#A0717F'; this.style.backgroundColor='#4E3B46';">
                                    <input type="radio" name="type" value="refund" 
                                           class="w-4 h-4 rounded-full" style="accent-color: #A0717F;"
                                           onchange="if(this.checked){ this.closest('.grid').querySelectorAll('label').forEach(el=>el.style.borderColor='rgba(160,113,127,0.3)'); this.closest('label').style.borderColor='#A0717F'; }">
                                    <span class="ml-3 text-sm font-bold uppercase tracking-wider" style="color: #CFCBCA;">Issue Refund</span>
                                </label>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <label class="block text-xs font-semibold uppercase relative top-1" style="color: #A0717F; letter-spacing: 0.15em;">Transaction Amount</label>
                                <span class="text-xs font-medium" style="color: rgba(207, 203, 202, 0.4);">
                                    {{ $remainingBalance > 0 ? '(Max Allowed: $' . number_format($remainingBalance, 2) . ')' : '(Account is fully paid)' }}
                                </span>
                            </div>
                            <div class="relative flex items-center p-2 rounded-lg border transition-colors duration-300"
                                style="background-color: #2E2530; border-color: rgba(160, 113, 127, 0.2);"
                                onfocusin="this.style.borderColor='#A0717F';" onfocusout="this.style.borderColor='rgba(160, 113, 127, 0.2)';">
                                <span class="px-4 text-xl font-bold" style="color: #A0717F; font-family: 'Georgia', serif;">$</span>
                                <input type="number" name="amount" step="0.01" min="0" placeholder="0.00"
                                    {{ $remainingBalance <= 0 ? 'disabled' : '' }} value="{{ old('amount', $remainingBalance > 0 ? $remainingBalance : '') }}"
                                    class="w-full bg-transparent text-3xl font-bold placeholder-gray-500 focus:outline-none"
                                    style="color: #EAD3CD; font-family: 'Georgia', serif;" required>
                            </div>
                            @error('amount')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-xs font-semibold uppercase mb-2" style="color: rgba(207, 203, 202, 0.6); letter-spacing: 0.15em;">Internal Memo / Remarks</label>
                            <textarea name="notes" maxlength="500" rows="2"
                                placeholder="e.g. Card ending in 4242 processed..."
                                class="w-full px-5 py-4 rounded-xl focus:outline-none transition-all duration-300"
                                style="background-color: #2E2530; color: #EAD3CD; border: 1px solid rgba(160, 113, 127, 0.2); resize: none;"
                                onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-6" style="border-top: 1px solid rgba(160, 113, 127, 0.1);">
                            <button type="submit" {{ $remainingBalance <= 0 ? 'disabled' : '' }}
                                class="w-full py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 {{ $remainingBalance <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                style="background-color: #A0717F; color: #FFFFFF; letter-spacing: 0.12em; box-shadow: 0 4px 15px rgba(160, 113, 127, 0.3);"
                                @if($remainingBalance > 0)
                                onmouseover="this.style.backgroundColor='#b58290'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.backgroundColor='#A0717F'; this.style.transform='translateY(0)';"
                                @endif>
                                Finalize Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Financial Sidebar -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Ledger Summary -->
                <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                    <h3 class="text-xl font-bold mb-6" style="color: #EAD3CD; font-family: 'Georgia', serif;">Ledger Overview</h3>

                    <div class="space-y-4">
                        <div class="rounded-xl p-5" style="background-color: #2E2530; border: 1px solid rgba(160, 113, 127, 0.2);">
                            <p class="text-xs font-medium uppercase mb-1" style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.15em;">Total Billing Target</p>
                            <p class="text-3xl font-bold" style="color: #EAD3CD; font-family: 'Georgia', serif;">${{ number_format($booking->total_amount, 2) }}</p>
                        </div>

                        <div class="rounded-xl p-5" style="background-color: #2E2530; border: 1px solid rgba(160, 113, 127, 0.05);">
                            <div class="flex justify-between mb-3">
                                <span class="text-sm font-semibold uppercase tracking-wider" style="color: #CFCBCA;">Collected</span>
                                <span class="font-bold" style="color: #4ADE80;">${{ number_format($amountPaid, 2) }}</span>
                            </div>
                            @if ($amountRefunded > 0)
                                <div class="flex justify-between mb-3">
                                    <span class="text-sm font-semibold uppercase tracking-wider" style="color: #CFCBCA;">Refunded</span>
                                    <span class="font-bold" style="color: #FACC15;">-${{ number_format($amountRefunded, 2) }}</span>
                                </div>
                            @endif
                            @if ($amountNetPaid > 0)
                                <div class="flex justify-between pt-3 border-t border-[rgba(234,211,205,0.05)]">
                                    <span class="text-sm font-semibold uppercase tracking-wider" style="color: #A0717F;">Net Realized</span>
                                    <span class="font-bold" style="color: #A0717F;">${{ number_format($amountNetPaid, 2) }}</span>
                                </div>
                            @endif
                        </div>

                        @if ($remainingBalance > 0)
                            <div class="rounded-xl p-5" style="background-color: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2);">
                                <p class="text-xs font-bold uppercase mb-1" style="color: #F87171; letter-spacing: 0.15em;">Outstanding Balance</p>
                                <p class="text-3xl font-bold" style="color: #F87171; font-family: 'Georgia', serif;">${{ number_format($remainingBalance, 2) }}</p>
                            </div>
                        @else
                            <div class="rounded-xl p-5" style="background-color: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2);">
                                <p class="text-xs font-bold uppercase mb-1" style="color: #4ADE80; letter-spacing: 0.15em;">Account Status</p>
                                <p class="text-3xl font-bold" style="color: #4ADE80; font-family: 'Georgia', serif;">Settled</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Transaction Log -->
                <div class="rounded-2xl shadow-xl p-8" style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);">
                    <h3 class="text-xl font-bold mb-6" style="color: #EAD3CD; font-family: 'Georgia', serif;">Transaction Log</h3>

                    @if ($booking->payments->count() > 0)
                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach ($booking->payments as $payment)
                                <div class="rounded-xl p-4 border"
                                    style="background-color: #2E2530; border-color: {{ $payment->type === 'payment' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)' }};">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="font-bold text-sm uppercase tracking-wider" style="color: {{ $payment->type === 'payment' ? '#4ADE80' : '#F87171' }};">
                                                {{ $payment->type === 'payment' ? 'Payment' : 'Refund' }}
                                            </p>
                                            <p class="text-xs mt-1" style="color: rgba(207, 203, 202, 0.5);">
                                                {{ $payment->payment_date->format('M d, Y h:i A') }}
                                            </p>
                                        </div>
                                        <span class="text-lg font-bold" style="color: {{ $payment->type === 'payment' ? '#4ADE80' : '#F87171' }};">
                                            {{ $payment->type === 'payment' ? '+' : '-' }}${{ number_format($payment->amount, 2) }}
                                        </span>
                                    </div>

                                    @if ($payment->notes)
                                        <p class="text-sm italic mb-4" style="color: #CFCBCA;">"{{ $payment->notes }}"</p>
                                    @endif

                                    <div class="flex justify-between items-center pt-3 border-t border-[rgba(234,211,205,0.05)]">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="user" class="w-3 h-3 text-gray-500" />
                                            <span class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(207, 203, 202, 0.5);">{{ $payment->processedBy->name }}</span>
                                        </div>
                                        
                                        @if ($payment->processed_by === Auth::id())
                                            <form method="POST" action="{{ route('receptionist.payments.delete', [$booking, $payment]) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="NocturnalUI.confirm('Revoke this transaction logging?', 'Revoke Transaction').then(c => { if(c) this.closest('form').submit(); })"
                                                    class="text-xs uppercase font-bold transition-colors"
                                                    style="color: #F87171; letter-spacing: 0.1em;"
                                                    onmouseover="this.style.color='#EF4444';"
                                                    onmouseout="this.style.color='#F87171';">
                                                    Revoke
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl p-8 text-center" style="background-color: #2E2530; border: 1px solid rgba(160, 113, 127, 0.1);">
                            <p class="text-sm italic" style="color: rgba(207, 203, 202, 0.4);">No financial operations have been securely logged.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
