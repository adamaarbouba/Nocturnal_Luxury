@extends('layouts.app')

@section('content')
    <div style="background-color: transparent; min-height: 100vh;" class="flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-[#EAD3CD]">Hotel Management</h1>
                <p class="text-[#A0717F] mt-2">Sign in to your account</p>
            </div>

            <!-- Login Card -->
            <div
                class=" bg-[#383537] rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
                @if ($errors->any())
                    <div class="mb-6 border rounded-lg px-4 py-3"
                        style="background-color: rgba(211, 199, 173, 0.05); border-color: #4E3B46; color: #ff8e8b;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 border rounded-lg px-4 py-3"
                        style="background-color: rgba(211, 199, 173, 0.1); border-color: #4E3B46; color: #A0717F;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#CFCBCA] mb-2">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#A0717F] focus:border-transparent outline-none transition"
                            placeholder="you@example.com" required>
                        @error('email')
                            <p class="text-[#C89AB3] text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-[#CFCBCA] mb-2">
                            Password
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#A0717F] focus:border-transparent outline-none transition"
                            placeholder="••••••••" required>
                        @error('password')
                            <p class="text-[#C89AB3] text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded"
                            style="accent-color: #A0717F;">
                        <label for="remember" class="ml-2 block text-sm text-[#CFCBCA]">
                            Remember me
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit"
                        class="w-full text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                        style="background-color: #A0717F;" onmouseover="this.style.backgroundColor='#8F6470'"
                        onmouseout="this.style.backgroundColor='#A0717F'">
                        Sign In
                    </button>
                </form>

                <!-- Register Link -->
                <p class="text-center text-[#CFCBCA] text-sm mt-6">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold transition" style="color: #EAD3CD;"
                        onmouseover="this.style.color='#CFCBCA'" onmouseout="this.style.color='#EAD3CD'">
                        Register here
                    </a>
                </p>
            </div>
            <div class="text-[#CFCBCA] text-[10px] mt-8 text-center p-4 bg-[#2A2729] rounded-xl border border-[#4E3B46]/50">
                <p class="mb-2 font-bold uppercase tracking-widest text-[#A0717F]">Quick Access (Test Mode)</p>
                <div class="space-y-1 opacity-70">
                    <p>Owner: <span class="text-[#EAD3CD]">owner@test.com</span></p>
                    <p>Receptionist: <span class="text-[#EAD3CD]">receptionist@test.com</span></p>
                    <p>Cleaner: <span class="text-[#EAD3CD]">cleaner@test.com</span></p>
                    <p>Inspector: <span class="text-[#EAD3CD]">inspector@test.com</span></p>
                    <p>Guest: <span class="text-[#EAD3CD]">guest@test.com</span></p>
                    <p class="mt-2 pt-2 border-t border-[#4E3B46]/30">Password: <span class="text-[#EAD3CD]">password123</span></p>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-[#CFCBCA] text-xs mt-6">
                © 2026 Hotel Management System. All rights reserved.
            </p>
        </div>
    </div>
@endsection




