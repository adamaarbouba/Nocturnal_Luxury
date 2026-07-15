import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Layout from '../../components/Layout'
import Alert from '../../components/Alert'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import api from '../../lib/api'
import { money } from './util'

const cardHover = {
  onMouseOver: (e) => {
    e.currentTarget.style.transform = 'translateY(-6px)'
    e.currentTarget.style.boxShadow = '0 25px 50px rgba(160,113,127,0.15)'
  },
  onMouseOut: (e) => {
    e.currentTarget.style.transform = 'translateY(0)'
    e.currentTarget.style.boxShadow = '0 10px 15px rgba(0,0,0,0.1)'
  },
}

const tileHover = {
  onMouseOver: (e) => {
    e.currentTarget.style.backgroundColor = '#2E2530'
    e.currentTarget.style.borderColor = 'rgba(160, 113, 127, 0.4)'
    e.currentTarget.style.transform = 'translateY(-2px)'
  },
  onMouseOut: (e) => {
    e.currentTarget.style.backgroundColor = '#4E3B46'
    e.currentTarget.style.borderColor = 'rgba(160, 113, 127, 0.15)'
    e.currentTarget.style.transform = 'translateY(0)'
  },
}

const tileStyle = { backgroundColor: '#4E3B46', border: '1px solid rgba(160, 113, 127, 0.15)' }
const statCardStyle = { backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }

function StatBox({ title, value, icon, valueColor = '#EAD3CD', iconColor = '#A0717F' }) {
  return (
    <div className="rounded-2xl overflow-hidden shadow-lg transition-all duration-500" style={statCardStyle} {...cardHover}>
      <div className="p-6">
        <div className="flex items-center justify-between mb-4">
          <h3 className="text-xs font-medium uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>{title}</h3>
          <div className="p-2 rounded-lg" style={{ backgroundColor: '#2A2729' }}>
            <Icon name={icon} className="w-5 h-5" style={{ color: iconColor }} />
          </div>
        </div>
        <p className="text-4xl font-bold" style={{ color: valueColor, fontFamily: "'Georgia', serif" }}>{value}</p>
      </div>
    </div>
  )
}

