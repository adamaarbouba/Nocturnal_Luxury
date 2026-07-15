import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import api from '../../../lib/api'
import { money, timeAgo, ucfirst } from '../format'

const roleCls = (role) =>
  role === 'cleaner' ? 'text-blue-400 border-blue-500' : role === 'inspector' ? 'text-purple-400 border-purple-500' : 'text-green-400 border-green-500'

// Ports resources/views/owner/staff/applications.blade.php
export default function StaffApplications() {
  const [data, setData] = useState(null)
  const [rates, setRates] = useState({})

  const load = () => api.get('/owner/staff/applications').then((res) => setData(res.data)).catch(() => {})

  useEffect(() => { load() }, [])

  const approve = async (e, app) => {
    e.preventDefault()
    try {
      await api.post(`/owner/staff/applications/${app.id}/approve`, { hourly_rate: rates[app.id] ?? app.hotel.default_hourly_wage ?? '15.00' })
      load()
    } catch {
      // ponytail: inline errors skipped, list refresh reflects failure to change state
    }
  }

  const reject = async (app) => {
    if (!confirm('Reject this application?')) return
    await api.post(`/owner/staff/applications/${app.id}/reject`)
    load()
  }

  if (!data) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  const { pendingApplications, reviewedApplications } = data

  return (
    <Layout>
      <Breadcrumbs links={[{ label: 'Owner Dashboard', url: '/owner/dashboard' }, { label: 'Staff Applications', url: '#' }]} />

      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
          <h2 className="text-3xl font-bold text-[#EAD3CD]">Staff Applications</h2>
          <p className="text-[#CFCBCA] mt-1">Review and manage staff applications for your hotels</p>
        </div>
        <Link to="/owner/dashboard" className="border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold px-5 py-2 rounded transition text-sm">
          ← Back to Dashboard
        </Link>
      </div>

      {/* Pending Applications */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden mb-8">
        <div className="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
          <h3 className="text-lg font-semibold text-[#EAD3CD]">
            Pending Applications
            {pendingApplications.length > 0 && (
              <span className="inline-flex items-center justify-center w-6 h-6 ml-2 text-xs font-bold text-white rounded-full bg-[#A0717F]">
                {pendingApplications.length}
              </span>
            )}
          </h3>
        </div>

        {pendingApplications.length > 0 ? (
          <div className="divide-y divide-[#4E3B46]">
            {pendingApplications.map((application) => (
              <div key={application.id} className="px-6 py-5 hover:bg-[#2A2729]/50 transition border-t border-[#4E3B46]">
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div className="flex-1">
                    <div className="flex items-center gap-3 mb-2">
                      <h4 className="text-lg font-semibold text-[#EAD3CD]">{application.user.name}</h4>
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold border bg-[#2A2729] ${roleCls(application.role)}`}>
                        {ucfirst(application.role)}
                      </span>
                    </div>
                    <p className="text-sm text-[#CFCBCA] mb-1"><strong className="text-[#EAD3CD]">Hotel:</strong> {application.hotel.name}</p>
                    <p className="text-sm text-[#CFCBCA] mb-1"><strong className="text-[#EAD3CD]">Email:</strong> {application.user.email}</p>
                    {application.message && <p className="text-sm text-[#CFCBCA] mt-2 italic">"{application.message}"</p>}
                    <p className="text-xs text-[#4E3B46] mt-2">Applied {timeAgo(application.created_at)}</p>
                  </div>

                  <div className="flex flex-col gap-2 min-w-[200px]">
                    <form onSubmit={(e) => approve(e, application)} className="flex flex-col gap-2">
                      <label className="text-xs text-[#CFCBCA] font-semibold">Hourly Rate ($)</label>
                      <input type="number" step="0.01" min="0.01"
                        defaultValue={application.hotel.default_hourly_wage ?? '15.00'}
                        onChange={(e) => setRates({ ...rates, [application.id]: e.target.value })}
                        className="px-3 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded text-sm focus:outline-none focus:ring-1 focus:ring-[#A0717F]" />
                      <button type="submit" className="text-white font-semibold px-4 py-2 rounded transition text-sm bg-[#A0717F] hover:bg-[#8F6470]">
                        Approve
                      </button>
                    </form>
                    <button onClick={() => reject(application)} className="w-full text-white font-semibold px-4 py-2 rounded transition text-sm bg-[#4E3B46] hover:bg-red-700">
                      Reject
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="px-6 py-12 text-center">
            <p className="text-[#CFCBCA]">No pending applications at this time.</p>
          </div>
        )}
      </div>

      {/* Recently Reviewed Applications */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden">
        <div className="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
          <h3 className="text-lg font-semibold text-[#EAD3CD]">Recently Reviewed</h3>
        </div>

        {reviewedApplications.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead className="bg-[#2A2729] border-b border-[#4E3B46]">
                <tr>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Applicant</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Hotel</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Role</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Status</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Rate</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Reviewed</th>
                </tr>
              </thead>
              <tbody>
                {reviewedApplications.map((app) => (
                  <tr key={app.id} className="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                    <td className="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">{app.user.name}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{app.hotel.name}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{ucfirst(app.role)}</td>
                    <td className="px-6 py-4 text-sm">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold border bg-[#2A2729] ${app.status === 'approved' ? 'text-green-400 border-green-500' : 'text-red-400 border-red-500'}`}>
                        {ucfirst(app.status)}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{app.hourly_rate ? `$${money(app.hourly_rate)}` : '—'}</td>
                    <td className="px-6 py-4 text-sm text-[#4E3B46]">{app.reviewed_at ? timeAgo(app.reviewed_at) : '—'}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="px-6 py-12 text-center">
            <p className="text-[#CFCBCA]">No reviewed applications yet.</p>
          </div>
        )}
      </div>
    </Layout>
  )
}
