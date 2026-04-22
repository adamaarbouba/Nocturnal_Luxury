@extends('layouts.app')

@section('content')
    <div style="background-color: transparent; min-height: 100vh;" class="flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-[#EAD3CD]">Hotel Management</h1>
                <p class="text-[#A0717F] mt-2">Create your account</p>
            </div>

            <!-- Register Card -->
            <div
                class="bg-[#383537] rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
                @if ($errors->any())
                    <div class="mb-6 border rounded-lg px-4 py-3"
                        style="background-color: rgba(211, 199, 173, 0.05); border-color: #4E3B46; color: #ff8e8b;">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                    @csrf

                    <!-- User Type Selection (Premium Grid) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#CFCBCA] mb-4">
                            Select Your Purpose <span style="color: #A0717F;">*</span>
                        </label>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                            {{-- Role: Guest --}}
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="user_type" value="guest" class="peer sr-only" {{ old('user_type') === 'guest' ? 'checked' : '' }} required>
                                <div class="px-2 py-3 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-[#A0717F] peer-checked:bg-[#A0717F]/10 transition-all text-center">
                                    <div class="flex justify-center mb-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <x-icon name="user" size="sm" class="text-[#EAD3CD]" />
                                    </div>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-[#EAD3CD]">Guest</span>
                                </div>
                            </label>

                            {{-- Role: Owner --}}
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="user_type" value="owner" class="peer sr-only" {{ old('user_type') === 'owner' ? 'checked' : '' }} required>
                                <div class="px-2 py-3 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-[#A0717F] peer-checked:bg-[#A0717F]/10 transition-all text-center">
                                    <div class="flex justify-center mb-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <x-icon name="building" size="sm" class="text-[#EAD3CD]" />
                                    </div>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-[#EAD3CD]">Owner</span>
                                </div>
                            </label>

                            {{-- Role: Receptionist --}}
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="user_type" value="receptionist" class="peer sr-only" {{ old('user_type') === 'receptionist' ? 'checked' : '' }} required>
                                <div class="px-2 py-3 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-[#A0717F] peer-checked:bg-[#A0717F]/10 transition-all text-center">
                                    <div class="flex justify-center mb-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <x-icon name="phone" size="sm" class="text-[#EAD3CD]" />
                                    </div>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-[#EAD3CD]">Front Desk</span>
                                </div>
                            </label>

                             {{-- Role: Cleaner --}}
                             <label class="relative cursor-pointer group">
                                <input type="radio" name="user_type" value="cleaner" class="peer sr-only" {{ old('user_type') === 'cleaner' ? 'checked' : '' }} required>
                                <div class="px-2 py-3 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-[#A0717F] peer-checked:bg-[#A0717F]/10 transition-all text-center">
                                    <div class="flex justify-center mb-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <x-icon name="sparkles" size="sm" class="text-[#EAD3CD]" />
                                    </div>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-[#EAD3CD]">Cleaner</span>
                                </div>
                            </label>

                             {{-- Role: Inspector --}}
                             <label class="relative cursor-pointer group">
                                <input type="radio" name="user_type" value="inspector" class="peer sr-only" {{ old('user_type') === 'inspector' ? 'checked' : '' }} required>
                                <div class="px-2 py-3 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-[#A0717F] peer-checked:bg-[#A0717F]/10 transition-all text-center">
                                    <div class="flex justify-center mb-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                        <x-icon name="clipboard-check" size="sm" class="text-[#EAD3CD]" />
                                    </div>
                                    <span class="block text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-[#EAD3CD]">Inspector</span>
                                </div>
                            </label>
                        </div>
                        @error('user_type')
                            <p class="text-[#C89AB3] text-[10px] mt-2 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#CFCBCA] mb-2">
                            Full Name <span style="color: #A0717F;">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#A0717F] focus:border-transparent outline-none transition"
                            placeholder="John Doe" required>
                        @error('name')
                            <p class="text-[#C89AB3] text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#CFCBCA] mb-2">
                            Email Address <span style="color: #A0717F;">*</span>
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
                            Password <span style="color: #A0717F;">*</span>
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#A0717F] focus:border-transparent outline-none transition"
                            placeholder="••••••••" required>
                        <p class="text-[#A0717F] text-xs mt-1">Minimum 8 characters</p>
                        @error('password')
                            <p class="text-[#C89AB3] text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-[#CFCBCA] mb-2">
                            Confirm Password <span style="color: #A0717F;">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#A0717F] focus:border-transparent outline-none transition"
                            placeholder="••••••••" required>
                        @error('password_confirmation')
                            <p class="text-[#C89AB3] text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Register Button -->
                    <button type="submit"
                        class="w-full text-white font-semibold py-2 px-4 rounded-lg transition duration-200 mt-6"
                        style="background-color: #A0717F;" onmouseover="this.style.backgroundColor='#8F6470'"
                        onmouseout="this.style.backgroundColor='#A0717F'">
                        Create Account
                    </button>
                </form>

                <!-- Login Link -->
                <p class="text-center text-[#CFCBCA] text-sm mt-6">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold transition" style="color: #EAD3CD;"
                        onmouseover="this.style.color='#CFCBCA'" onmouseout="this.style.color='#EAD3CD'">
                        Sign in here
                    </a>
                </p>
            </div>

            <!-- Footer -->
            <p class="text-center text-[#CFCBCA] text-xs mt-6">
                © 2026 Hotel Management System. All rights reserved.
            </p>
        </div>
    </div>
@endsection




