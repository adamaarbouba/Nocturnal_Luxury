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
                <h2 class="text-3xl font-bold text-[#EAD3CD]">Change Password</h2>
            </div>

            @if ($errors->any())
                <div class="border rounded-lg p-4 mb-8"
                    style="background-color: rgba(255, 142, 139, 0.1); border-color: #4E3B46; color: #ff8e8b;">
                    <h3 class="font-semibold mb-3">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="border rounded-lg p-4 mb-8"
                    style="background-color: rgba(234, 211, 205, 0.1); border-color: #4E3B46; color: #A0717F;">
                    <p class="font-semibold">✓ {{ session('success') }}</p>
                </div>
            @endif

            <!-- Change Password Form -->
            <form action="{{ route('profile.updatePassword') }}" method="POST"
                class="rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg transition"
                style="background-color: #383537;">
                @csrf
                @method('POST')

                <!-- Current Password -->
                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-semibold text-[#CFCBCA] mb-2">Current
                        Password</label>
                    <input type="password" name="current_password" id="current_password"
                        class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('current_password') border-[#ff8e8b] @enderror"
                        style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;" required>
                    @error('current_password')
                        <p class="text-[#ff8e8b] text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-[#CFCBCA] mb-2">New Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('password') border-[#ff8e8b] @enderror"
                        style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;" required>
                    @error('password')
                        <p class="text-[#ff8e8b] text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-semibold text-[#CFCBCA] mb-2">Confirm
                        Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition"
                        style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;" required>
                    @error('password_confirmation')
                        <p class="text-[#ff8e8b] text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6" style="border-top: 1px solid #4E3B46;">
                    <a href="{{ route('profile.show') }}" class="flex-1 text-center px-6 py-3 rounded-lg transition"
                        style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 text-white font-semibold px-6 py-3 rounded-lg transition"
                        style="background-color: #A0717F;" onmouseover="this.style.backgroundColor='#8F6470'"
                        onmouseout="this.style.backgroundColor='#A0717F'">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection




