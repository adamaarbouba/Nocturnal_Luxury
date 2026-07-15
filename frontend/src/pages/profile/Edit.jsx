import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import api from '../../lib/api'
import Layout from '../../components/Layout'

export default function ProfileEdit() {
  const navigate = useNavigate()
  const [form, setForm] = useState({ name: '', email: '', phone: '' })
  const [errors, setErrors] = useState({})
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    api.get('/profile')
      .then(res => setForm({
        name: res.data.user.name ?? '',
        email: res.data.user.email ?? '',
        phone: res.data.user.phone ?? '',
      }))
      .catch(() => {})
  }, [])

  const set = (field) => (e) => setForm({ ...form, [field]: e.target.value })
  const allErrors = Object.values(errors).flat()

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSaving(true)
    try {
      await api.patch('/profile', form)
      navigate('/profile', { state: { success: 'Profile updated successfully!' } })
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
            <h2 className="text-3xl font-bold text-[#EAD3CD]">Edit Profile</h2>
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

          {/* Edit Form */}
          <form onSubmit={handleSubmit}
            className="rounded-2xl shadow-sm p-8 border border-transparent hover:shadow-lg transition"
            style={{ backgroundColor: '#383537' }}>
            {/* Full Name */}
            <div className="mb-6">
              <label htmlFor="name" className="block text-sm font-semibold text-[#CFCBCA] mb-2">Full Name</label>
              <input type="text" name="name" id="name" value={form.name} onChange={set('name')}
                className={`w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition ${errors.name ? 'border-[#ff8e8b]' : ''}`}
                style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }} required />
              {errors.name && <p className="text-[#ff8e8b] text-sm mt-2">{errors.name[0]}</p>}
            </div>

            {/* Email Address */}
            <div className="mb-6">
              <label htmlFor="email" className="block text-sm font-semibold text-[#CFCBCA] mb-2">Email Address</label>
              <input type="email" name="email" id="email" value={form.email} onChange={set('email')}
                className={`w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition ${errors.email ? 'border-[#ff8e8b]' : ''}`}
                style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }} required />
              {errors.email && <p className="text-[#ff8e8b] text-sm mt-2">{errors.email[0]}</p>}
            </div>

            {/* Phone Number */}
            <div className="mb-8">
              <label htmlFor="phone" className="block text-sm font-semibold text-[#CFCBCA] mb-2">Phone Number</label>
              <input type="text" name="phone" id="phone" value={form.phone} onChange={set('phone')}
                className="w-full px-4 py-2 rounded-lg focus:ring-2 focus:border-transparent outline-none transition"
                style={{ border: '1px solid #4E3B46', backgroundColor: '#2A2729', color: '#CFCBCA' }} />
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
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  )
}
