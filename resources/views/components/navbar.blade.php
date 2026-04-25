<header class="sticky top-0 z-40" style="background-color: #383537; border-bottom: 1px solid rgba(234, 211, 205, 0.1);">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 flex items-center justify-between" style="height: 72px;">
        <a href="{{ route('welcome') }}" class="transition-colors duration-300"
            style="color: #EAD3CD; font-family: 'Georgia', serif; font-size: 1.25rem; font-weight: 700; letter-spacing: 0.2em;"
            onmouseover="this.style.color='#A0717F';" onmouseout="this.style.color='#EAD3CD';">
            NOCTURNAL LUXURY
        </a>

        <div class="hidden md:flex items-center gap-8">
            @guest
                <a href="{{ route('login') }}"
                    class="text-xs font-medium uppercase transition-colors duration-300 relative group"
                    style="color: #CFCBCA; letter-spacing: 0.15em;">
                    Login
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full"
                        style="background-color: #A0717F;"></span>
                </a>
                <a href="{{ route('register') }}"
                    class="text-xs font-semibold uppercase rounded-md px-5 py-2.5 text-white transition-all duration-300 ml-2"
                    style="background-color: #A0717F; letter-spacing: 0.12em;"
                    onmouseover="this.style.backgroundColor='#b58290'; this.style.boxShadow='0 8px 25px rgba(160,113,127,0.25)';"
                    onmouseout="this.style.backgroundColor='#A0717F'; this.style.boxShadow='none';">
                    Book Now
                </a>
            @else
                <div class="relative group">
                    <button class="flex items-center gap-2 text-xs font-medium uppercase transition-colors duration-300"
                        style="color: #CFCBCA; letter-spacing: 0.15em;">
                        <x-icon name="user" size="sm" hoverable="true" style="color: currentColor;" />
                        Account
                        <x-icon name="chevron-down" size="sm" hoverable="true"
                            class="transition group-hover:rotate-180" style="color: currentColor;" />
                    </button>

                    <div class="absolute right-0 mt-3 w-52 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden"
                        style="background-color: #4E3B46; border: 1px solid rgba(234, 211, 205, 0.1); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">

                        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(234, 211, 205, 0.08);">
                            <p class="text-sm font-semibold truncate" style="color: #EAD3CD;">{{ Auth::user()->name }}</p>
                            <p class="text-xs truncate mt-0.5" style="color: rgba(207, 203, 202, 0.5);">
                                {{ Auth::user()->role->title ?? 'User' }}</p>
                        </div>

                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm transition-all duration-200"
                            style="color: #CFCBCA;"
                            onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.1)'; this.style.color='#EAD3CD';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#CFCBCA';">
                            <x-icon name="user" size="sm" hoverable="true" style="color: #A0717F;" />
                            Profile
                        </a>

                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm transition-all duration-200"
                            style="color: #CFCBCA;"
                            onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.1)'; this.style.color='#EAD3CD';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#CFCBCA';">
                            <x-icon name="home" size="sm" hoverable="true" style="color: #A0717F;" />
                            Dashboard
                        </a>

                        @if(Auth::user()->role && in_array(Auth::user()->role->slug, ['cleaner', 'inspector', 'receptionist', 'staff']))
                            <a href="{{ route('staff.hotels.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm transition-all duration-200"
                                style="color: #CFCBCA;"
                                onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.1)'; this.style.color='#EAD3CD';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#CFCBCA';">
                                <x-icon name="building" size="sm" hoverable="true" style="color: #A0717F;" />
                                Browse Jobs
                            </a>
                            <a href="{{ route('staff.my-applications') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm transition-all duration-200"
                                style="color: #CFCBCA;"
                                onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.1)'; this.style.color='#EAD3CD';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#CFCBCA';">
                                <x-icon name="file" size="sm" hoverable="true" style="color: #A0717F;" />
                                My Applications
                            </a>
                        @endif

                        <a href="{{ route('guest.bookings.index') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm transition-all duration-200"
                            style="color: #CFCBCA;"
                            onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.1)'; this.style.color='#EAD3CD';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#CFCBCA';">
                            <x-icon name="calendar" size="sm" hoverable="true" style="color: #A0717F;" />
                            My Bookings
                        </a>

                        <a href="{{ route('guest.reviews.index') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm transition-all duration-200"
                            style="color: #CFCBCA;"
                            onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.1)'; this.style.color='#EAD3CD';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#CFCBCA';">
                            <x-icon name="star" size="sm" hoverable="true" style="color: #A0717F;" />
                            My Reviews
                        </a>

                        <div style="border-top: 1px solid rgba(234, 211, 205, 0.08);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 text-left px-4 py-3 text-sm transition-all duration-200 font-medium"
                                    style="color: #A0717F;"
                                    onmouseover="this.style.backgroundColor='rgba(160, 113, 127, 0.1)';"
                                    onmouseout="this.style.backgroundColor='transparent';">
                                    <x-icon name="logout" size="sm" hoverable="true" style="color: #A0717F;" />
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest
        </div>

        {{-- Mobile Menu Button --}}
        <button class="md:hidden p-2 transition-colors" style="color: #EAD3CD;"
            onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </nav>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="hidden md:hidden px-4 pb-6 space-y-4"
        style="border-top: 1px solid rgba(234, 211, 205, 0.08);">
        <a href="{{ route('guest.hotels.index') }}" class="block py-2 text-sm uppercase"
            style="color: #CFCBCA; letter-spacing: 0.15em;">Suites</a>
        <a href="#" class="block py-2 text-sm uppercase"
            style="color: #CFCBCA; letter-spacing: 0.15em;">Dining</a>
        <a href="#" class="block py-2 text-sm uppercase"
            style="color: #CFCBCA; letter-spacing: 0.15em;">Wellness</a>
        <a href="#" class="block py-2 text-sm uppercase"
            style="color: #CFCBCA; letter-spacing: 0.15em;">Concierge</a>
        @guest
            <a href="{{ route('login') }}" class="block py-2 text-sm uppercase"
                style="color: #CFCBCA; letter-spacing: 0.15em;">Login</a>
            <a href="{{ route('register') }}"
                class="inline-block text-white text-xs font-semibold uppercase rounded-md px-5 py-2.5 mt-2"
                style="background-color: #A0717F; letter-spacing: 0.12em;">Book Now</a>
        @else
            <a href="{{ route('dashboard') }}" class="block py-2 text-sm uppercase"
                style="color: #CFCBCA; letter-spacing: 0.15em;">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block py-2 text-sm uppercase font-medium"
                    style="color: #A0717F; letter-spacing: 0.15em;">Logout</button>
            </form>
        @endguest
    </div>
</header>
