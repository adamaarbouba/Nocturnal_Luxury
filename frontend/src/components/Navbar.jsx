import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth, dashboardPathFor } from '../context/AuthContext'
import Icon from './Icon'

const dropdownItemClass =
  'flex items-center gap-3 px-4 py-3 text-sm transition-all duration-200 text-[#CFCBCA] hover:bg-[#A0717F]/10 hover:text-[#EAD3CD]'

export default function Navbar() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const [mobileOpen, setMobileOpen] = useState(false)

  const handleLogout = async () => {
    await logout()
    navigate('/login')
  }

  const isStaff = ['cleaner', 'inspector', 'receptionist', 'staff'].includes(user?.role?.slug)

  return (
    <header className="sticky top-0 z-40" style={{ backgroundColor: '#383537', borderBottom: '1px solid rgba(234, 211, 205, 0.1)' }}>
      <nav className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 flex items-center justify-between" style={{ height: '72px' }}>
        <Link
          to="/"
          className="transition-colors duration-300 text-[#EAD3CD] hover:text-[#A0717F]"
          style={{ fontFamily: "'Georgia', serif", fontSize: '1.25rem', fontWeight: 700, letterSpacing: '0.2em' }}
        >
          NOCTURNAL LUXURY
        </Link>

        <div className="hidden md:flex items-center gap-8">
          {!user ? (
            <>
              <Link
                to="/login"
                className="text-xs font-medium uppercase transition-colors duration-300 relative group"
                style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}
              >
                Login
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full" style={{ backgroundColor: '#A0717F' }}></span>
              </Link>
              <Link
                to="/register"
                className="text-xs font-semibold uppercase rounded-md px-5 py-2.5 text-white transition-all duration-300 ml-2 bg-[#A0717F] hover:bg-[#b58290] hover:shadow-[0_8px_25px_rgba(160,113,127,0.25)]"
                style={{ letterSpacing: '0.12em' }}
              >
                Book Now
              </Link>
            </>
          ) : (
            <div className="relative group">
              <button
                className="flex items-center gap-2 text-xs font-medium uppercase transition-colors duration-300"
                style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}
              >
                <Icon name="user" size="sm" hoverable style={{ color: 'currentColor' }} />
                Account
                <Icon name="chevron-down" size="sm" hoverable className="transition group-hover:rotate-180" style={{ color: 'currentColor' }} />
              </button>

              <div
                className="absolute right-0 mt-3 w-52 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden"
                style={{ backgroundColor: '#4E3B46', border: '1px solid rgba(234, 211, 205, 0.1)', boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.5)' }}
              >
                <div className="px-4 py-3" style={{ borderBottom: '1px solid rgba(234, 211, 205, 0.08)' }}>
                  <p className="text-sm font-semibold truncate" style={{ color: '#EAD3CD' }}>{user.name}</p>
                  <p className="text-xs truncate mt-0.5" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>{user.role?.title ?? 'User'}</p>
                </div>

                <Link to="/profile" className={dropdownItemClass}>
                  <Icon name="user" size="sm" hoverable style={{ color: '#A0717F' }} />
                  Profile
                </Link>

                <Link to={dashboardPathFor(user)} className={dropdownItemClass}>
                  <Icon name="home" size="sm" hoverable style={{ color: '#A0717F' }} />
                  Dashboard
                </Link>

                {isStaff && (
                  <>
                    <Link to="/staff/hotels" className={dropdownItemClass}>
                      <Icon name="building" size="sm" hoverable style={{ color: '#A0717F' }} />
                      Browse Jobs
                    </Link>
                    <Link to="/staff/my-applications" className={dropdownItemClass}>
                      <Icon name="file" size="sm" hoverable style={{ color: '#A0717F' }} />
                      My Applications
                    </Link>
                  </>
                )}

                <Link to="/guest/bookings" className={dropdownItemClass}>
                  <Icon name="calendar" size="sm" hoverable style={{ color: '#A0717F' }} />
                  My Bookings
                </Link>

                <Link to="/guest/reviews" className={dropdownItemClass}>
                  <Icon name="star" size="sm" hoverable style={{ color: '#A0717F' }} />
                  My Reviews
                </Link>

                <div style={{ borderTop: '1px solid rgba(234, 211, 205, 0.08)' }}>
                  <button
                    type="button"
                    onClick={handleLogout}
                    className="w-full flex items-center gap-3 text-left px-4 py-3 text-sm transition-all duration-200 font-medium text-[#A0717F] hover:bg-[#A0717F]/10"
                  >
                    <Icon name="logout" size="sm" hoverable style={{ color: '#A0717F' }} />
                    Logout
                  </button>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Mobile Menu Button */}
        <button className="md:hidden p-2 transition-colors" style={{ color: '#EAD3CD' }} onClick={() => setMobileOpen(o => !o)}>
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </nav>

      {/* Mobile Menu */}
      {mobileOpen && (
        <div className="md:hidden px-4 pb-6 space-y-4" style={{ borderTop: '1px solid rgba(234, 211, 205, 0.08)' }}>
          <Link to="/guest/hotels" className="block py-2 text-sm uppercase" style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}>Suites</Link>
          <a href="#" className="block py-2 text-sm uppercase" style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}>Dining</a>
          <a href="#" className="block py-2 text-sm uppercase" style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}>Wellness</a>
          <a href="#" className="block py-2 text-sm uppercase" style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}>Concierge</a>
          {!user ? (
            <>
              <Link to="/login" className="block py-2 text-sm uppercase" style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}>Login</Link>
              <Link
                to="/register"
                className="inline-block text-white text-xs font-semibold uppercase rounded-md px-5 py-2.5 mt-2"
                style={{ backgroundColor: '#A0717F', letterSpacing: '0.12em' }}
              >
                Book Now
              </Link>
            </>
          ) : (
            <>
              <Link to={dashboardPathFor(user)} className="block py-2 text-sm uppercase" style={{ color: '#CFCBCA', letterSpacing: '0.15em' }}>Dashboard</Link>
              <button type="button" onClick={handleLogout} className="block py-2 text-sm uppercase font-medium" style={{ color: '#A0717F', letterSpacing: '0.15em' }}>
                Logout
              </button>
            </>
          )}
        </div>
      )}
    </header>
  )
}
