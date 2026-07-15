import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import api from '../../lib/api'
import Layout from '../../components/Layout'

export default function ProfileChangePassword() {
  const navigate = useNavigate()
  const [form, setForm] = useState({ current_password: '', password: '', password_confirmation: '' })
  const [errors, setErrors] = useState({})
  const [saving, setSaving] = useState(false)

  const set = (field) => (e) => setForm({ ...form, [field]: e.target.value })
  const allErrors = Object.values(errors).flat()

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSaving(true)
    try {
      await api.post('/profile/password', form)
      navigate('/profile', { state: { success: 'Password changed successfully!' } })
    } catch (err) {
      setErrors(err.response?.data?.errors ?? {})
      setSaving(false)
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
            <h2 className="text-3xl font-bold text-[#EAD3CD]">Change Password</h2>
          </div>

          {allErrors.length > 0 && (
            <div className="border rounded-lg p-4 mb-8"
              style={{ backgroundColor: 'rgba(255, 142, 139, 0.1)', borderColor: '#4E3B46', color: '#ff8e8b' }}>
              <h3 className="font-semibold mb-3">Please fix the following errors:</h3>
              <ul className="list-disc list-inside space-y-1">
                {allErrors.map((error, i) => <li key={i}>{error}</li>)}
              </ul>
            </div>
          )}

          {/* Change Password Form */}
          <form onSubmit={handleSubmit}
            className="rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg transition"
            style={{ backgroundColor: '#383537' }}>
            {/* Current Password */}
            <div className="mb-6">
              <label htmlFor="current_password" className="block text-sm font-semibold text-[#CFCBCA] mb-2">Current
                Password</label>
              <input type="password" name="current_password" id="current_password"
                value={form.current_password} onChange={set('current_password')}
                className={`w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition ${errors.current_password ? 'border-[#ff8e8b]' : ''}`}
                style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }} required />
              {errors.current_password && <p className="text-[#ff8e8b] text-sm mt-2">{errors.current_password[0]}</p>}
            </div>

            {/* New Password */}
            <div className="mb-6">
              <label htmlFor="password" className="block text-sm font-semibold text-[#CFCBCA] mb-2">New Password</label>
              <input type="password" name="password" id="password"
                value={form.password} onChange={set('password')}
                className={`w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition ${errors.password ? 'border-[#ff8e8b]' : ''}`}
                style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }} required />
              {errors.password && <p className="text-[#ff8e8b] text-sm mt-2">{errors.password[0]}</p>}
            </div>

            {/* Confirm Password */}
            <div className="mb-8">
              <label htmlFor="password_confirmation" className="block text-sm font-semibold text-[#CFCBCA] mb-2">Confirm
                Password</label>
              <input type="password" name="password_confirmation" id="password_confirmation"
                value={form.password_confirmation} onChange={set('password_confirmation')}
                className="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition"
                style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }} required />
              {errors.password_confirmation && <p className="text-[#ff8e8b] text-sm mt-2">{errors.password_confirmation[0]}</p>}
            </div>

            {/* Buttons */}
            <div className="flex gap-4 pt-6" style={{ borderTop: '1px solid #4E3B46' }}>
              <Link to="/profile" className="flex-1 text-center px-6 py-3 rounded-lg transition"
                style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }}>
                Cancel
              </Link>
              <button type="submit" disabled={saving}
                className="flex-1 text-white font-semibold px-6 py-3 rounded-lg transition"
                style={{ backgroundColor: '#A0717F' }}
                onMouseOver={e => (e.currentTarget.style.backgroundColor = '#8F6470')}
                onMouseOut={e => (e.currentTarget.style.backgroundColor = '#A0717F')}>
                Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  )
}
