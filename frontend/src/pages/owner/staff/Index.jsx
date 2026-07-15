import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import Alert from '../../../components/Alert'
import api from '../../../lib/api'
import { money } from '../format'

// Ports resources/views/owner/staff/index.blade.php
export default function StaffIndex() {
  const { id } = useParams()
  const [hotel, setHotel] = useState(null)
  const [wages, setWages] = useState({})
  const [flash, setFlash] = useState(null)

  const load = () => api.get(`/owner/hotels/${id}/staff`).then((res) => setHotel(res.data.hotel)).catch(() => {})

  useEffect(() => { load() }, [id])

  const saveWage = async (userId) => {
    try {
      const res = await api.patch(`/owner/hotels/${id}/staff/${userId}/wage`, { hourly_rate: wages[userId] })
      setFlash(res.data.message)
      load()
    } catch (err) {
      setFlash(err.response?.data?.message || 'Error updating wage')
    }
  }

  const removeStaff = async (userId, name) => {
    if (!confirm(`Remove ${name} from this hotel?`)) return
    try {
      const res = await api.delete(`/owner/hotels/${id}/staff/${userId}`)
      setFlash(res.data.message)
      load()
    } catch (err) {
      setFlash(err.response?.data?.message || 'Error removing staff')
    }
  }

  if (!hotel) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  return (
    <Layout>
      <Breadcrumbs links={[
        { label: 'Owner Dashboard', url: '/owner/dashboard' },
        { label: hotel.name, url: `/owner/hotels/${hotel.id}` },
        { label: 'Staff', url: '#' },
      ]} />

      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
          <h2 className="text-3xl font-bold text-[#EAD3CD]">Staff — {hotel.name}</h2>
          <p className="text-[#CFCBCA] mt-1">Manage staff members assigned to this hotel</p>
        </div>
        <div className="flex gap-2">
          <Link to="/owner/staff/applications" className="flex items-center gap-2 bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-5 py-2 rounded-lg transition text-sm shadow-md">
            <Icon name="user" className="w-4 h-4" /> View Applications
          </Link>
          <Link to={`/owner/hotels/${hotel.id}`} className="border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold px-5 py-2 rounded transition text-sm">
            ← Back to Hotel
          </Link>
        </div>
      </div>

      {flash && <Alert variant="success" className="mb-6">{flash}</Alert>}

      {/* Freelance Staff (Cleaners & Inspectors) */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden mb-8">
        <div className="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
          <h3 className="text-lg font-semibold text-[#EAD3CD]">Freelance Staff (Cleaners & Inspectors)</h3>
        </div>

        {hotel.staff?.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead className="bg-[#2A2729] border-b border-[#4E3B46]">
                <tr>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Name</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Email</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Role</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Hourly Rate</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Status</th>
                  <th className="px-6 py-3 text-center text-xs font-semibold text-[#CFCBCA]">Actions</th>
                </tr>
              </thead>
              <tbody>
                {hotel.staff.map((staff) => (
                  <tr key={staff.id} className="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                    <td className="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">{staff.name}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{staff.email}</td>
                    <td className="px-6 py-4 text-sm">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold border bg-[#2A2729] ${staff.pivot.role === 'cleaner' ? 'text-blue-400 border-blue-500' : 'text-purple-400 border-purple-500'}`}>
                        {staff.pivot.role.charAt(0).toUpperCase() + staff.pivot.role.slice(1)}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">
                      <div className="flex items-center gap-2">
                        <span className="text-[#CFCBCA]">$</span>
                        <input type="number" step="0.01" min="0"
                          defaultValue={staff.pivot.hourly_rate}
                          onChange={(e) => setWages({ ...wages, [staff.id]: e.target.value })}
                          className="w-20 px-2 py-1 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded text-sm focus:outline-none focus:ring-1 focus:ring-[#A0717F]" />
                        <button onClick={() => saveWage(staff.id)} className="text-xs text-[#EAD3CD] bg-[#4E3B46] hover:bg-[#68525F] px-2 py-1 rounded transition">
                          Save
                        </button>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold border bg-[#2A2729] ${staff.pivot.is_available ? 'text-green-400 border-green-500' : 'text-red-400 border-red-500'}`}>
                        {staff.pivot.is_available ? 'Available' : 'Unavailable'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-center">
                      <button onClick={() => removeStaff(staff.id, staff.name)} className="text-[#A0717F] hover:text-red-500 text-sm font-semibold transition">
                        Remove
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="px-6 py-12 text-center">
            <p className="text-[#CFCBCA]">No freelance staff assigned yet. Staff can apply from their dashboards.</p>
          </div>
        )}
      </div>

      {/* Receptionists */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden">
        <div className="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
          <h3 className="text-lg font-semibold text-[#EAD3CD]">Receptionists</h3>
        </div>

        {hotel.receptionists?.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead className="bg-[#2A2729] border-b border-[#4E3B46]">
                <tr>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Name</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Email</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Phone</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Status</th>
                  <th className="px-6 py-3 text-center text-xs font-semibold text-[#CFCBCA]">Actions</th>
                </tr>
              </thead>
              <tbody>
                {hotel.receptionists.filter((r) => r.user && !r.user.banned_at).map((receptionist) => (
                  <tr key={receptionist.id} className="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                    <td className="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">{receptionist.user.name}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{receptionist.user.email}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{receptionist.user.phone ?? 'N/A'}</td>
                    <td className="px-6 py-4 text-sm">
                      <span className="inline-block px-3 py-1 rounded-full text-xs font-semibold border border-blue-500 text-blue-400 bg-[#2A2729]">
                        Active
                      </span>
                    </td>
                    <td className="px-6 py-4 text-center">
                      <button onClick={() => removeStaff(receptionist.user.id, receptionist.user.name)} className="text-[#A0717F] hover:text-red-500 text-sm font-semibold transition">
                        Remove
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="px-6 py-12 text-center">
            <p className="text-[#CFCBCA]">No receptionists assigned yet. Receptionists can apply from their dashboards.</p>
          </div>
        )}
      </div>
    </Layout>
  )
}
