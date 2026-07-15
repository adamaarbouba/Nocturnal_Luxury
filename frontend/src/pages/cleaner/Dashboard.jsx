import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Layout from '../../components/Layout'
import api from '../../lib/api'

// Ports resources/views/cleaner/dashboard.blade.php
export default function CleanerDashboard() {
  const navigate = useNavigate()
  const [staffRoles, setStaffRoles] = useState([])
  const [rooms, setRooms] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/cleaner/dashboard').then(res => {
      const { staffRoles, roomsNeedsCleaning } = res.data
      if (!staffRoles.length) { navigate('/staff/hotels'); return }
      setStaffRoles(staffRoles)
      setRooms(roomsNeedsCleaning)
    }).finally(() => setLoading(false))
  }, [navigate])

  if (loading) return <Layout><p className="text-[#CFCBCA] text-center py-12">Loading…</p></Layout>
  if (!staffRoles.length) {
    return (
      <Layout>
        <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow p-6 text-center">
          <p className="text-[#CFCBCA]">You have not been assigned to any hotel yet.</p>
        </div>
      </Layout>
    )
  }

  return (
    <Layout>
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-6 mb-8 border-t-4 border-t-[#A0717F]">
        <h3 className="text-sm font-semibold text-[#EAD3CD]">Rooms Needing Cleaning</h3>
        <p className="text-3xl font-bold mt-2 text-[#A0717F]">{rooms.length}</p>
      </div>

      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden">
        <div className="bg-[#2A2729] border-b border-[#4E3B46] px-6 py-4">
          <h3 className="text-lg font-semibold text-[#EAD3CD]">Assigned Cleaning Tasks</h3>
        </div>

        {rooms.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead className="bg-[#2A2729] border-b border-[#4E3B46]">
                <tr>
                  <th className="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Hotel</th>
                  <th className="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Room Number</th>
                  <th className="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Room Type</th>
                  <th className="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Capacity</th>
                  <th className="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Last Guest</th>
                  <th className="px-6 py-3 text-sm font-semibold text-[#CFCBCA]">Status</th>
                  <th className="px-6 py-3 text-center text-sm font-semibold text-[#CFCBCA]">Actions</th>
                </tr>
              </thead>
              <tbody>
                {rooms.map(room => {
                  const lastGuest = room.booking_items?.at(-1)?.booking?.user?.name
                  return (
                    <tr key={room.id} className="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                      <td className="px-6 py-4 text-[#A0717F] font-semibold">{room.hotel.name}</td>
                      <td className="px-6 py-4 font-semibold text-[#EAD3CD]">{room.room_number}</td>
                      <td className="px-6 py-4 text-[#CFCBCA]">{room.room_type}</td>
                      <td className="px-6 py-4 text-[#CFCBCA]">{room.capacity} guests</td>
                      <td className="px-6 py-4 text-[#CFCBCA]">{lastGuest || '—'}</td>
                      <td className="px-6 py-4">
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border border-[#A0717F] text-[#A0717F] bg-[#2A2729]">
                          Cleaning
                        </span>
                      </td>
                      <td className="px-6 py-4 text-center">
                        <Link to={`/cleaner/rooms/${room.id}/complete`}
                          className="bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] px-4 py-2 rounded transition inline-block">
                          Mark as Complete
                        </Link>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="px-6 py-12 text-center">
            <p className="text-[#EAD3CD] text-lg">All rooms are clean. Great job! 🎉</p>
          </div>
        )}
      </div>
    </Layout>
  )
}
