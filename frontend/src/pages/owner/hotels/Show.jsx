import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import Layout from '../../../components/Layout'
import Icon from '../../../components/Icon'
import Alert from '../../../components/Alert'
import api from '../../../lib/api'
import { money } from '../format'

// Ports resources/views/owner/hotels/show.blade.php
export default function HotelShow() {
  const { id } = useParams()
  const [hotel, setHotel] = useState(null)
  const [form, setForm] = useState({ name: '', default_hourly_wage: '' })
  const [errors, setErrors] = useState({})
  const [flash, setFlash] = useState(null)

  useEffect(() => {
    api.get(`/owner/hotels/${id}`).then((res) => {
      setHotel(res.data.hotel)
      setForm({ name: res.data.hotel.name ?? '', default_hourly_wage: res.data.hotel.default_hourly_wage ?? '' })
    }).catch(() => {})
  }, [id])

  const submitEdit = async (e) => {
    e.preventDefault()
    setErrors({})
    try {
      const res = await api.patch(`/owner/hotels/${id}/update`, form)
      setHotel(res.data.hotel)
      setFlash('Hotel updated successfully.')
    } catch (err) {
      setErrors(err.response?.data?.errors ?? {})
    }
  }

  if (!hotel) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  return (
    <Layout>
      {/* Header Card */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
          <div>
            <h2 className="text-3xl font-bold text-[#EAD3CD] mb-1">{hotel.name}</h2>
            <p className="text-[#CFCBCA] text-sm">Manage your hotel information, staff, and rooms</p>
          </div>
          <div className="flex flex-wrap gap-2">
            <Link to={`/owner/hotels/${hotel.id}/staff`} className="bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition text-sm whitespace-nowrap flex items-center gap-2">
              <Icon name="user" size="sm" className="text-white" />
              Manage Staff
            </Link>
            <Link to={`/owner/hotels/${hotel.id}/manage`} className="border border-[#A0717F] text-[#A0717F] hover:bg-[#A0717F] hover:text-white font-semibold px-6 py-2 rounded transition text-sm whitespace-nowrap">
              Manage Rooms
            </Link>
            <Link to="/owner/hotels" className="border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold px-6 py-2 rounded transition text-sm whitespace-nowrap">
              Back to Hotels
            </Link>
          </div>
        </div>
      </div>

      {flash && <Alert variant="success" className="mb-8">{flash}</Alert>}

      {/* Edit Hotel Form (Name + Hourly Wage only) */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <h3 className="text-2xl font-bold text-[#EAD3CD] mb-6">Edit Hotel</h3>
        <form onSubmit={submitEdit}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label htmlFor="name" className="text-[#CFCBCA] font-semibold text-sm">Hotel Name</label>
              <input
                type="text"
                name="name"
                id="name"
                value={form.name}
                onChange={(e) => setForm({ ...form, name: e.target.value })}
                className="w-full mt-1 px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
              />
              {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name[0]}</p>}
            </div>
            <div>
              <label htmlFor="default_hourly_wage" className="text-[#CFCBCA] font-semibold text-sm">Default Hourly Wage ($)</label>
              <input
                type="number"
                name="default_hourly_wage"
                id="default_hourly_wage"
                value={form.default_hourly_wage}
                onChange={(e) => setForm({ ...form, default_hourly_wage: e.target.value })}
                step="0.01"
                min="0"
                placeholder="e.g. 15.00"
                className="w-full mt-1 px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
              />
              <p className="text-[#4E3B46] text-xs mt-1">Staff will see this when browsing hotels to apply</p>
              {errors.default_hourly_wage && <p className="text-red-500 text-xs mt-1">{errors.default_hourly_wage[0]}</p>}
            </div>
          </div>
          <button type="submit" className="bg-[#2A2729] hover:bg-[#4E3B46] border border-[#4E3B46] text-[#EAD3CD] font-semibold px-6 py-2 rounded transition text-sm">
            Save Changes
          </button>
        </form>
      </div>

      {/* Hotel Information Card */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <h3 className="text-2xl font-bold text-[#EAD3CD] mb-6">Hotel Information</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <p className="text-[#CFCBCA] font-semibold text-sm">Location</p>
            <p className="text-[#EAD3CD] text-lg mt-1">{hotel.city}, {hotel.country}</p>
          </div>
          <div>
            <p className="text-[#CFCBCA] font-semibold text-sm">Email</p>
            <p className="text-[#EAD3CD] text-lg mt-1">{hotel.email ?? 'N/A'}</p>
          </div>
          <div>
            <p className="text-[#CFCBCA] font-semibold text-sm">Phone</p>
            <p className="text-[#EAD3CD] text-lg mt-1">{hotel.phone ?? 'N/A'}</p>
          </div>
          <div>
            <p className="text-[#CFCBCA] font-semibold text-sm">Status</p>
            <p className="text-[#EAD3CD] text-lg mt-1">
              <span className="inline-block px-3 py-1 rounded-full text-xs font-semibold text-[#A0717F] border border-[#A0717F] bg-[#2A2729]">
                {hotel.is_verified ? 'Verified' : 'Pending'}
              </span>
            </p>
          </div>
          <div className="md:col-span-2">
            <p className="text-[#CFCBCA] font-semibold text-sm">Address</p>
            <p className="text-[#EAD3CD] text-lg mt-1">{hotel.address ?? 'N/A'}</p>
          </div>
          <div className="md:col-span-2">
            <p className="text-[#CFCBCA] font-semibold text-sm">Description</p>
            <p className="text-[#EAD3CD] text-lg mt-1">{hotel.description ?? 'No description provided'}</p>
          </div>
          <div>
            <p className="text-[#CFCBCA] font-semibold text-sm">Total Rooms</p>
            <p className="text-[#EAD3CD] text-lg mt-1">{hotel.rooms?.length ?? 0}</p>
          </div>
          <div>
            <p className="text-[#CFCBCA] font-semibold text-sm">Rating</p>
            <p className="text-[#EAD3CD] text-lg mt-1">{hotel.rating ?? 'N/A'}/5.0</p>
          </div>
          <div>
            <p className="text-[#CFCBCA] font-semibold text-sm">Default Hourly Wage</p>
            <p className="text-lg mt-1 font-semibold text-[#A0717F]">
              {hotel.default_hourly_wage ? `$${money(hotel.default_hourly_wage)}/hr` : 'Not set'}
            </p>
          </div>
        </div>
      </div>

      {/* Staff Section */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-2xl font-bold text-[#EAD3CD]">Hotel Staff</h3>
          <Link to={`/owner/hotels/${hotel.id}/staff`} className="text-sm font-semibold hover:underline text-[#A0717F]">
            Full Staff Management →
          </Link>
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
                </tr>
              </thead>
              <tbody>
                {hotel.staff.filter((s) => !s.banned_at).map((staff) => (
                  <tr key={staff.id} className="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                    <td className="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">{staff.name}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{staff.email}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{staff.pivot.role.charAt(0).toUpperCase() + staff.pivot.role.slice(1)}</td>
                    <td className="px-6 py-4 text-sm text-[#A0717F] font-semibold">${money(staff.pivot.hourly_rate)}</td>
                    <td className="px-6 py-4 text-sm">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold border bg-[#2A2729] ${staff.pivot.is_available ? 'text-green-400 border-green-500' : 'text-red-400 border-red-500'}`}>
                        {staff.pivot.is_available ? 'Available' : 'Unavailable'}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <p className="text-[#CFCBCA] text-center py-8">No staff assigned to this hotel yet.</p>
        )}
      </div>

      {/* Receptionists Section */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
        <h3 className="text-2xl font-bold text-[#EAD3CD] mb-6">Receptionists</h3>

        {hotel.receptionists?.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead className="bg-[#2A2729] border-b border-[#4E3B46]">
                <tr>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Name</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Email</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Phone</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Status</th>
                </tr>
              </thead>
              <tbody>
                {hotel.receptionists.filter((r) => r.user && !r.user.banned_at).map((receptionist) => (
                  <tr key={receptionist.id} className="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                    <td className="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">{receptionist.user.name}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{receptionist.user.email}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{receptionist.user.phone ?? 'N/A'}</td>
                    <td className="px-6 py-4 text-sm">
                      <span className="inline-block px-3 py-1 rounded-full text-xs font-semibold text-blue-400 border border-blue-500 bg-[#2A2729]">
                        Active
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <p className="text-[#CFCBCA] text-center py-8">No receptionists assigned to this hotel yet.</p>
        )}
      </div>
    </Layout>
  )
}
