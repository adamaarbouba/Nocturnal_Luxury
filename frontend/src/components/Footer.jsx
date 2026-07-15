const footerLinks = ['Privacy Policy', 'Terms', 'Atelier Journal', 'Contact']

export default function Footer() {
  return (
    <footer className="mt-0" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-16">
        {/* Top: Brand + Newsletter */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
          <div>
            <h2 style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif", fontSize: '1.5rem', fontWeight: 700, letterSpacing: '0.3em' }}>
              NOCTURNAL LUXURY
            </h2>
            <p className="mt-4 text-sm leading-relaxed max-w-md" style={{ color: 'rgba(207, 203, 202, 0.6)' }}>
              A global collection of luxury ateliers dedicated to the art of refined living and the preservation of quiet moments.
            </p>
          </div>

          <div className="flex flex-col items-start md:items-end">
            <p className="text-xs font-semibold tracking-widest uppercase mb-4" style={{ color: '#EAD3CD' }}>Stay Informed</p>
            <div className="flex w-full max-w-sm">
              <input
                type="email"
                placeholder="EMAIL ADDRESS"
                className="text-xs tracking-wider uppercase rounded-l-lg px-4 py-3 w-full outline-none transition-colors border border-r-0 border-[rgba(160,113,127,0.2)] focus:border-[rgba(160,113,127,0.5)]"
                style={{ backgroundColor: '#4E3B46', color: '#EAD3CD', letterSpacing: '0.1em' }}
              />
              <button
                className="text-xs font-semibold tracking-wider uppercase rounded-r-lg px-5 py-3 text-white shrink-0 transition-all duration-300 bg-[#A0717F] hover:bg-[#b58290]"
              >
                JOIN
              </button>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="flex flex-col md:flex-row items-center justify-between gap-4 pt-8" style={{ borderTop: '1px solid rgba(234, 211, 205, 0.08)' }}>
          <div className="flex flex-wrap items-center gap-6">
            {footerLinks.map((label) => (
              <a
                key={label}
                href="#"
                className="text-xs tracking-wider uppercase transition-colors duration-300 text-[rgba(207,203,202,0.5)] hover:text-[#CFCBCA]"
                style={{ letterSpacing: '0.1em' }}
              >
                {label}
              </a>
            ))}
          </div>

          <p className="text-xs" style={{ color: 'rgba(207, 203, 202, 0.35)' }}>
            &copy; {new Date().getFullYear()} Nocturnal Luxury. An Atelier of Refined Hospitality.
          </p>
        </div>
      </div>
    </footer>
  )
}
