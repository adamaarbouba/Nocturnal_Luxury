import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth, dashboardPathFor } from '../../context/AuthContext'
import Layout from '../../components/Layout'

const inputClass =
  'w-full px-4 py-2 bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46] rounded-lg focus:ring-2 focus:ring-[#A0717F] focus:border-transparent outline-none transition'

// Ports auth/login.blade.php
export default function Login() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [form, setForm] = useState({ email: '', password: '', remember: false })
  const [errors, setErrors] = useState([])
  const [submitting, setSubmitting] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrors([])
    try {
      const user = await login(form)
      navigate(dashboardPathFor(user))
    } catch (err) {
      const data = err.response?.data
      setErrors(data?.errors ? Object.values(data.errors).flat() : [data?.message ?? 'Login failed.'])
      setSubmitting(false)
    }
  }

  return (
    <Layout>
      <div style={{ backgroundColor: 'transparent', minHeight: '100vh' }} className="flex items-center justify-center px-4">
        <div className="w-full max-w-md">
          {/* Header */}
          <div className="text-center mb-8">
            <h1 className="text-4xl font-bold text-[#EAD3CD]">Hotel Management</h1>
            <p className="text-[#A0717F] mt-2">Sign in to your account</p>
          </div>

          {/* Login Card */}
          <div className="bg-[#383537] rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg hover:border-[#4E3B46] transition">
            {errors.length > 0 && (
              <div className="mb-6 border rounded-lg px-4 py-3"
                style={{ backgroundColor: 'rgba(211, 199, 173, 0.05)', borderColor: '#4E3B46', color: '#ff8e8b' }}>
                <ul>
                  {errors.map((error, i) => <li key={i}>{error}</li>)}
                </ul>
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Email */}
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-[#CFCBCA] mb-2">
                  Email Address
                </label>
                <input type="email" id="email" value={form.email}
                  onChange={e => setForm({ ...form, email: e.target.value })}
                  className={inputClass} placeholder="you@example.com" required />
              </div>

              {/* Password */}
              <div>
                <label htmlFor="password" className="block text-sm font-medium text-[#CFCBCA] mb-2">
                  Password
                </label>
                <input type="password" id="password" value={form.password}
                  onChange={e => setForm({ ...form, password: e.target.value })}
                  className={inputClass} placeholder="••••••••" required />
              </div>

              {/* Remember Me */}
              <div className="flex items-center">
                <input type="checkbox" id="remember" checked={form.remember}
                  onChange={e => setForm({ ...form, remember: e.target.checked })}
                  className="h-4 w-4 rounded" style={{ accentColor: '#A0717F' }} />
                <label htmlFor="remember" className="ml-2 block text-sm text-[#CFCBCA]">
                  Remember me
                </label>
              </div>

              {/* Login Button */}
              <button type="submit" disabled={submitting}
                className="w-full text-white font-semibold py-2 px-4 rounded-lg transition duration-200 bg-[#A0717F] hover:bg-[#8F6470] disabled:opacity-60">
                Sign In
              </button>
            </form>

            {/* Register Link */}
            <p className="text-center text-[#CFCBCA] text-sm mt-6">
              Don't have an account?{' '}
              <Link to="/register" className="font-semibold transition text-[#EAD3CD] hover:text-[#CFCBCA]">
                Register here
              </Link>
            </p>
          </div>

          <div className="text-[#CFCBCA] text-[10px] mt-8 text-center p-4 bg-[#2A2729] rounded-xl border border-[#4E3B46]/50">
            <p className="mb-2 font-bold uppercase tracking-widest text-[#A0717F]">Quick Access (Test Mode)</p>
            <div className="space-y-1 opacity-70">
              <p>Owner: <span className="text-[#EAD3CD]">owner@test.com</span></p>
              <p>Receptionist: <span className="text-[#EAD3CD]">receptionist@test.com</span></p>
              <p>Cleaner: <span className="text-[#EAD3CD]">cleaner@test.com</span></p>
              <p>Inspector: <span className="text-[#EAD3CD]">inspector@test.com</span></p>
              <p>Guest: <span className="text-[#EAD3CD]">guest@test.com</span></p>
              <p className="mt-2 pt-2 border-t border-[#4E3B46]/30">Password: <span className="text-[#EAD3CD]">password123</span></p>
            </div>
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
