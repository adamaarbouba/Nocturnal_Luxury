import { useEffect, useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import api from '../../lib/api'
import Layout from '../../components/Layout'
import { dashboardPathFor } from '../../context/AuthContext'

const monthShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
const monthLong = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']

function formatDate(dateString, months) {
  if (!dateString) return '—'
  const d = new Date(dateString)
  return `${months[d.getMonth()]} ${String(d.getDate()).padStart(2, '0')}, ${d.getFullYear()}`
}

export default function ProfileShow() {
  const location = useLocation()
  const [user, setUser] = useState(null)
  const [stats, setStats] = useState({ bookings: 0, reviews: 0 })
  const [success, setSuccess] = useState(location.state?.success ?? null)
  const [modalOpen, setModalOpen] = useState(false)
  const [password, setPassword] = useState('')
  const [errors, setErrors] = useState([])
  const [passwordError, setPasswordError] = useState(null)
  const [deleting, setDeleting] = useState(false)

  useEffect(() => {
    api.get('/profile')
      .then(res => {
        setUser(res.data.user)
        setStats(res.data.stats)
      })
      .catch(() => {})
  }, [])

  const handleDelete = async (e) => {
    e.preventDefault()
    setErrors([])
    setPasswordError(null)
    setDeleting(true)
    try {
      await api.delete('/profile', { data: { password } })
      // Session is invalidated server-side; full reload resets auth state.
      window.location.assign('/login')
    } catch (err) {
      const errs = err.response?.data?.errors ?? {}
      setErrors(Object.values(errs).flat())
      setPasswordError(errs.password?.[0] ?? null)
      setDeleting(false)
    }
  }

  if (!user) {
    return (
      <Layout>
        <div className="py-24 text-center text-mist">Loading…</div>
      </Layout>
    )
  }

  const isAdmin = user.role?.slug === 'admin'

  return (
    <Layout>
      {/* Main Content */}
      <div style={{ backgroundColor: 'transparent', minHeight: '100vh' }}>
        <div className="max-w-3xl mx-auto px-4 py-12">
          {/* Header */}
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-3xl font-bold text-[#EAD3CD]">Account Settings</h2>
            <Link to={dashboardPathFor(user)} className="text-white font-semibold px-4 py-2 rounded transition"
              style={{ backgroundColor: '#A0717F' }}
              onMouseOver={e => (e.currentTarget.style.backgroundColor = '#8F6470')}
              onMouseOut={e => (e.currentTarget.style.backgroundColor = '#A0717F')}>
              ← Back to Dashboard
            </Link>
          </div>

          {success && (
            <div className="border rounded-lg p-4 mb-8"
              style={{ backgroundColor: 'rgba(234, 211, 205, 0.1)', borderColor: '#4E3B46', color: '#A0717F' }}>
              <p className="font-semibold">✓ {success}</p>
            </div>
          )}

          {/* Profile Card */}
          <div className="rounded-2xl shadow-sm p-8 mb-8 border border-transparent hover:shadow-lg transition"
            style={{ backgroundColor: '#383537', borderColor: '#4E3B46' }}>
            {/* User Avatar Section */}
            <div className="flex items-center gap-6 mb-8 pb-8" style={{ borderBottom: '1px solid #4E3B46' }}>
              <div className="w-24 h-24 flex items-center justify-center rounded-full text-white text-4xl font-bold"
                style={{ background: 'linear-gradient(135deg, #A0717F, #4E3B46)' }}>
                {user.name.charAt(0).toUpperCase()}
              </div>
              <div>
                <h3 className="text-2xl font-bold text-[#EAD3CD]">{user.name}</h3>
                <p className="text-[#CFCBCA]">{user.email}</p>
                <p className="text-sm text-[#CFCBCA] mt-2" style={{ opacity: 0.8 }}>
                  <strong>{user.role?.slug ? user.role.slug.charAt(0).toUpperCase() + user.role.slug.slice(1) : ''}</strong> Account • Member since{' '}
                  {formatDate(user.created_at, monthShort)}
                </p>
              </div>
            </div>

            {/* Profile Information Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
              {/* Name */}
              <div>
                <label className="block text-sm font-semibold text-[#CFCBCA] mb-2">Full Name</label>
                <p className="text-lg p-3 rounded" style={{ backgroundColor: '#2A2729', color: '#CFCBCA' }}>
                  {user.name}</p>
              </div>

              {/* Email */}
              <div>
                <label className="block text-sm font-semibold text-[#CFCBCA] mb-2">Email Address</label>
                <p className="text-lg p-3 rounded" style={{ backgroundColor: '#2A2729', color: '#CFCBCA' }}>
                  {user.email}</p>
              </div>

              {/* Phone */}
              <div>
                <label className="block text-sm font-semibold text-[#CFCBCA] mb-2">Phone Number</label>
                <p className="text-lg p-3 rounded" style={{ backgroundColor: '#2A2729', color: '#CFCBCA' }}>
                  {user.phone ?? '—'}</p>
              </div>

              {/* Member Since */}
              <div>
                <label className="block text-sm font-semibold text-[#CFCBCA] mb-2">Member Since</label>
                <p className="text-lg p-3 rounded" style={{ backgroundColor: '#2A2729', color: '#CFCBCA' }}>
                  {formatDate(user.created_at, monthLong)}</p>
              </div>
            </div>

            {/* Action Buttons */}
            {isAdmin && (
              <div className="pt-6 flex gap-4" style={{ borderTop: '1px solid #4E3B46' }}>
                <Link to="/profile/edit"
                  className="inline-block text-white font-semibold px-6 py-3 rounded-lg transition"
                  style={{ backgroundColor: '#A0717F' }}
                  onMouseOver={e => (e.currentTarget.style.backgroundColor = '#8F6470')}
                  onMouseOut={e => (e.currentTarget.style.backgroundColor = '#A0717F')}>
                  Edit Profile
                </Link>
                <Link to="/profile/password"
                  className="inline-block text-white font-semibold px-6 py-3 rounded-lg transition"
                  style={{ backgroundColor: '#A0717F' }}
                  onMouseOver={e => (e.currentTarget.style.backgroundColor = '#8F6470')}
                  onMouseOut={e => (e.currentTarget.style.backgroundColor = '#A0717F')}>
                  Change Password
                </Link>
              </div>
            )}
          </div>

          {/* Account Statistics */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div className="bg-[#383537] rounded-2xl shadow-sm p-6 text-center border border-transparent hover:shadow-lg transition">
              <p className="text-[#CFCBCA] text-sm font-semibold mb-2">Total Bookings</p>
              <p className="text-3xl font-bold text-[#EAD3CD]">{stats.bookings}</p>
            </div>
            <div className="bg-[#383537] rounded-2xl shadow-sm p-6 text-center border border-transparent hover:shadow-lg transition">
              <p className="text-[#CFCBCA] text-sm font-semibold mb-2">Reviews Written</p>
              <p className="text-3xl font-bold text-[#EAD3CD]">{stats.reviews}</p>
            </div>
            <div className="bg-[#383537] rounded-2xl shadow-sm p-6 text-center border border-transparent hover:shadow-lg transition">
              <p className="text-[#CFCBCA] text-sm font-semibold mb-2">Account Status</p>
              <p className="text-xl font-bold">
                <span className="px-3 py-1 rounded-full text-sm text-[#A0717F]"
                  style={{ backgroundColor: '#2A2729' }}>Active</span>
              </p>
            </div>
          </div>

          {/* Account Actions */}
          {!isAdmin && (
            <div className="rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg transition"
              style={{ backgroundColor: '#383537' }}>
              <h3 className="text-lg font-bold text-[#EAD3CD] mb-6">Account Actions</h3>
              <div className="space-y-3">
                <Link to="/profile/edit" className="block p-4 rounded-lg transition"
                  style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729' }}>
                  <h4 className="font-semibold text-[#A0717F]">Edit Profile</h4>
                  <p className="text-sm text-[#CFCBCA]" style={{ opacity: 0.8 }}>Update your name, email, and phone</p>
                </Link>
                <Link to="/profile/password" className="block p-4 rounded-lg transition"
                  style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729' }}>
                  <h4 className="font-semibold text-[#A0717F]">Change Password</h4>
                  <p className="text-sm text-[#CFCBCA]" style={{ opacity: 0.8 }}>Update your password for security</p>
                </Link>
                <button type="button" onClick={() => setModalOpen(true)}
                  className="w-full text-left block p-4 rounded-lg transition"
                  style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729' }}>
                  <h4 className="font-semibold text-[#ff8e8b]">Delete Account</h4>
                  <p className="text-sm text-[#CFCBCA]" style={{ opacity: 0.8 }}>Permanently delete your account</p>
                </button>
              </div>
            </div>
          )}
        </div>

        {/* Delete Account Modal */}
        {modalOpen && (
          <div className="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50"
            onClick={() => setModalOpen(false)}>
            <div className="rounded-2xl shadow-lg p-8 max-w-sm w-full mx-4" onClick={e => e.stopPropagation()}
              style={{ backgroundColor: '#383537', border: '1px solid #4E3B46' }}>
              <h3 className="text-xl font-bold text-[#EAD3CD] mb-2">Delete Account</h3>
              <span className="text-sm font-semibold text-[#ff8e8b]">This action cannot be reverted</span>

              {errors.length > 0 && (
                <div className="border rounded-lg p-4 mb-4 mt-4"
                  style={{ backgroundColor: 'rgba(255, 142, 139, 0.1)', borderColor: '#4E3B46', color: '#ff8e8b' }}>
                  <ul className="list-disc list-inside text-sm">
                    {errors.map((error, i) => <li key={i}>{error}</li>)}
                  </ul>
                </div>
              )}

              <form onSubmit={handleDelete}>
                <div className="mb-6 mt-6">
                  <input type="password" name="password" value={password}
                    onChange={e => setPassword(e.target.value)}
                    className={`w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition ${passwordError ? 'border-[#ff8e8b]' : ''}`}
                    style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }}
                    placeholder="Enter password" required />
                  {passwordError && <p className="text-[#ff8e8b] text-sm mt-2">{passwordError}</p>}
                </div>

                <div className="flex gap-3">
                  <button type="button" onClick={() => setModalOpen(false)}
                    className="flex-1 font-semibold px-4 py-2 rounded-lg transition"
                    style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }}>
                    Cancel
                  </button>
                  <button type="submit" disabled={deleting}
                    className="flex-1 text-white font-semibold px-4 py-2 rounded-lg transition"
                    style={{ backgroundColor: '#b04441' }}
                    onMouseOver={e => (e.currentTarget.style.backgroundColor = '#853230')}
                    onMouseOut={e => (e.currentTarget.style.backgroundColor = '#b04441')}>
                    Delete
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </Layout>
  )
}
