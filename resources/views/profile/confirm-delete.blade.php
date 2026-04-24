@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div style="background-color: transparent; min-height: 100vh;">
        <div class="max-w-3xl mx-auto px-4 py-12">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('profile.show') }}"
                    class="text-[#A0717F] hover:text-[#EAD3CD] text-sm font-semibold mb-4 inline-block">
                    ← Back to Profile
                </a>
                <h2 class="text-3xl font-bold text-[#EAD3CD]">Delete Account</h2>
                <p class="text-[#CFCBCA] mt-2">Permanently delete your account and all associated data</p>
            </div>

            <!-- Warning Card -->
            <div
                class="bg-[#383537] rounded-2xl shadow-sm p-8 mb-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
                <div class="border-l-4 border-[#ff8e8b] pl-4 py-2" style="background-color: rgba(255, 142, 139, 0.1);">
                    <h3 class="font-bold text-[#ff8e8b] mb-2">⚠️ Warning</h3>
                    <p class="text-[#CFCBCA] text-sm mb-4">
                        Deleting your account is permanent and cannot be undone. This action will:
                    </p>
                    <ul class="list-disc list-inside text-[#CFCBCA] text-sm space-y-2">
                        <li>Remove your profile and personal information</li>
                        <li>Cancel any active bookings</li>
                        <li>Remove you from all associated hotels and roles</li>
                        <li>Delete all your reviews and preferences</li>
                    </ul>
                </div>
            </div>

            <!-- Confirmation Form -->
            <div
                class="bg-[#383537] rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
                <h3 class="text-lg font-bold text-[#EAD3CD] mb-6">Confirm Account Deletion</h3>

                @if ($errors->any())
                    <div class="border rounded-lg p-4 mb-6"
                        style="background-color: rgba(255, 142, 139, 0.1); border-color: #4E3B46; color: #ff8e8b;">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.delete') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Password Confirmation -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-[#CFCBCA] mb-2">
                            Enter Your Password to Confirm
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#ff8e8b] focus:border-transparent outline-none transition @error('password') border-[#ff8e8b] @enderror"
                            placeholder="••••••••" required>
                        @error('password')
                            <p class="text-[#ff8e8b] text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation Checkbox -->
                    <div class="mb-8">
                        <label class="flex items-center">
                            <input type="checkbox" name="confirm" value="1" required class="w-4 h-4 rounded"
                                style="accent-color: #A0717F;">
                            <span class="ml-3 text-sm text-[#CFCBCA]">
                                I understand this action is permanent and cannot be reversed
                            </span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <a href="{{ route('profile.show') }}"
                            class="flex-1 border border-[#4E3B46] bg-[#2A2729] hover:bg-[#383537] text-[#CFCBCA] font-semibold px-6 py-3 rounded-lg transition text-center">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 text-white font-semibold px-6 py-3 rounded-lg transition"
                            style="background-color: #b04441;" onmouseover="this.style.backgroundColor='#853230'"
                            onmouseout="this.style.backgroundColor='#b04441'">
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-8 border rounded-lg p-4"
                style="background-color: #383537; border-color: #4E3B46;">
                <p class="text-sm text-[#CFCBCA]">
                    <strong>Need help?</strong> If you're having issues or want to request your data before deletion, please
                    contact our support team.
                </p>
            </div>
        </div>
    </div>
@endsection




