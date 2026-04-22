<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hotel Management') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased bg-[#4E3B46] text-[#CFCBCA]">
    <div class="flex h-screen overflow-hidden ">
        <!-- Sidebar (Fixed) -->
        <aside class="w-64 flex-shrink-0 overflow-y-auto fixed left-0 top-0 h-screen z-50 bg-[#383537]">
            <!-- Logo/Branding -->
            <div class="px-6 py-8 border-b border-[#4E3B46]">
                <h1 class="text-2xl font-bold text-[#EAD3CD]">{{ config('app.name', 'Hotel') }}</h1>
                <p class="text-sm mt-1 text-[#A0717F]">Management System</p>
            </div>

            <!-- Navigation Items -->
            <nav class="px-4 py-6 space-y-2">
                @isset($sidebarItems)
                    @foreach ($sidebarItems as $item)
                        @if (isset($item['divider']) && $item['divider'])
                            <div class="my-4 border-t border-[#4E3B46]"></div>
                        @else
                            @php
                                $activeRoute = $item['active_route'] ?? $item['route'];
                                $isActive = request()->routeIs($activeRoute);
                            @endphp
                            <a href="{{ $item['route'] }}"
                                class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200
                                       {{ $isActive ? 'text-[#EAD3CD] font-semibold bg-[#A0717F]' : 'text-[#CFCBCA] hover:text-[#EAD3CD] hover:bg-[#2A2729]' }}">
                                <span class="mr-3 flex items-center justify-center">
                                    @if(isset($item['icon_name']))
                                        <x-icon :name="$item['icon_name']" size="md" />
                                    @else
                                        •
                                    @endif
                                </span>
                                <span>{{ $item['label'] }}</span>
                                @if (isset($item['badge']))
                                    <span class="ml-auto px-2 py-1 text-xs font-semibold rounded-full text-white bg-[#A0717F]">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                @endisset
            </nav>

            <!-- User Section -->
            <div class="fixed bottom-0 left-0 w-64 border-t border-[#4E3B46] p-4 bg-[#383537]">
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#2A2729]">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-[#EAD3CD] truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-[#A0717F]">{{ ucfirst(auth()->user()->role->name ?? 'User') }}</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="{{ $route_profile ?? route('profile.show') }}"
                        class="flex items-center justify-center w-full px-4 py-2 rounded-lg transition-colors duration-200 text-white bg-[#A0717F] hover:opacity-90">
                        <x-icon name="user" class="w-5 h-5 mr-2" /> Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit"
                            class="flex items-center justify-center w-full px-4 py-2 rounded-lg transition-colors duration-200 text-[#CFCBCA] border border-[#4E3B46] hover:bg-[#4E3B46]">
                            <x-icon name="logout" class="w-5 h-5 mr-2" /> Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto" style="margin-left: 16rem;">
            <!-- Top Header Bar -->
            <div class="border-b border-[#4E3B46] sticky top-0 z-10 bg-[#383537]">
                <div class="px-8 py-4 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-[#EAD3CD]">
                        {{ $pageTitle ?? 'Dashboard' }}
                    </h2>
                </div>
            </div>

            <!-- Flash Messages -->
            <div class="px-8 py-4">
                @if ($message = Session::get('success'))
                    <x-alert variant="success" dismissible="true" class="mb-4">
                        ✓ {{ $message }}
                    </x-alert>
                @endif

                @if ($message = Session::get('error'))
                    <x-alert variant="error" dismissible="true" class="mb-4">
                        ✗ {{ $message }}
                    </x-alert>
                @endif

                @if ($message = Session::get('info'))
                    <x-alert variant="info" dismissible="true" class="mb-4">
                        ℹ️ {{ $message }}
                    </x-alert>
                @endif
            </div>

            <!-- Page Content -->
            <div class="px-8 py-6">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>




