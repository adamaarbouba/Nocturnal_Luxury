import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import api from '../../lib/api'

// Ports resources/views/owner/dashboard.blade.php
export default function Dashboard() {
  const [data, setData] = useState(null)

  useEffect(() => {
    api.get('/owner/dashboard').then((res) => setData(res.data)).catch(() => {})
  }, [])

  if (!data) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  const { stats, recentHotels, hotelRequests } = data

  return (
    <Layout>
      <Breadcrumbs links={[{ label: 'Owner Dashboard', url: '/owner/dashboard' }]} />

      {/* Statistics Cards Section */}
      <div id="stats-section">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="rounded-lg shadow-lg p-6 border-t-4 border-[#A0717F] bg-[#383537]">
            <h3 className="text-sm font-semibold text-[#EAD3CD]">Total Hotels</h3>
            <p className="text-4xl font-bold mt-3 text-[#A0717F]">{stats.totalHotels}</p>
            <p className="text-xs mt-2 text-[#CFCBCA]">Hotels owned by you</p>
          </div>
          <div className="rounded-lg shadow-lg p-6 border-t-4 border-[#4E3B46] bg-[#383537]">
            <h3 className="text-sm font-semibold text-[#EAD3CD]">Total Rooms</h3>
            <p className="text-4xl font-bold mt-3 text-[#EAD3CD]">{stats.totalRooms}</p>
            <p className="text-xs mt-2 text-[#CFCBCA]">Across all hotels</p>
          </div>
          <div className="rounded-lg shadow-lg p-6 border-t-4 border-[#A0717F] bg-[#383537]">
            <h3 className="text-sm font-semibold text-[#EAD3CD]">Total Bookings</h3>
            <p className="text-4xl font-bold mt-3 text-[#A0717F]">{stats.totalBookings}</p>
            <p className="text-xs mt-2 text-[#CFCBCA]">Active and completed</p>
          </div>
        </div>
      </div>

      {/* Room Status Breakdown Section */}
      <div id="room-status-section" className="mt-8">
        <h3 className="text-xl font-bold mb-6 text-[#EAD3CD]">Room Status Overview</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
          {[
            ['Available', 'available', '#4E3B46', 'text-[#EAD3CD]'],
            ['Occupied', 'occupied', '#A0717F', 'text-[#A0717F]'],
            ['Cleaning', 'cleaning', '#4E3B46', 'text-[#EAD3CD]'],
            ['Inspection', 'inspection', '#A0717F', 'text-[#A0717F]'],
            ['Maintenance', 'maintenance', '#4E3B46', 'text-[#EAD3CD]'],
            ['Disabled', 'disabled', '#4E3B46', 'text-[#EAD3CD]'],
          ].map(([label, key, border, textCls]) => (
            <div key={key} className="rounded-lg shadow-lg p-6 border-t-4 bg-[#383537]" style={{ borderTopColor: border }}>
              <h4 className="text-sm font-semibold text-[#EAD3CD]">{label}</h4>
              <p className={`text-3xl font-bold mt-2 ${textCls}`}>{stats.roomStatusCounts[key]}</p>
            </div>
          ))}
        </div>
      </div>

      {/* Hotel Requests Status Section */}
      <div id="requests-section" className="mt-12">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="rounded-lg shadow-lg p-6 bg-[#383537] border-l-4 border-[#A0717F]">
            <h3 className="text-sm font-semibold text-[#EAD3CD]">Pending Requests</h3>
            <p className="text-3xl font-bold mt-2 text-[#A0717F]">{hotelRequests.pending}</p>
            <Link to="/owner/hotel-requests" className="text-xs font-semibold mt-3 inline-block text-[#A0717F] hover:underline">View Details →</Link>
          </div>
          <div className="rounded-lg shadow-lg p-6 bg-[#383537] border-l-4 border-[#4E3B46]">
            <h3 className="text-sm font-semibold text-[#EAD3CD]">Approved Requests</h3>
            <p className="text-3xl font-bold mt-2 text-[#EAD3CD]">{hotelRequests.approved}</p>
            <Link to="/owner/hotel-requests" className="text-xs font-semibold mt-3 inline-block text-[#CFCBCA] hover:text-[#EAD3CD]">View Details →</Link>
          </div>
          <div className="rounded-lg shadow-lg p-6 bg-[#383537] border-l-4 border-[#4E3B46]">
            <h3 className="text-sm font-semibold text-[#EAD3CD]">Rejected Requests</h3>
            <p className="text-3xl font-bold mt-2 text-[#EAD3CD]">{hotelRequests.rejected}</p>
            <Link to="/owner/hotel-requests" className="text-xs font-semibold mt-3 inline-block text-[#CFCBCA] hover:text-[#EAD3CD]">View Details →</Link>
          </div>
        </div>

        {/* Quick Action Button */}
        <div className="rounded-lg p-6 flex items-center justify-between border border-[#4E3B46] bg-[#2A2729]">
          <div>
            <h4 className="font-semibold text-[#EAD3CD]">Ready to add a new hotel?</h4>
            <p className="text-sm mt-1 text-[#CFCBCA]">Submit a hotel request to expand your portfolio</p>
          </div>
          <Link to="/owner/hotel-requests/create" className="text-white font-semibold px-6 py-2 rounded transition bg-[#A0717F] hover:bg-[#8F6470]">
            Request New Hotel
          </Link>
        </div>

        {/* Maintenance Quick Action */}
        {(stats.roomStatusCounts.maintenance > 0 || stats.roomStatusCounts.cleaning > 0) && (
          <div className="rounded-lg p-6 flex items-center justify-between mt-4 border border-[#4E3B46] bg-[#2A2729]">
            <div>
              <h4 className="font-semibold text-[#EAD3CD]">Room Maintenance Needed</h4>
              <p className="text-sm mt-1 text-[#CFCBCA]">
                {stats.roomStatusCounts.maintenance} rooms in maintenance, {stats.roomStatusCounts.cleaning} in cleaning
              </p>
            </div>
            <Link to="/owner/maintenance" className="text-white font-semibold px-6 py-2 rounded transition bg-[#A0717F] hover:bg-[#8F6470]">
              View Maintenance
            </Link>
          </div>
        )}
      </div>

      {/* Occupancy Rate Chart Section */}
      {/* ponytail: Chart.js not installed in the SPA — same data as horizontal CSS bars, add chart.js if interactivity is ever needed */}
      {recentHotels.length > 0 && (
        <div id="occupancy-chart-section" className="mt-12">
          <div className="rounded-lg shadow-lg p-8 bg-[#383537]">
            <h3 className="text-xl font-bold mb-6 text-[#EAD3CD]">Hotel Occupancy Rates</h3>
            <div className="space-y-4">
              {recentHotels.map((hotel, i) => (
                <div key={hotel.id}>
                  <div className="flex justify-between text-sm mb-1">
                    <span className="text-[#CFCBCA] font-medium">{hotel.name}</span>
                    <span className="text-[#EAD3CD] font-semibold">{hotel.occupancy_rate}%</span>
                  </div>
                  <div className="h-5 rounded-md bg-[#2A2729] overflow-hidden">
                    <div
                      className="h-full rounded-md border-2 border-[#383537]"
                      style={{ width: `${hotel.occupancy_rate}%`, backgroundColor: i % 4 === 0 ? '#A0717F' : '#4E3B46' }}
                    />
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Recent Hotels Section */}
      <div id="hotels-section" className="mt-12">
        <div className="rounded-lg shadow-lg p-8 bg-[#383537]">
          <div className="flex items-center justify-between mb-6">
            <h3 className="text-xl font-bold text-[#EAD3CD]">Your Hotels</h3>
            <Link to="/owner/hotels" className="text-sm font-semibold text-[#A0717F] hover:underline">View All →</Link>
          </div>

          {recentHotels.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {recentHotels.map((hotel) => (
                <div key={hotel.id} className="border border-[#4E3B46] bg-[#2A2729] rounded-lg p-6 hover:shadow-md transition">
                  <h4 className="font-semibold text-lg mb-2 text-[#EAD3CD]">{hotel.name}</h4>
                  <div className="space-y-2 text-sm mb-4 text-[#CFCBCA]">
                    <p><strong className="text-[#EAD3CD]">Location:</strong> {hotel.location}</p>
                    <p><strong className="text-[#EAD3CD]">Total Rooms:</strong> {hotel.rooms}</p>
                    <p><strong className="text-[#EAD3CD]">Active Bookings:</strong> {hotel.bookings}</p>
                    <p><strong className="text-[#A0717F]">Occupancy Rate:</strong> <span className="font-bold text-[#A0717F]">{hotel.occupancy_rate}%</span></p>
                  </div>
                  <div className="flex gap-2">
                    <Link to={`/owner/hotels/${hotel.id}`} className="text-[#CFCBCA] font-semibold px-4 py-2 rounded text-sm transition border border-[#4E3B46] hover:bg-[#383537]">View</Link>
                    <Link to={`/owner/hotels/${hotel.id}/manage`} className="text-[#CFCBCA] font-semibold px-4 py-2 rounded text-sm transition border border-[#4E3B46] hover:bg-[#383537]">Manage</Link>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-[#CFCBCA]">No hotels found. Submit a hotel request to get started.</p>
          )}
        </div>
      </div>
    </Layout>
  )
}
