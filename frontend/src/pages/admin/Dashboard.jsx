import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import api from '../../lib/api'

const fmtDate = (s) => new Date(s).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
const ucfirst = (s) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : '')

const kpiCard = 'bg-[#383537] rounded-2xl p-8 border border-transparent shadow-sm hover:shadow-lg hover:border-[#4E3B46] transition'

export default function Dashboard() {
  const [data, setData] = useState(null)

  useEffect(() => {
    api.get('/admin/dashboard').then((res) => setData(res.data)).catch(() => setData({ error: true }))
  }, [])

  if (!data) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Loading…</div></Layout>
  if (data.error) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Failed to load dashboard.</div></Layout>

  const { stats, systemUsers, recentRequests } = data
  const total = stats.pendingRequests + stats.approvedRequests + stats.rejectedRequests
  const rate = total > 0 ? Math.round((stats.approvedRequests / total) * 100) : 0

  return (
    <Layout>
      <div style={{ backgroundColor: 'transparent', minHeight: '100vh' }}>
        {/* Breadcrumb Navigation */}
        <div className="max-w-7xl mx-auto px-4 pt-8 pb-2">
          <Breadcrumbs links={[
            { label: 'Admin', url: '/admin' },
            { label: 'Dashboard', url: '/admin' },
          ]} />
        </div>

        {/* Page Header */}
        <div className="max-w-7xl mx-auto px-4 pb-8">
          <div>
            <h1 className="text-4xl font-bold text-[#EAD3CD]">Dashboard</h1>
            <p className="text-sm text-[#CFCBCA] mt-1">System overview and management</p>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 pb-12">
          {/* KPI Row */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {[
              ['Total Hotels', stats.totalHotels, 'Active properties'],
              ['Total Users', stats.totalUsers, 'Registered members'],
              ['Total Bookings', stats.totalBookings, 'All-time total'],
              ['Approval Rate', `${rate}%`, 'Hotel requests'],
            ].map(([label, value, sub]) => (
              <div key={label} className={kpiCard}>
                <div className="flex justify-between items-start">
                  <div>
                    <p className="text-xs uppercase tracking-wider text-[#CFCBCA] font-medium">{label}</p>
                    <p className="text-4xl font-bold text-[#EAD3CD] mt-4">{value}</p>
                    <p className="text-xs text-[#CFCBCA] mt-2">{sub}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* Main Content Grid */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* Left Column */}
            <div className="lg:col-span-2 space-y-6">
              {/* Request Status Breakdown */}
              <div className="bg-[#383537] rounded-2xl p-8 border border-transparent shadow-sm hover:border-[#4E3B46] transition">
                <h3 className="text-lg font-bold text-[#EAD3CD] mb-6">Request Status Breakdown</h3>
                <div className="grid grid-cols-3 gap-4">
                  {[
                    [stats.pendingRequests, 'Pending'],
                    [stats.approvedRequests, 'Approved'],
                    [stats.rejectedRequests, 'Rejected'],
                  ].map(([value, label]) => (
                    <div key={label} className="text-center p-4 rounded-lg" style={{ backgroundColor: '#2A2729' }}>
                      <p className="text-3xl font-bold text-[#A0717F]">{value}</p>
                      <p className="text-xs text-[#CFCBCA] mt-2">{label}</p>
                    </div>
                  ))}
                </div>
              </div>

              {/* Recent Hotel Requests */}
              <div className="bg-[#383537] rounded-2xl border border-transparent shadow-sm overflow-hidden hover:border-[#4E3B46] transition">
                <div className="px-8 py-6 border-b border-[#4E3B46]" style={{ backgroundColor: '#2A2729' }}>
                  <h3 className="text-lg font-bold text-[#EAD3CD]">Recent Hotel Requests</h3>
                </div>
                {recentRequests.length > 0 ? (
                  <div className="divide-y divide-[#4E3B46]">
                    {recentRequests.map((request) => (
                      <div key={request.id} className="px-8 py-4 hover:bg-[#2A2729] transition">
                        <div className="flex justify-between items-start">
                          <div className="flex-1">
                            <h4 className="font-semibold text-[#EAD3CD]">{request.name}</h4>
                            <p className="text-xs text-[#CFCBCA] mt-1">
                              by <strong>{request.owner?.name}</strong> • {fmtDate(request.created_at)}
                            </p>
                          </div>
                          <span
                            className="text-xs px-3 py-1.5 rounded-full font-medium"
                            style={{ backgroundColor: request.status === 'approved' ? '#4E3B46' : '#2A2729', color: '#A0717F' }}
                          >
                            {ucfirst(request.status)}
                          </span>
                        </div>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="px-8 py-12 text-center text-[#CFCBCA]">
                    <p>No recent requests</p>
                  </div>
                )}
              </div>
            </div>

            {/* Right Column */}
            <div className="space-y-6">
              {/* Recent Users */}
              <div className="bg-[#383537] rounded-2xl p-8 border border-transparent shadow-sm hover:border-[#4E3B46] transition">
                <h3 className="text-lg font-bold text-[#EAD3CD] mb-6">Recent Users</h3>
                {systemUsers.length > 0 ? (
                  <div className="space-y-4">
                    {systemUsers.slice(0, 5).map((user) => (
                      <div key={user.id} className="flex items-center justify-between pb-4 border-b border-[#4E3B46] last:border-b-0">
                        <div>
                          <p className="text-sm font-medium text-[#EAD3CD]">{user.name}</p>
                          <p className="text-xs text-[#CFCBCA]">{ucfirst(user.role?.slug)}</p>
                        </div>
                        <span className="text-xs px-2 py-1 rounded" style={{ backgroundColor: '#2A2729', color: '#CFCBCA' }}>
                          {new Date(user.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit' })}
                        </span>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-sm text-[#CFCBCA]">No users found</p>
                )}
              </div>

              {/* Quick Actions */}
              <div className="bg-[#383537] rounded-2xl p-8 border border-transparent shadow-sm hover:border-[#4E3B46] transition">
                <h3 className="text-lg font-bold text-[#EAD3CD] mb-4">Quick Actions</h3>
                <div className="space-y-3">
                  <Link
                    to="/admin/hotel-requests"
                    className="w-full block px-4 py-3 rounded-lg font-medium text-white transition-colors text-center bg-[#A0717F] hover:bg-[#8F6470]"
                  >
                    Review Requests
                  </Link>
                  <Link
                    to="/admin/users"
                    className="w-full block px-4 py-3 rounded-lg font-medium border border-[#4E3B46] text-[#CFCBCA] text-center hover:bg-[#2A2729] transition"
                  >
                    Manage Users
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
