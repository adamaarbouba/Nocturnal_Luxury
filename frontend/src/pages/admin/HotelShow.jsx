import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import api from '../../lib/api'

export default function HotelShow() {
  const { id } = useParams()
  const [hotel, setHotel] = useState(null)

  useEffect(() => {
    api.get(`/admin/hotels/${id}`).then((res) => setHotel(res.data.hotel)).catch(() => setHotel({ error: true }))
  }, [id])

  if (!hotel) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Loading…</div></Layout>
  if (hotel.error) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Failed to load hotel.</div></Layout>

  return (
    <Layout>
      {/* Page Header */}
      <div
        className="mb-8 border-b border-[#4E3B46] flex justify-between items-start"
        style={{ background: 'linear-gradient(180deg, rgba(42, 39, 41, 0.5) 0%, transparent 100%)' }}
      >
        <div className="py-6">
          <h1 className="text-3xl font-semibold text-[#EAD3CD]">{hotel.name}</h1>
          <p className="text-sm mt-2 text-[#CFCBCA]">Owner: {hotel.owner?.name}</p>
        </div>
        <Link to={`/admin/users/${hotel.owner?.id}`} className="px-4 py-2 text-sm border border-[#4E3B46] text-[#CFCBCA] rounded-lg hover:bg-[#2A2729] transition">
          View Owner
        </Link>
      </div>

      {/* Hotel Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div className="border border-[#4E3B46] rounded-lg p-6 bg-[#383537]">
          <p className="text-xs uppercase tracking-wider text-[#CFCBCA] font-medium">Total Rooms</p>
          <p className="text-3xl font-semibold text-[#EAD3CD] mt-3">{hotel.rooms?.length ?? 0}</p>
          <p className="text-xs text-[#CFCBCA] mt-1">Available properties</p>
        </div>

        <div className="border border-[#4E3B46] rounded-lg p-6 bg-[#383537]">
          <p className="text-xs uppercase tracking-wider text-[#CFCBCA] font-medium">Status</p>
          <div className="mt-3">
            {hotel.verified ? (
              <span className="inline-block px-2.5 py-1 text-xs bg-green-900/50 text-green-300 rounded font-medium border border-green-800">Verified</span>
            ) : (
              <span className="inline-block px-2.5 py-1 text-xs bg-yellow-900/50 text-yellow-300 rounded font-medium border border-yellow-800">Pending</span>
            )}
          </div>
          <p className="text-xs text-[#CFCBCA] mt-2">Verification state</p>
        </div>

        <div className="border border-[#4E3B46] rounded-lg p-6 bg-[#383537]">
          <p className="text-xs uppercase tracking-wider text-[#CFCBCA] font-medium">Contact</p>
          <p className="text-sm font-medium text-[#EAD3CD] mt-3">{hotel.email ?? 'N/A'}</p>
          <p className="text-xs text-[#CFCBCA] mt-1">{hotel.phone ?? 'N/A'}</p>
        </div>
      </div>

      {/* Hotel Information */}
      <div className="border border-[#4E3B46] rounded-lg p-6 mb-8 bg-[#383537]">
        <h3 className="text-sm font-semibold text-[#EAD3CD] uppercase tracking-wider mb-4">Hotel Details</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="border-b border-[#4E3B46] pb-4">
            <p className="text-xs text-[#CFCBCA] font-medium">Location</p>
            <p className="text-sm text-[#EAD3CD] mt-2">{hotel.location ?? 'Not specified'}</p>
          </div>
          <div className="border-b border-[#4E3B46] pb-4">
            <p className="text-xs text-[#CFCBCA] font-medium">Address</p>
            <p className="text-sm text-[#EAD3CD] mt-2">{hotel.address ?? 'Not specified'}</p>
          </div>
          <div className="border-b border-[#4E3B46] pb-4">
            <p className="text-xs text-[#CFCBCA] font-medium">Email</p>
            <p className="text-sm text-[#EAD3CD] mt-2">{hotel.email ?? 'N/A'}</p>
          </div>
          <div className="border-b border-[#4E3B46] pb-4">
            <p className="text-xs text-[#CFCBCA] font-medium">Phone</p>
            <p className="text-sm text-[#EAD3CD] mt-2">{hotel.phone ?? 'N/A'}</p>
          </div>
        </div>

        {hotel.description && (
          <div className="border-t border-[#4E3B46] mt-6 pt-6">
            <p className="text-xs text-[#CFCBCA] font-medium">Description</p>
            <p className="text-sm text-[#EAD3CD] mt-2">{hotel.description}</p>
          </div>
        )}
      </div>

      {/* Rooms Table */}
      <div className="border border-[#4E3B46] rounded-lg overflow-hidden bg-[#383537]">
        <div className="p-6 border-b border-[#4E3B46]" style={{ backgroundColor: '#2A2729' }}>
          <h3 className="text-sm font-semibold text-[#EAD3CD] uppercase tracking-wider">Room Inventory</h3>
        </div>

        {hotel.rooms?.length > 0 ? (
          <table className="w-full text-sm">
            <thead style={{ backgroundColor: '#2A2729' }}>
              <tr className="border-b border-[#4E3B46]">
                <th className="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">Room</th>
                <th className="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">Type</th>
                <th className="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">Capacity</th>
                <th className="px-6 py-3 text-left text-xs font-semibold text-[#CFCBCA] uppercase tracking-wide">Price</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#4E3B46]">
              {hotel.rooms.map((room) => (
                <tr key={room.id} className="hover:bg-[#2A2729] transition">
                  <td className="px-6 py-3 font-medium text-[#EAD3CD]">{room.room_number}</td>
                  <td className="px-6 py-3 text-[#CFCBCA]">{room.room_type?.charAt(0).toUpperCase() + room.room_type?.slice(1)}</td>
                  <td className="px-6 py-3 text-[#CFCBCA]">{room.capacity} guests</td>
                  <td className="px-6 py-3 font-medium text-[#A0717F]">${Number(room.price_per_night).toFixed(2)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        ) : (
          <div className="p-12 text-center" style={{ backgroundColor: '#2A2729' }}>
            <p className="text-[#EAD3CD] font-semibold">No rooms available</p>
            <p className="text-xs text-[#CFCBCA] mt-1">This hotel has no rooms listed yet</p>
          </div>
        )}
      </div>
    </Layout>
  )
}
