import { useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../../lib/api'
import Layout from '../../components/Layout'

export default function ProfileConfirmDelete() {
  const [password, setPassword] = useState('')
  const [confirm, setConfirm] = useState(false)
  const [errors, setErrors] = useState({})
  const [deleting, setDeleting] = useState(false)

  const allErrors = Object.values(errors).flat()

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setDeleting(true)
    try {
      await api.delete('/profile', { data: { password } })
      // Session is invalidated server-side; full reload resets auth state.
      window.location.assign('/login')
    } catch (err) {
      setErrors(err.response?.data?.errors ?? {})
      setDeleting(false)
    }
  }

  return (
    <Layout>
      {/* Main Content */}
      <div style={{ backgroundColor: 'transparent', minHeight: '100vh' }}>
        <div className="max-w-3xl mx-auto px-4 py-12">
          {/* Header */}
          <div className="mb-8">
            <Link to="/profile"
              className="text-[#A0717F] hover:text-[#EAD3CD] text-sm font-semibold mb-4 inline-block">
              ← Back to Profile
            </Link>
            <h2 className="text-3xl font-bold text-[#EAD3CD]">Delete Account</h2>
            <p className="text-[#CFCBCA] mt-2">Permanently delete your account and all associated data</p>
          </div>

          {/* Warning Card */}
          <div
            className="bg-[#383537] rounded-2xl shadow-sm p-8 mb-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
            <div className="border-l-4 border-[#ff8e8b] pl-4 py-2" style={{ backgroundColor: 'rgba(255, 142, 139, 0.1)' }}>
              <h3 className="font-bold text-[#ff8e8b] mb-2">⚠️ Warning</h3>
              <p className="text-[#CFCBCA] text-sm mb-4">
                Deleting your account is permanent and cannot be undone. This action will:
              </p>
              <ul className="list-disc list-inside text-[#CFCBCA] text-sm space-y-2">
                <li>Remove your profile and personal information</li>
                <li>Cancel any active bookings</li>
                <li>Remove you from all associated hotels and roles</li>
                <li>Delete all your reviews and preferences</li>
              </ul>
            </div>
          </div>

          {/* Confirmation Form */}
          <div
            className="bg-[#383537] rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
            <h3 className="text-lg font-bold text-[#EAD3CD] mb-6">Confirm Account Deletion</h3>

            {allErrors.length > 0 && (
              <div className="border rounded-lg p-4 mb-6"
                style={{ backgroundColor: 'rgba(255, 142, 139, 0.1)', borderColor: '#4E3B46', color: '#ff8e8b' }}>
                <ul className="list-disc list-inside">
                  {allErrors.map((error, i) => <li key={i}>{error}</li>)}
                </ul>
              </div>
            )}

            <form onSubmit={handleSubmit}>
              {/* Password Confirmation */}
              <div className="mb-6">
                <label htmlFor="password" className="block text-sm font-semibold text-[#CFCBCA] mb-2">
                  Enter Your Password to Confirm
                </label>
                <input type="password" id="password" name="password" value={password}
                  onChange={e => setPassword(e.target.value)}
                  className={`w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border rounded-lg focus:ring-2 focus:ring-[#ff8e8b] focus:border-transparent outline-none transition ${errors.password ? 'border-[#ff8e8b]' : 'border-[#4E3B46]'}`}
                  placeholder="••••••••" required />
                {errors.password && <p className="text-[#ff8e8b] text-sm mt-2">{errors.password[0]}</p>}
              </div>

              {/* Confirmation Checkbox */}
              <div className="mb-8">
                <label className="flex items-center">
                  <input type="checkbox" name="confirm" value="1" required checked={confirm}
                    onChange={e => setConfirm(e.target.checked)}
                    className="w-4 h-4 rounded" style={{ accentColor: '#A0717F' }} />
                  <span className="ml-3 text-sm text-[#CFCBCA]">
                    I understand this action is permanent and cannot be reversed
                  </span>
                </label>
              </div>

              {/* Buttons */}
              <div className="flex gap-4">
                <Link to="/profile"
                  className="flex-1 border border-[#4E3B46] bg-[#2A2729] hover:bg-[#383537] text-[#CFCBCA] font-semibold px-6 py-3 rounded-lg transition text-center">
                  Cancel
                </Link>
                <button type="submit" disabled={deleting}
                  className="flex-1 text-white font-semibold px-6 py-3 rounded-lg transition"
                  style={{ backgroundColor: '#b04441' }}
                  onMouseOver={e => (e.currentTarget.style.backgroundColor = '#853230')}
                  onMouseOut={e => (e.currentTarget.style.backgroundColor = '#b04441')}>
                  Delete Account
                </button>
              </div>
            </form>
          </div>

          {/* Info Box */}
          <div className="mt-8 border rounded-lg p-4"
            style={{ backgroundColor: '#383537', borderColor: '#4E3B46' }}>
            <p className="text-sm text-[#CFCBCA]">
              <strong>Need help?</strong> If you're having issues or want to request your data before deletion, please
              contact our support team.
            </p>
          </div>
        </div>
      </div>
    </Layout>
  )
}
