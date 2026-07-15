import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../../components/Layout'
import api from '../../../lib/api'

// Ports resources/views/owner/hotels/index.blade.php
export default function HotelsIndex() {
  const [hotels, setHotels] = useState(null)

  useEffect(() => {
    api.get('/owner/hotels').then((res) => setHotels(res.data)).catch(() => {})
  }, [])

  if (!hotels) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  const items = hotels.data ?? []

  return (
    <Layout>
      <div className="flex justify-between items-center mb-8">
        <h2 className="text-3xl font-bold text-[#EAD3CD]">My Hotels</h2>
        <div className="flex gap-4">
          <Link to="/owner/hotel-requests/create" className="bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition">
            Add New Hotel
          </Link>
          <Link to="/owner/dashboard" className="border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold px-6 py-2 rounded transition">
            Back to Dashboard
          </Link>
        </div>
      </div>

      {items.length > 0 ? (
        <>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {items.map((hotel) => (
              <div key={hotel.id} className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                <div className="bg-[#2A2729] border-b border-[#4E3B46] p-6">
                  <h3 className="text-xl font-bold text-[#EAD3CD]">{hotel.name}</h3>
                  <p className="text-[#A0717F] text-sm mt-1">{hotel.location ?? 'Location not specified'}</p>
                </div>
                <div className="p-6">
                  <div className="grid grid-cols-2 gap-4 mb-6">
                    <div className="bg-[#2A2729] border border-[#4E3B46] p-4 rounded-lg">
                      <p className="text-[#CFCBCA] text-xs font-semibold">Total Rooms</p>
                      <p className="text-2xl font-bold text-[#A0717F] mt-2">{hotel.rooms?.length ?? 0}</p>
                    </div>
                    <div className="bg-[#2A2729] border border-[#4E3B46] p-4 rounded-lg">
                      <p className="text-[#CFCBCA] text-xs font-semibold">Bookings</p>
                      <p className="text-2xl font-bold text-[#EAD3CD] mt-2">{hotel.bookings_count ?? 0}</p>
                    </div>
                  </div>

                  <div className="space-y-3 mb-6 text-sm border-t border-[#4E3B46] pt-4">
                    <div>
                      <p className="text-[#CFCBCA] font-semibold">Email</p>
                      <p className="text-[#EAD3CD]">{hotel.email ?? 'N/A'}</p>
                    </div>
                    <div>
                      <p className="text-[#CFCBCA] font-semibold">Phone</p>
                      <p className="text-[#EAD3CD]">{hotel.phone ?? 'N/A'}</p>
                    </div>
                    <div>
                      <p className="text-[#CFCBCA] font-semibold">Address</p>
                      <p className="text-[#EAD3CD]">{hotel.address ?? 'N/A'}</p>
                    </div>
                  </div>

                  <div className="pt-4 border-t border-[#4E3B46] flex gap-2">
                    <Link to={`/owner/hotels/${hotel.id}`} className="flex-1 text-center bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] font-semibold py-2 rounded transition text-sm">
                      View
                    </Link>
                    <Link to={`/owner/hotels/${hotel.id}/manage`} className="flex-1 text-center bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold py-2 rounded transition text-sm">
                      Manage
                    </Link>
                  </div>
                </div>
              </div>
            ))}
          </div>
          {/* ponytail: pagination controls omitted, add Table-style pager if owners exceed 10 hotels often */}
        </>
      ) : (
        <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-12 text-center">
          <div className="mb-6">
            <div className="text-6xl text-[#4E3B46] mb-4">H</div>
          </div>
          <h3 className="text-2xl font-bold text-[#EAD3CD] mb-2">No Hotels Yet</h3>
          <p className="text-[#CFCBCA] mb-8">You haven't added any hotels to your portfolio yet. Start by submitting a hotel request.</p>
          <Link to="/owner/hotel-requests/create" className="inline-block bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-8 py-3 rounded transition">
            Request New Hotel
          </Link>
        </div>
      )}
    </Layout>
  )
}
