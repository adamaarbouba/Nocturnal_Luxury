import Navbar from './Navbar'
import Footer from './Footer'

// Ports layouts/app.blade.php. Session flash alerts are page-local in the SPA:
// pass rendered <Alert> elements via the `flash` prop.
// ponytail: global modal machinery (NocturnalUI) skipped — pages handle their own confirms.
export default function Layout({ flash, children }) {
  return (
    <div className="bg-[#4E3B46] font-sans text-[#CFCBCA] antialiased min-h-screen flex flex-col">
      <Navbar />

      {flash && <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 w-full">{flash}</div>}

      <main className="flex-1">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">{children}</div>
      </main>

      <Footer />
    </div>
  )
}
