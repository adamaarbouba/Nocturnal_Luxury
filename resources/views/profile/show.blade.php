@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div style="background-color: transparent; min-height: 100vh;">
        <div class="max-w-3xl mx-auto px-4 py-12">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-[#EAD3CD]">Account Settings</h2>
                <a href="{{ route('dashboard') }}" class="text-white font-semibold px-4 py-2 rounded transition"
                    style="background-color: #A0717F;" onmouseover="this.style.backgroundColor='#8F6470'"
                    onmouseout="this.style.backgroundColor='#A0717F'">
                    ← Back to Dashboard
                </a>
            </div>

            @if (session('success'))
                <div class="border rounded-lg p-4 mb-8"
                    style="background-color: rgba(234, 211, 205, 0.1); border-color: #4E3B46; color: #A0717F;">
                    <p class="font-semibold">✓ {{ session('success') }}</p>
                </div>
            @endif

            <!-- Profile Card -->
            <div class="rounded-2xl shadow-sm p-8 mb-8 border border-transparent hover:shadow-lg transition"
                style="background-color: #383537; border-color: #4E3B46;">
                <!-- User Avatar Section -->
                <div class="flex items-center gap-6 mb-8 pb-8" style="border-bottom: 1px solid #4E3B46;">
                    <div class="w-24 h-24 flex items-center justify-center rounded-full text-white text-4xl font-bold"
                        style="background: linear-gradient(135deg, #A0717F, #4E3B46);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#EAD3CD]">{{ $user->name }}</h3>
                        <p class="text-[#CFCBCA]">{{ $user->email }}</p>
                        <p class="text-sm text-[#CFCBCA] mt-2" style="opacity: 0.8;">
                            <strong>{{ ucfirst($user->role->slug) }}</strong> Account • Member since
                            {{ $user->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <!-- Profile Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-[#CFCBCA] mb-2">Full Name</label>
                        <p class="text-lg p-3 rounded" style="background-color: #2A2729; color: #CFCBCA;">
                            {{ $user->name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-[#CFCBCA] mb-2">Email Address</label>
                        <p class="text-lg p-3 rounded" style="background-color: #2A2729; color: #CFCBCA;">
                            {{ $user->email }}</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-semibold text-[#CFCBCA] mb-2">Phone Number</label>
                        <p class="text-lg p-3 rounded" style="background-color: #2A2729; color: #CFCBCA;">
                            {{ $user->phone ?? '—' }}</p>
                    </div>

                    <!-- Member Since -->
                    <div>
                        <label class="block text-sm font-semibold text-[#CFCBCA] mb-2">Member Since</label>
                        <p class="text-lg p-3 rounded" style="background-color: #2A2729; color: #CFCBCA;">
                            {{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if (auth()->user()->role->slug === 'admin')
                    <div class="pt-6 flex gap-4" style="border-top: 1px solid #4E3B46;">
                        <a href="{{ route('profile.edit') }}"
                            class="inline-block text-white font-semibold px-6 py-3 rounded-lg transition"
                            style="background-color: #A0717F;" onmouseover="this.style.backgroundColor='#8F6470'"
                            onmouseout="this.style.backgroundColor='#A0717F'">
                            Edit Profile
                        </a>
                        <a href="{{ route('profile.change-password') }}"
                            class="inline-block text-white font-semibold px-6 py-3 rounded-lg transition"
                            style="background-color: #A0717F;" onmouseover="this.style.backgroundColor='#8F6470'"
                            onmouseout="this.style.backgroundColor='#A0717F'">
                            Change Password
                        </a>
                    </div>
                @endif
            </div>

            <!-- Account Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#383537] rounded-2xl shadow-sm p-6 text-center border border-transparent hover:shadow-lg transition">
                    <p class="text-[#CFCBCA] text-sm font-semibold mb-2">Total Bookings</p>
                    <p class="text-3xl font-bold text-[#EAD3CD]">
                        {{ $user->bookings()->count() }}
                    </p>
                </div>
                <div class="bg-[#383537] rounded-2xl shadow-sm p-6 text-center border border-transparent hover:shadow-lg transition">
                    <p class="text-[#CFCBCA] text-sm font-semibold mb-2">Reviews Written</p>
                    <p class="text-3xl font-bold text-[#EAD3CD]">
                        {{ $user->reviews()->count() }}
                    </p>
                </div>
                <div class="bg-[#383537] rounded-2xl shadow-sm p-6 text-center border border-transparent hover:shadow-lg transition">
                    <p class="text-[#CFCBCA] text-sm font-semibold mb-2">Account Status</p>
                    <p class="text-xl font-bold">
                        <span class="px-3 py-1 rounded-full text-sm text-[#A0717F]"
                            style="background-color: #2A2729;">Active</span>
                    </p>
                </div>
            </div>

            <!-- Account Actions -->
            @if (auth()->user()->role->slug !== 'admin')
                <div class="rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg transition"
                    style="background-color: #383537;">
                    <h3 class="text-lg font-bold text-[#EAD3CD] mb-6">Account Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('profile.edit') }}" class="block p-4 rounded-lg transition"
                            style="border: 1px solid #4E3B46; background-color: #2A2729;">
                            <h4 class="font-semibold text-[#A0717F]">Edit Profile</h4>
                            <p class="text-sm text-[#CFCBCA]" style="opacity: 0.8;">Update your name, email, and phone</p>
                        </a>
                        <a href="{{ route('profile.change-password') }}" class="block p-4 rounded-lg transition"
                            style="border: 1px solid #4E3B46; background-color: #2A2729;">
                            <h4 class="font-semibold text-[#A0717F]">Change Password</h4>
                            <p class="text-sm text-[#CFCBCA]" style="opacity: 0.8;">Update your password for security</p>
                        </a>
                        <button type="button" onclick="openDeleteModal()"
                            class="w-full text-left block p-4 rounded-lg transition"
                            style="border: 1px solid #4E3B46; background-color: #2A2729;">
                            <h4 class="font-semibold text-[#ff8e8b]">Delete Account</h4>
                            <p class="text-sm text-[#CFCBCA]" style="opacity: 0.8;">Permanently delete your account</p>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Delete Account Modal -->
        <div id="deleteModal" class="hidden fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="rounded-2xl shadow-lg p-8 max-w-sm w-full mx-4" onclick="event.stopPropagation()"
                style="background-color: #383537; border: 1px solid #4E3B46;">
                <h3 class="text-xl font-bold text-[#EAD3CD] mb-2">Delete Account</h3>
                <span class="text-sm font-semibold text-[#ff8e8b]">This action cannot be reverted</span>

                @if ($errors->any())
                    <div class="border rounded-lg p-4 mb-4 mt-4"
                        style="background-color: rgba(255, 142, 139, 0.1); border-color: #4E3B46; color: #ff8e8b;">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.delete') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="mb-6 mt-6">
                        <input type="password" name="password"
                            class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('password') border-[#ff8e8b] @enderror"
                            style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;"
                            placeholder="Enter password" required>
                        @error('password')
                            <p class="text-[#ff8e8b] text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 font-semibold px-4 py-2 rounded-lg transition"
                            style="border: 1px solid #4E3B46; background-color: #2A2729; color: #CFCBCA;">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 text-white font-semibold px-4 py-2 rounded-lg transition"
                            style="background-color: #b04441;" onmouseover="this.style.backgroundColor='#853230'"
                            onmouseout="this.style.backgroundColor='#b04441'">
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openDeleteModal() {
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Close modal on background click
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });
        </script>
    </div>
    </div>
@endsection




