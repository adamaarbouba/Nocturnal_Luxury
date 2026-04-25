
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hotel Management') }}</title>


    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#4E3B46] font-sans text-[#CFCBCA] antialiased">
    <x-navbar />


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
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


    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @yield('content')
        </div>
    </main>


    <div id="nocturnal-modal-overlay" class="fixed inset-0 bg-[#1A1515] bg-opacity-70 backdrop-blur-lg hidden z-[100] flex items-center justify-center transition-opacity duration-300 opacity-0 px-4">
        <div id="nocturnal-modal-box" class="bg-[#383537] border-t-[4px] border-[#A0717F] rounded-2xl shadow-[0_25px_50px_rgba(0,0,0,0.5)] p-8 max-w-lg w-full transform transition-all duration-300 scale-95 relative overflow-hidden">
            <!-- Subtle ambient glow -->
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-3xl" style="background: rgba(160, 113, 127, 0.15); pointer-events: none;"></div>
            
            <h3 id="nocturnal-modal-title" class="text-2xl font-bold font-serif text-[#EAD3CD] mb-3 relative z-10">Notice</h3>
            <p id="nocturnal-modal-message" class="text-sm text-[#CFCBCA] mb-8 leading-relaxed relative z-10 whitespace-pre-wrap">Message goes here...</p>
            <div id="nocturnal-modal-actions" class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 relative z-10">
                <!-- Buttons dynamically inserted here -->
            </div>
        </div>
    </div>

    <script>
        window.NocturnalUI = {
            _overlay: null, _box: null, _title: null, _message: null, _actions: null,

            _init() {
                if (!this._overlay) {
                    this._overlay = document.getElementById('nocturnal-modal-overlay');
                    this._box = document.getElementById('nocturnal-modal-box');
                    this._title = document.getElementById('nocturnal-modal-title');
                    this._message = document.getElementById('nocturnal-modal-message');
                    this._actions = document.getElementById('nocturnal-modal-actions');
                }
            },

            _open() {
                this._overlay.classList.remove('hidden');
                // Force reflow
                void this._overlay.offsetWidth;
                this._overlay.classList.remove('opacity-0');
                this._box.classList.remove('scale-95', 'translate-y-4');
                this._box.classList.add('scale-100', 'translate-y-0');
            },

            _close() {
                this._overlay.classList.add('opacity-0');
                this._box.classList.remove('scale-100', 'translate-y-0');
                this._box.classList.add('scale-95', 'translate-y-4');
                setTimeout(() => {
                    this._overlay.classList.add('hidden');
                }, 300);
            },

            alert(message, title = "System Notification") {
                return new Promise((resolve) => {
                    this._init();
                    this._title.innerHTML = `<span class="text-[#A0717F] mr-2">ℹ</span>${title}`;
                    this._message.textContent = message;
                    this._actions.innerHTML = `
                        <button id="nocturnal-btn-ok" class="w-full sm:w-auto px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-[#FFFFFF] shadow-xl transition-all duration-300 outline-none hover:-translate-y-0.5 focus:ring-2 focus:ring-[#A0717F]" style="background-color: #A0717F;">Acknowledge</button>
                    `;
                    this._open();
                    
                    const btn = document.getElementById('nocturnal-btn-ok');
                    btn.focus();
                    btn.onclick = () => { this._close(); resolve(true); };
                });
            },

            confirm(message, title = "Confirmation Required") {
                return new Promise((resolve) => {
                    this._init();
                    this._title.innerHTML = `<span class="text-yellow-500 mr-2">⚠</span>${title}`;
                    this._message.textContent = message;
                    this._actions.innerHTML = `
                        <button id="nocturnal-btn-cancel" class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-[#CFCBCA] transition-all duration-300 hover:bg-[#2A2729] outline-none focus:ring-2 focus:ring-[rgba(234,211,205,0.2)]" style="border: 1px solid rgba(234, 211, 205, 0.1);">Cancel Process</button>
                        <button id="nocturnal-btn-confirm" class="w-full sm:w-auto px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-[#FFFFFF] shadow-xl transition-all duration-300 outline-none hover:-translate-y-0.5 focus:ring-2 focus:ring-[#A0717F] hover:bg-[#8F6470]" style="background-color: #A0717F;">Confirm Action</button>
                    `;
                    this._open();

                    const btnCancel = document.getElementById('nocturnal-btn-cancel');
                    const btnConfirm = document.getElementById('nocturnal-btn-confirm');
                    btnCancel.onclick = () => { this._close(); resolve(false); };
                    btnConfirm.onclick = () => { this._close(); resolve(true); };
                });
            },

            prompt(message, title = "Input Required") {
                return new Promise((resolve) => {
                    this._init();
                    this._title.innerHTML = `<span class="text-[#A0717F] mr-2">✎</span>${title}`;
                    this._message.innerHTML = `
                        <div class="mb-4">${message}</div>
                        <input type="text" id="nocturnal-prompt-input" class="w-full px-5 py-4 rounded-xl text-sm outline-none transition-all duration-300" style="background-color: #2A2729; border: 1px solid rgba(234, 211, 205, 0.1); color: #EAD3CD;" onfocus="this.style.borderColor='#A0717F';" onblur="this.style.borderColor='rgba(234, 211, 205, 0.1)';">
                    `;
                    this._actions.innerHTML = `
                        <button id="nocturnal-btn-cancel" class="w-full sm:w-auto px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-[#CFCBCA] transition-all duration-300 hover:bg-[#2A2729] outline-none focus:ring-2 focus:ring-[rgba(234,211,205,0.2)]" style="border: 1px solid rgba(234, 211, 205, 0.1);">Cancel</button>
                        <button id="nocturnal-btn-confirm" class="w-full sm:w-auto px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-[#FFFFFF] shadow-xl transition-all duration-300 outline-none hover:-translate-y-0.5 focus:ring-2 focus:ring-[#A0717F]" style="background-color: #A0717F;">Submit</button>
                    `;
                    this._open();

                    const input = document.getElementById('nocturnal-prompt-input');
                    const btnCancel = document.getElementById('nocturnal-btn-cancel');
                    const btnConfirm = document.getElementById('nocturnal-btn-confirm');
                    
                    setTimeout(() => input.focus(), 50);

                    btnCancel.onclick = () => { this._close(); resolve(null); };
                    btnConfirm.onclick = () => { this._close(); resolve(input.value); };
                    input.onkeydown = (e) => { if(e.key === 'Enter') btnConfirm.click(); };
                });
            }
        };

        // Gracefully intercept standard browser alert() so any unedited logic benefits automatically!
        window.alert = function(msg) { window.NocturnalUI.alert(msg); };
    </script>
</body>

</html>




