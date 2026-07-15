import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../../components/Layout'
import Icon from '../../../components/Icon'
import Alert from '../../../components/Alert'
import api from '../../../lib/api'

// Ports resources/views/staff/hotels/apply.blade.php
export default function StaffHotelsApply() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [hotel, setHotel] = useState(null)
  const [role, setRole] = useState(null)
  const [message, setMessage] = useState('')
  const [errors, setErrors] = useState({})
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    api.get(`/staff/hotels/${id}/apply`)
      .then(res => { setHotel(res.data.hotel); setRole(res.data.role) })
      .catch(err => setError(err.response?.data?.error || 'Unable to load this hotel.'))
      .finally(() => setLoading(false))
  }, [id])

  const submit = async e => {
    e.preventDefault()
    setSubmitting(true)
    setErrors({})
    setError('')
    try {
      await api.post(`/staff/hotels/${id}/apply`, { message })
      navigate('/staff/my-applications')
    } catch (err) {
      if (err.response?.status === 422) setErrors(err.response.data.errors || {})
      else setError(err.response?.data?.error || 'Something went wrong.')
    } finally {
      setSubmitting(false)
    }
  }

  if (loading) return <Layout><p className="text-[#CFCBCA] text-center py-12">Loading…</p></Layout>
  if (error && !hotel) {
    return (
      <Layout>
        <div className="max-w-2xl mx-auto py-12">
          <Alert variant="error" dismissible={false}>{error}</Alert>
          <Link to="/staff/hotels" className="text-[#A0717F] mt-4 inline-block">Back to Browse Hotels</Link>
        </div>
      </Layout>
    )
  }

  return (
    <Layout>
      <div className="fixed top-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-12 max-w-4xl relative z-10">
        <div className="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
              Career Application
            </p>
            <h2 className="text-3xl lg:text-5xl font-bold font-serif text-[#EAD3CD] mb-2">{hotel.name}</h2>
            <p className="text-xs font-medium uppercase mt-2" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
              Applying for position: <strong className="text-[#EAD3CD]">{role.charAt(0).toUpperCase() + role.slice(1)}</strong>
            </p>
          </div>
          <Link to="/staff/hotels"
            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shrink-0"
            style={{ backgroundColor: 'transparent', color: '#CFCBCA', border: '1px solid rgba(234, 211, 205, 0.2)' }}
            onMouseOver={e => { e.currentTarget.style.backgroundColor = 'rgba(234, 211, 205, 0.05)'; e.currentTarget.style.color = '#EAD3CD' }}
            onMouseOut={e => { e.currentTarget.style.backgroundColor = 'transparent'; e.currentTarget.style.color = '#CFCBCA' }}>
            <Icon name="arrow-left" className="w-4 h-4" />
            Cancel Process
          </Link>
        </div>

        {error && <div className="mb-8"><Alert variant="error">{error}</Alert></div>}

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-1 space-y-6">
            <div className="rounded-2xl shadow-xl overflow-hidden bg-[#383537] border-t border-[rgba(234,211,205,0.05)] p-6 md:p-8">
              <h3 className="text-sm font-bold uppercase tracking-widest text-[#A0717F] mb-6">Property Profile</h3>
              <div className="space-y-6">
                <div>
                  <p className="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] mb-1 opacity-70">Region</p>
                  <p className="text-sm text-[#EAD3CD] flex items-center gap-2">
                    <Icon name="location" className="w-4 h-4 text-[#A0717F]" />
                    {hotel.city}, {hotel.country}
                  </p>
                </div>
                <div>
                  <p className="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] mb-1 opacity-70">Base Remuneration</p>
                  <p className="text-lg font-bold font-serif tracking-wide text-[#EAD3CD]">
                    {hotel.default_hourly_wage ? `$${Number(hotel.default_hourly_wage).toFixed(2)}/hr` : 'Discussed upon hire'}
                  </p>
                </div>
                <div>
                  <p className="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] mb-1 opacity-70">Volume</p>
                  <p className="text-sm text-[#EAD3CD]">{hotel.rooms_count} Total Suites</p>
                </div>
              </div>
            </div>

            {hotel.description && (
              <div className="rounded-2xl shadow-xl overflow-hidden bg-[#2A2729] border border-[rgba(234,211,205,0.03)] p-6">
                <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-3">About {hotel.name}</p>
                <p className="text-xs leading-relaxed text-[#CFCBCA] opacity-80">{hotel.description}</p>
              </div>
            )}
          </div>

          <div className="lg:col-span-2">
            <div className="rounded-2xl shadow-xl overflow-hidden bg-[#383537] border border-[rgba(234,211,205,0.05)] p-6 md:p-10">
              <h3 className="text-xl font-bold font-serif text-[#EAD3CD] mb-8">Letter of Intent</h3>

              <form onSubmit={submit}>
                <div className="mb-8">
                  <label className="block text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-3">Target Role</label>
                  <div className="w-full px-5 py-4 border rounded-xl text-sm font-semibold tracking-wide"
                    style={{ backgroundColor: 'rgba(26, 21, 21, 0.3)', borderColor: 'rgba(234, 211, 205, 0.1)', color: '#CFCBCA' }}>
                    {role.charAt(0).toUpperCase() + role.slice(1)} Professional
                  </div>
                  <p className="text-xs text-[#CFCBCA] opacity-60 mt-2">Your application will be locked to this specific staff classification.</p>
                </div>

                <div className="mb-10">
                  <label htmlFor="message" className="block text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-3">
                    Introduction / Cover Message
                  </label>
                  <textarea name="message" id="message" rows={5}
                    value={message} onChange={e => setMessage(e.target.value)}
                    className="w-full px-5 py-4 rounded-xl text-sm outline-none transition-all duration-300 resize-none font-serif"
                    style={{ backgroundColor: '#2A2729', border: '1px solid rgba(234, 211, 205, 0.1)', color: '#EAD3CD' }}
                    onFocus={e => { e.target.style.borderColor = '#A0717F'; e.target.style.boxShadow = '0 0 0 2px rgba(160,113,127,0.2)' }}
                    onBlur={e => { e.target.style.borderColor = 'rgba(234, 211, 205, 0.1)'; e.target.style.boxShadow = 'none' }}
                    placeholder="Express your interest, detail relevant past experience, or outline your availability..." />
                  {errors.message && <p className="text-red-400 text-xs mt-2 font-medium">{errors.message[0]}</p>}
                </div>

                <div className="border-t border-[rgba(234,211,205,0.05)] pt-8 flex items-center justify-between">
                  <p className="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] opacity-60 hidden sm:block">
                    Awaiting Submission
                  </p>
                  <button type="submit" disabled={submitting}
                    className="w-full sm:w-auto px-8 py-3.5 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all duration-300 shadow-xl disabled:opacity-60"
                    style={{ backgroundColor: '#A0717F', color: '#FFFFFF' }}
                    onMouseOver={e => { e.currentTarget.style.backgroundColor = '#b58290'; e.currentTarget.style.transform = 'translateY(-2px)' }}
                    onMouseOut={e => { e.currentTarget.style.backgroundColor = '#A0717F'; e.currentTarget.style.transform = 'translateY(0)' }}>
                    {submitting ? 'Submitting…' : 'Submit Cover Letter'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
