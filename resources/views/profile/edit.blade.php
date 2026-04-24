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
                <h2 class="text-3xl font-bold text-[#EAD3CD]">Edit Profile</h2>
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

            <!-- Edit Form -->
            <form action="{{ route('profile.update') }}" method="POST"
                class="rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg transition"
                style="background-color: #383537;">
                @csrf
                @method('PATCH')

                <!-- Full Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-[#CFCBCA] mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('name') border-[#ff8e8b] @enderror"
                        style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;" required>
                    @error('name')
                        <p class="text-[#ff8e8b] text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-[#CFCBCA] mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('email') border-[#ff8e8b] @enderror"
                        style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;" required>
                    @error('email')
                        <p class="text-[#ff8e8b] text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="mb-8">
                    <label for="phone" class="block text-sm font-semibold text-[#CFCBCA] mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition"
                        style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;">
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
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection




