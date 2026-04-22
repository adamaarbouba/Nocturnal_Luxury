{{-- Public Footer Component — Nivada Branding --}}
<footer style="background-color: #383537; border-top: 1px solid rgba(234, 211, 205, 0.1);" class="mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-16">

        {{-- Top: Brand + Newsletter --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
            {{-- Brand --}}
            <div>
                <h2 style="color: #EAD3CD; font-family: 'Georgia', serif; font-size: 1.5rem; font-weight: 700; letter-spacing: 0.3em;">
                    NOCTURNAL LUXURY
                </h2>
                <p class="mt-4 text-sm leading-relaxed max-w-md" style="color: rgba(207, 203, 202, 0.6);">
                    A global collection of luxury ateliers dedicated to the art of refined living and the preservation of quiet moments.
                </p>
            </div>

            {{-- Newsletter --}}
            <div class="flex flex-col items-start md:items-end">
                <p class="text-xs font-semibold tracking-widest uppercase mb-4" style="color: #EAD3CD;">Stay Informed</p>
                <div class="flex w-full max-w-sm">
                    <input type="email" placeholder="EMAIL ADDRESS"
                        class="text-xs tracking-wider uppercase rounded-l-lg px-4 py-3 w-full outline-none transition-colors"
                        style="background-color: #4E3B46; border: 1px solid rgba(160, 113, 127, 0.2); border-right: none; color: #EAD3CD; letter-spacing: 0.1em;"
                        onfocus="this.style.borderColor='rgba(160, 113, 127, 0.5)';"
                        onblur="this.style.borderColor='rgba(160, 113, 127, 0.2)';">
                    <button class="text-xs font-semibold tracking-wider uppercase rounded-r-lg px-5 py-3 text-white shrink-0 transition-all duration-300"
                        style="background-color: #A0717F;"
                        onmouseover="this.style.backgroundColor='#b58290';"
                        onmouseout="this.style.backgroundColor='#A0717F';">
                        JOIN
                    </button>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-8" style="border-top: 1px solid rgba(234, 211, 205, 0.08);">
            {{-- Links --}}
            <div class="flex flex-wrap items-center gap-6">
                <a href="#" class="text-xs tracking-wider uppercase transition-colors duration-300"
                    style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.1em;"
                    onmouseover="this.style.color='#CFCBCA';"
                    onmouseout="this.style.color='rgba(207, 203, 202, 0.5)';">Privacy Policy</a>
                <a href="#" class="text-xs tracking-wider uppercase transition-colors duration-300"
                    style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.1em;"
                    onmouseover="this.style.color='#CFCBCA';"
                    onmouseout="this.style.color='rgba(207, 203, 202, 0.5)';">Terms</a>
                <a href="#" class="text-xs tracking-wider uppercase transition-colors duration-300"
                    style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.1em;"
                    onmouseover="this.style.color='#CFCBCA';"
                    onmouseout="this.style.color='rgba(207, 203, 202, 0.5)';">Atelier Journal</a>
                <a href="#" class="text-xs tracking-wider uppercase transition-colors duration-300"
                    style="color: rgba(207, 203, 202, 0.5); letter-spacing: 0.1em;"
                    onmouseover="this.style.color='#CFCBCA';"
                    onmouseout="this.style.color='rgba(207, 203, 202, 0.5)';">Contact</a>
            </div>

            {{-- Copyright --}}
            <p class="text-xs" style="color: rgba(207, 203, 202, 0.35);">
                &copy; {{ date('Y') }} Nocturnal Luxury. An Atelier of Refined Hospitality.
            </p>
        </div>
    </div>
</footer>
