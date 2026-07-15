import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Alert from '../../../components/Alert'
import api from '../../../lib/api'
import { money, ucfirst } from '../format'

const emptyForm = { room_number: '', type: '', capacity: '', price: '' }

// Ports resources/views/owner/hotels/manage.blade.php
export default function HotelManage() {
  const { id } = useParams()
  const [hotel, setHotel] = useState(null)
  const [modalOpen, setModalOpen] = useState(false)
  const [editingRoomId, setEditingRoomId] = useState(null)
  const [form, setForm] = useState(emptyForm)
  const [flash, setFlash] = useState(null)
  const [error, setError] = useState(null)

  const load = () => api.get(`/owner/hotels/${id}/manage`).then((res) => setHotel(res.data.hotel)).catch(() => {})

  useEffect(() => { load() }, [id])

  const openAdd = () => {
    setForm(emptyForm)
    setEditingRoomId(null)
    setModalOpen(true)
  }

  const openEdit = (room) => {
    setForm({ room_number: room.room_number, type: room.room_type, capacity: room.capacity, price: room.price_per_night })
    setEditingRoomId(room.id)
    setModalOpen(true)
  }

  const submitRoom = async (e) => {
    e.preventDefault()
    try {
      const res = editingRoomId
        ? await api.post(`/owner/hotels/${id}/rooms/${editingRoomId}/update`, form)
        : await api.post(`/owner/hotels/${id}/add-room`, form)
      if (res.data.success) {
        setFlash(res.data.message)
        setModalOpen(false)
        load()
      } else {
        setError(res.data.message || 'Something went wrong')
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Error processing request')
    }
  }

  const disableRoom = async (room) => {
    if (!confirm(`Are you sure you want to disable room ${room.room_number}? The room will be marked as disabled and cannot be used for bookings.`)) return
    try {
      const res = await api.delete(`/owner/hotels/${id}/rooms/${room.id}`)
      if (res.data.success) {
        setFlash(res.data.message)
        load()
      } else {
        setError(res.data.message || 'Something went wrong')
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Error disabling room')
    }
  }

  if (!hotel) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  return (
    <Layout>
      <Breadcrumbs links={[{ label: 'Owner Dashboard', url: '/owner/dashboard' }, { label: 'Manage Hotel', url: '#' }]} />

      <div className="flex justify-between items-center mb-8">
        <h2 className="text-3xl font-bold text-[#EAD3CD]">{hotel.name} - Management</h2>
        <Link to="/owner/hotels" className="border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold px-6 py-2 rounded transition">
          Back to Hotels
        </Link>
      </div>

      {flash && <Alert variant="success" className="mb-6">{flash}</Alert>}
      {error && <Alert variant="error" className="mb-6" dismissible={false}>{error}</Alert>}

      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-2xl font-bold text-[#EAD3CD]">Room Management</h3>
          <button onClick={openAdd} className="bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition">
            Add New Room
          </button>
        </div>

        {hotel.rooms?.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead className="bg-[#2A2729] border-b border-[#4E3B46]">
                <tr>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Room Number</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Type</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Capacity</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Price</th>
                  <th className="px-6 py-3 text-xs font-semibold text-[#CFCBCA]">Action</th>
                </tr>
              </thead>
              <tbody>
                {hotel.rooms.map((room) => (
                  <tr key={room.id} className="border-b border-[#4E3B46] hover:bg-[#2A2729]/50">
                    <td className="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">{room.room_number}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{ucfirst(room.room_type)}</td>
                    <td className="px-6 py-4 text-sm text-[#CFCBCA]">{room.capacity} guests</td>
                    <td className="px-6 py-4 text-sm text-[#EAD3CD] font-semibold">${money(room.price_per_night)}</td>
                    <td className="px-6 py-4 text-sm flex gap-3">
                      {room.status === 'Occupied' ? (
                        <>
                          <button disabled className="text-[#4E3B46] cursor-not-allowed font-semibold" title="Cannot edit occupied room">Edit</button>
                          <button disabled className="text-[#4E3B46] cursor-not-allowed font-semibold" title="Cannot disable occupied room">Disable</button>
                        </>
                      ) : room.status === 'Disabled' ? (
                        <>
                          <button disabled className="text-[#4E3B46] cursor-not-allowed font-semibold" title="Room is disabled">Edit</button>
                          <button disabled className="text-[#4E3B46] cursor-not-allowed font-semibold" title="Room is already disabled">Disable</button>
                        </>
                      ) : (
                        <>
                          <button onClick={() => openEdit(room)} className="text-[#A0717F] hover:text-[#EAD3CD] font-semibold transition">Edit</button>
                          <button onClick={() => disableRoom(room)} className="text-[#CFCBCA] hover:text-red-500 font-semibold transition">Disable</button>
                        </>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="text-center py-8 border-t border-[#4E3B46]">
            <p className="text-[#CFCBCA] text-lg mb-4">No rooms added yet</p>
            <button onClick={openAdd} className="inline-block bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition">
              Add First Room
            </button>
          </div>
        )}
      </div>

      {/* Add/Edit Room Modal */}
      {modalOpen && (
        <div
          className="fixed inset-0 bg-[#1A1515] bg-opacity-80 z-50 flex items-center justify-center"
          onClick={(e) => { if (e.target === e.currentTarget) setModalOpen(false) }}
        >
          <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-xl p-8 w-full max-w-md mx-4">
            <h3 className="text-2xl font-bold text-[#EAD3CD] mb-6">{editingRoomId ? 'Edit Room' : 'Add New Room'}</h3>

            <form onSubmit={submitRoom}>
              <div className="mb-4">
                <label className="block text-[#CFCBCA] font-semibold mb-2">Room Number</label>
                <input
                  type="text" required value={form.room_number}
                  onChange={(e) => setForm({ ...form, room_number: e.target.value })}
                  className="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
                  placeholder="e.g., 101, 102"
                />
              </div>

              <div className="mb-4">
                <label className="block text-[#CFCBCA] font-semibold mb-2">Room Type</label>
                <select
                  required value={form.type}
                  onChange={(e) => setForm({ ...form, type: e.target.value })}
                  className="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
                >
                  <option value="">Select Type</option>
                  <option value="single">Single</option>
                  <option value="double">Double</option>
                  <option value="suite">Suite</option>
                </select>
              </div>

              <div className="mb-4">
                <label className="block text-[#CFCBCA] font-semibold mb-2">Capacity (Guests)</label>
                <input
                  type="number" required min="1" value={form.capacity}
                  onChange={(e) => setForm({ ...form, capacity: e.target.value })}
                  className="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
                  placeholder="e.g., 2"
                />
              </div>

              <div className="mb-6">
                <label className="block text-[#CFCBCA] font-semibold mb-2">Price per Night</label>
                <input
                  type="number" required min="0" step="0.01" value={form.price}
                  onChange={(e) => setForm({ ...form, price: e.target.value })}
                  className="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]"
                  placeholder="e.g., 99.99"
                />
              </div>

              <div className="flex gap-4">
                <button type="button" onClick={() => setModalOpen(false)} className="flex-1 border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold py-2 rounded transition">
                  Cancel
                </button>
                <button type="submit" className="flex-1 bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold py-2 rounded transition">
                  Save Room
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </Layout>
  )
}
