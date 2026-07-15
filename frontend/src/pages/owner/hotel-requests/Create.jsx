import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import api from '../../../lib/api'

const initial = { name: '', email: '', phone: '', description: '', address: '', city: '', country: '' }

// Ports resources/views/owner/hotel-requests/create.blade.php
export default function HotelRequestCreate() {
  const navigate = useNavigate()
  const [form, setForm] = useState(initial)
  const [errors, setErrors] = useState({})
  const set = (field) => (e) => setForm({ ...form, [field]: e.target.value })

  const submit = async (e) => {
    e.preventDefault()
    setErrors({})
    try {
      await api.post('/owner/hotel-requests', form)
      navigate('/owner/dashboard')
    } catch (err) {
      setErrors(err.response?.data?.errors ?? {})
    }
  }

  const errorList = Object.values(errors).flat()

  return (
    <Layout>
      <div className="max-w-4xl mx-auto">
        <Breadcrumbs links={[{ label: 'Owner Dashboard', url: '/owner/dashboard' }, { label: 'New Hotel Request', url: '#' }]} />

        <div className="relative overflow-hidden rounded-2xl border border-[#4E3B46] bg-[#383537] shadow-2xl">
          <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#A0717F] to-transparent opacity-50"></div>

          <div className="p-8 md:p-12">
            <header className="mb-10 text-center">
              <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#4E3B46] mb-6 text-[#A0717F] shadow-inner">
                <Icon name="building" size="lg" />
              </div>
              <h2 className="text-4xl font-bold font-serif text-[#EAD3CD] mb-4">Request a New Hotel</h2>
              <p className="text-[#CFCBCA] max-w-lg mx-auto leading-relaxed">
                Submit your property for review. Our curators will evaluate the proposal to ensure it meets our standards of nocturnal luxury.
              </p>
            </header>

            {errorList.length > 0 && (
              <div className="mb-10 p-5 bg-red-950/30 border border-red-900/50 rounded-xl flex gap-4 items-start">
                <div className="shrink-0 text-red-500 mt-0.5">
                  <Icon name="close" size="sm" />
                </div>
                <div>
                  <h3 className="text-red-400 font-bold text-sm uppercase tracking-wider mb-2">Refinement Required</h3>
                  <ul className="text-red-300/80 text-sm space-y-1">
                    {errorList.map((err, i) => <li key={i}>{err}</li>)}
                  </ul>
                </div>
              </div>
            )}

            <form onSubmit={submit} className="space-y-12">
              <section className="space-y-8">
                <div className="flex items-center gap-4 mb-6">
                  <div className="h-[1px] flex-1 bg-gradient-to-r from-transparent to-[#4E3B46]"></div>
                  <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-[#A0717F]">Essential Details</h3>
                  <div className="h-[1px] flex-1 bg-gradient-to-l from-transparent to-[#4E3B46]"></div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                      Hotel Name <span className="text-[#A0717F]">*</span>
                    </label>
                    <input type="text" value={form.name} onChange={set('name')} required minLength={3} maxLength={255}
                      className="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                      placeholder="The Nocturnal Atelier" />
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                      Email Address <span className="text-[#A0717F]">*</span>
                    </label>
                    <input type="email" value={form.email} onChange={set('email')} required
                      className="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                      placeholder="concierge@atelier.com" />
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                      Contact Phone <span className="text-[#A0717F]">*</span>
                    </label>
                    <input type="tel" value={form.phone} onChange={set('phone')} required
                      className="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                      placeholder="+1 (000) 000-0000" />
                  </div>

                  <div className="md:col-span-2 space-y-2">
                    <label className="text-sm font-medium text-[#CFCBCA] flex items-center gap-2">
                      Property Narrative <span className="text-xs text-[#4E3B46] font-normal">(Optional)</span>
                    </label>
                    <textarea rows={4} value={form.description} onChange={set('description')}
                      className="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46] resize-none"
                      placeholder="Describe the architectural soul and atmosphere of the hotel..." />
                  </div>
                </div>
              </section>

              <section className="space-y-8">
                <div className="flex items-center gap-4 mb-6">
                  <div className="h-[1px] flex-1 bg-gradient-to-r from-transparent to-[#4E3B46]"></div>
                  <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-[#A0717F]">Geographic Atelier</h3>
                  <div className="h-[1px] flex-1 bg-gradient-to-l from-transparent to-[#4E3B46]"></div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div className="md:col-span-2 space-y-2">
                    <label className="text-sm font-medium text-[#CFCBCA]">Street Address <span className="text-[#A0717F]">*</span></label>
                    <input type="text" value={form.address} onChange={set('address')} required
                      className="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                      placeholder="Rue de la Paix, 12" />
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-[#CFCBCA]">City <span className="text-[#A0717F]">*</span></label>
                    <input type="text" value={form.city} onChange={set('city')} required
                      className="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                      placeholder="Paris" />
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-[#CFCBCA]">Country <span className="text-[#A0717F]">*</span></label>
                    <input type="text" value={form.country} onChange={set('country')} required
                      className="w-full bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-4 focus:outline-none focus:border-[#A0717F] focus:ring-1 focus:ring-[#A0717F] transition-all placeholder-[#4E3B46]"
                      placeholder="France" />
                  </div>
                </div>
              </section>

              <div className="pt-10 flex flex-col sm:flex-row gap-4">
                <Link to="/owner/dashboard"
                  className="flex-1 order-2 sm:order-1 border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-bold uppercase tracking-widest text-[10px] sm:text-xs px-8 py-5 rounded-xl text-center transition-all">
                  Abandon Request
                </Link>
                <button type="submit"
                  className="flex-[2] order-1 sm:order-2 bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-widest text-[10px] sm:text-xs px-8 py-5 rounded-xl shadow-xl transition-all flex items-center justify-center gap-3">
                  <Icon name="checkmark" size="sm" />
                  Submit Proposal
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Layout>
  )
}