export default function Dashboard() {
  const [data, setData] = useState(null)
  const [flash, setFlash] = useState(null)
  const navigate = useNavigate()

  const load = () =>
    api.get('/receptionist/dashboard')
      .then((res) => setData(res.data))
      .catch((err) => {
        if (err.response?.status === 403) navigate('/staff/hotels')
      })

  useEffect(() => { load() }, [])

  if (!data) return <Layout><div /></Layout>

  const { hotel, todayCheckIns, todayCheckOuts, occupiedRooms, pendingRefundRequests } = data

  const handleRefund = async (id, action) => {
    try {
      const res = await api.post(`/receptionist/refund-requests/${id}/${action}`)
      setFlash({ variant: 'success', message: res.data.message })
      load()
    } catch (err) {
      setFlash({ variant: 'error', message: err.response?.data?.message ?? 'Something went wrong.' })
    }
  }

  return (
    <Layout>
      <div className="fixed top-0 left-64 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.04)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="relative z-10">
        <Breadcrumbs links={[{ label: 'Receptionist Dashboard', url: '/receptionist/dashboard' }]} />

        <div className="mb-10">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
            Overview
          </p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
            Reception Atelier
          </h1>
        </div>

        {flash && <div className="mb-6"><Alert variant={flash.variant}>{flash.message}</Alert></div>}

        {/* Receptionist Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <StatBox title="Today's Check-ins" value={todayCheckIns} icon="check" />
          <StatBox title="Today's Check-outs" value={todayCheckOuts} icon="arrow-right" valueColor="#A0717F" />
          <StatBox title="Occupied Rooms" value={occupiedRooms} icon="building" />
        </div>

        {/* Receptionist Functions */}
        <div className="space-y-8">
          <div className="rounded-2xl shadow-2xl p-8" style={statCardStyle}>

            {pendingRefundRequests.length > 0 && (
              <div className="mb-10 rounded-2xl bg-[#2A2729] border border-[#A0717F] p-8 relative overflow-hidden group">
                <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                  <Icon name="sparkles" size="2xl" />
                </div>
                <div className="relative z-10">
                  <div className="flex items-center gap-3 mb-4">
                    <div className="w-2 h-2 rounded-full bg-[#A0717F] animate-pulse"></div>
                    <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-[#A0717F]">Action Required</h3>
                  </div>
                  <h4 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-6">Pending Refund Requests</h4>

                  <div className="space-y-4">
                    {pendingRefundRequests.map((request) => (
                      <div key={request.id} className="p-5 rounded-xl bg-[#383537] border border-[#4E3B46] hover:border-[#A0717F] transition-all group/item">
                        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                          <div>
                            <div className="flex items-center gap-2 mb-1">
                              <span className="text-[#EAD3CD] font-bold">Booking #{request.booking_id}</span>
                              <span className="text-[10px] text-[#4E3B46]">•</span>
                              <span className="text-[#CFCBCA] text-sm">{request.booking?.user?.name}</span>
                            </div>
                            <p className="text-xs text-[#4E3B46] italic mb-3">
                              "{request.reason?.length > 60 ? request.reason.slice(0, 60) + '...' : request.reason}"
                            </p>
                            <p className="text-lg font-bold text-[#A0717F]">${money(request.amount)}</p>
                          </div>
                          <div className="flex items-center gap-3">
                            <button type="button" onClick={() => handleRefund(request.id, 'approve')}
                              className="px-4 py-2 bg-[#A0717F] hover:bg-[#8F6470] text-white text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                              Approve
                            </button>
                            <button type="button" onClick={() => handleRefund(request.id, 'deny')}
                              className="px-4 py-2 border border-[#4E3B46] text-[#CFCBCA] hover:bg-[#4E3B46] text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                              Deny
                            </button>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}

            <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>Capabilities</p>
            <h3 className="text-2xl font-bold mb-8" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Front Desk Operations</h3>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <Link to="/receptionist/check-in" className="p-5 rounded-xl transition-all duration-300" style={tileStyle} {...tileHover}>
                <div className="flex items-center gap-3 mb-3">
                  <div className="p-2 rounded-lg" style={{ backgroundColor: '#383537' }}>
                    <Icon name="check" size="md" style={{ color: '#A0717F' }} />
                  </div>
                  <h4 className="text-lg font-semibold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Check-in Guest</h4>
                </div>
                <p className="text-sm" style={{ color: '#CFCBCA' }}>Process incoming guest arrivals.</p>
                {todayCheckIns > 0 && (
                  <p className="text-xs font-semibold mt-3 uppercase" style={{ color: '#A0717F', letterSpacing: '0.1em' }}>
                    {todayCheckIns} Pending
                  </p>
                )}
              </Link>

              <Link to="/receptionist/check-out" className="p-5 rounded-xl transition-all duration-300" style={tileStyle} {...tileHover}>
                <div className="flex items-center gap-3 mb-3">
                  <div className="p-2 rounded-lg" style={{ backgroundColor: '#383537' }}>
                    <Icon name="arrow-right" size="md" style={{ color: '#EAD3CD' }} />
                  </div>
                  <h4 className="text-lg font-semibold" style={{ color: '#A0717F', fontFamily: "'Georgia', serif" }}>Check-out Guest</h4>
                </div>
                <p className="text-sm" style={{ color: '#CFCBCA' }}>Finalize guest billings and departures.</p>
                {todayCheckOuts > 0 && (
                  <p className="text-xs font-semibold mt-3 uppercase" style={{ color: '#A0717F', letterSpacing: '0.1em' }}>
                    {todayCheckOuts} Pending
                  </p>
                )}
              </Link>

              <Link to="/receptionist/bookings" className="p-5 rounded-xl transition-all duration-300" style={tileStyle} {...tileHover}>
                <div className="flex items-center gap-3 mb-3">
                  <div className="p-2 rounded-lg" style={{ backgroundColor: '#383537' }}>
                    <Icon name="calendar" size="md" style={{ color: '#A0717F' }} />
                  </div>
                  <h4 className="text-lg font-semibold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>View Bookings</h4>
                </div>
                <p className="text-sm" style={{ color: '#CFCBCA' }}>See current reservations and history.</p>
              </Link>

              <Link to="/receptionist/bookings/create" className="p-5 rounded-xl transition-all duration-300" style={tileStyle} {...tileHover}>
                <div className="flex items-center gap-3 mb-3">
                  <div className="p-2 rounded-lg" style={{ backgroundColor: '#383537' }}>
                    <Icon name="plus" size="md" style={{ color: '#A0717F' }} />
                  </div>
                  <h4 className="text-lg font-semibold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Create Booking</h4>
                </div>
                <p className="text-sm" style={{ color: '#CFCBCA' }}>Book rooms for walk-ins or calls.</p>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
