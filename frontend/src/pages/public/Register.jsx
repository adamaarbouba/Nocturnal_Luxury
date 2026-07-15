import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth, dashboardPathFor } from '../../context/AuthContext'
import Layout from '../../components/Layout'
import Icon from '../../components/Icon'

const inputClass =
  'w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#A0717F] focus:border-transparent outline-none transition'

const roles = [
  ['guest', 'Guest', 'user'],
  ['owner', 'Owner', 'building'],
  ['receptionist', 'Front Desk', 'phone'],
  ['cleaner', 'Cleaner', 'sparkles'],
  ['inspector', 'Inspector', 'clipboard-check'],
]

// Ports auth/register.blade.php
export default function Register() {
  const { register } = useAuth()
  const navigate = useNavigate()
  const [form, setForm] = useState({
    user_type: '', name: '', email: '', password: '', password_confirmation: '',
  })
  const [errors, setErrors] = useState([])
  const [submitting, setSubmitting] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrors([])
    try {
      const user = await register(form)
      navigate(dashboardPathFor(user))
    } catch (err) {
      const data = err.response?.data
      setErrors(data?.errors ? Object.values(data.errors).flat() : [data?.message ?? 'Registration failed.'])
      setSubmitting(false)
    }
  }

  return (
    <Layout>
      <div style={{ backgroundColor: 'transparent', minHeight: '100vh' }} className="flex items-center justify-center px-4 py-8">
        <div className="w-full max-w-md">
          {/* Header */}
          <div className="text-center mb-8">
            <h1 className="text-4xl font-bold text-[#EAD3CD]">Hotel Management</h1>
            <p className="text-[#A0717F] mt-2">Create your account</p>
          </div>

          {/* Register Card */}
          <div className="bg-[#383537] rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
            {errors.length > 0 && (
              <div className="mb-6 border rounded-lg px-4 py-3"
                style={{ backgroundColor: 'rgba(211, 199, 173, 0.05)', borderColor: '#4E3B46', color: '#ff8e8b' }}>
                <ul className="list-disc list-inside">
                  {errors.map((error, i) => <li key={i}>{error}</li>)}
                </ul>
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
              {/* User Type Selection (Premium Grid) */}
              <div className="mb-6">
                <label className="block text-sm font-medium text-[#CFCBCA] mb-4">
                  Select Your Purpose <span style={{ color: '#A0717F' }}>*</span>
                </label>
                <div className="grid grid-cols-2 lg:grid-cols-3 gap-3">
                  {roles.map(([value, label, icon]) => (
                    <label key={value} className="relative cursor-pointer group">
                      <input type="radio" name="user_type" value={value} className="peer sr-only"
                        checked={form.user_type === value}
                        onChange={() => setForm({ ...form, user_type: value })} required />
                      <div className="px-2 py-3 rounded-xl border border-[#4E3B46] bg-[#2A2729] peer-checked:border-[#A0717F] peer-checked:bg-[#A0717F]/10 transition-all text-center">
                        <div className="flex justify-center mb-2 opacity-50 group-hover:opacity-100 transition-opacity">
                          <Icon name={icon} size="sm" className="text-[#EAD3CD]" />
                        </div>
                        <span className="block text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] peer-checked:text-[#EAD3CD]">{label}</span>
                      </div>
                    </label>
                  ))}
                </div>
              </div>

              {/* Name */}
              <div>
                <label htmlFor="name" className="block text-sm font-medium text-[#CFCBCA] mb-2">
                  Full Name <span style={{ color: '#A0717F' }}>*</span>
                </label>
                <input type="text" id="name" value={form.name}
                  onChange={e => setForm({ ...form, name: e.target.value })}
                  className={inputClass} placeholder="John Doe" required />
              </div>

              {/* Email */}
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-[#CFCBCA] mb-2">
                  Email Address <span style={{ color: '#A0717F' }}>*</span>
                </label>
                <input type="email" id="email" value={form.email}
                  onChange={e => setForm({ ...form, email: e.target.value })}
                  className={inputClass} placeholder="you@example.com" required />
              </div>

              {/* Password */}
              <div>
                <label htmlFor="password" className="block text-sm font-medium text-[#CFCBCA] mb-2">
                  Password <span style={{ color: '#A0717F' }}>*</span>
                </label>
                <input type="password" id="password" value={form.password}
                  onChange={e => setForm({ ...form, password: e.target.value })}
                  className={inputClass} placeholder="••••••••" required />
                <p className="text-[#A0717F] text-xs mt-1">Minimum 8 characters</p>
              </div>

              {/* Confirm Password */}
              <div>
                <label htmlFor="password_confirmation" className="block text-sm font-medium text-[#CFCBCA] mb-2">
                  Confirm Password <span style={{ color: '#A0717F' }}>*</span>
                </label>
                <input type="password" id="password_confirmation" value={form.password_confirmation}
                  onChange={e => setForm({ ...form, password_confirmation: e.target.value })}
                  className={inputClass} placeholder="••••••••" required />
              </div>

              {/* Register Button */}
              <button type="submit" disabled={submitting}
                className="w-full text-white font-semibold py-2 px-4 rounded-lg transition duration-200 mt-6 bg-[#A0717F] hover:bg-[#8F6470] disabled:opacity-60">
                Create Account
              </button>
            </form>

            {/* Login Link */}
            <p className="text-center text-[#CFCBCA] text-sm mt-6">
              Already have an account?{' '}
              <Link to="/login" className="font-semibold transition text-[#EAD3CD] hover:text-[#CFCBCA]">
                Sign in here
              </Link>
            </p>
          </div>

          {/* Footer */}
          <p className="text-center text-[#CFCBCA] text-xs mt-6">
            © 2026 Hotel Management System. All rights reserved.
          </p>
        </div>
      </div>
    </Layout>
  )
}
