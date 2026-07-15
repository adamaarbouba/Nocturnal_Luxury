import { useEffect, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import { money, limit } from './../fmt'

function Pagination({ meta, onPage }) {
  if (!meta || meta.last_page <= 1) return null
  const pages = Array.from({ length: meta.last_page }, (_, i) => i + 1)
  const btn = 'flex items-center justify-center min-w-10 h-10 px-3 text-xs font-bold uppercase tracking-widest border rounded-xl transition-all duration-300'
  return (
    <div className="mt-16 flex justify-center">
      <ul className="flex gap-2 justify-center flex-wrap">
        {pages.map(p => (
          <li key={p}>
            <button
              type="button"
              onClick={() => onPage(p)}
              className={`${btn} ${p === meta.current_page
                ? 'bg-[#A0717F] text-white border-[#A0717F] shadow-[0_4px_12px_rgba(160,113,127,0.3)]'
                : 'text-[#CFCBCA] bg-[#2A2729] border-[#4E3B46] hover:bg-[#A0717F] hover:text-white hover:border-[#A0717F] hover:-translate-y-0.5'}`}
            >
              {p}
            </button>
          </li>
        ))}
      </ul>
    </div>
  )
}

export default function GuestHotelsIndex() {
  const [searchParams, setSearchParams] = useSearchParams()
  const [data, setData] = useState(null)
  const [form, setForm] = useState({
    search: searchParams.get('search') ?? '',
    city: searchParams.get('city') ?? '',
    min_price: searchParams.get('min_price') ?? '',
    max_price: searchParams.get('max_price') ?? '',
  })

  useEffect(() => {
    setData(null)
    api.get(`/guest/hotels?${searchParams.toString()}`)
      .then(res => setData(res.data))
      .catch(() => setData({ hotels: { data: [], total: 0 }, cities: [] }))
  }, [searchParams])

  const submit = (e) => {
    e.preventDefault()
    const params = new URLSearchParams()
    Object.entries(form).forEach(([k, v]) => { if (v && String(v).trim() !== '') params.set(k, v) })
    setSearchParams(params)
  }

  const goPage = (p) => {
    const params = new URLSearchParams(searchParams)
    params.set('page', p)
    setSearchParams(params)
  }

  const hotels = data?.hotels
  const cities = data?.cities ?? []
  const inputCls = 'w-full px-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 placeholder-[#CFCBCA]/30'

  return (
    <Layout>
      {/* Ambient Gradients */}
      <div className="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="max-w-6xl mx-auto px-6 py-12 relative z-10">
        <div className="mb-12">
          <Breadcrumbs links={[
            { label: 'Guest Dashboard', url: '/guest/dashboard' },
            { label: 'Destinations', url: '#' },
          ]} />
        </div>

        {/* Header */}
        <div className="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <p className="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Explore</p>
            <h1 className="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">Find Your Sanctuary</h1>
            <p className="text-xs uppercase mt-4" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
              Curated destinations for the discerning traveler
            </p>
          </div>
        </div>

        {/* Search and Filters Section */}
        <div className="bg-[#2A2729] rounded-3xl shadow-xl p-8 lg:p-10 mb-12 border border-[#4E3B46] relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none"></div>

          <form onSubmit={submit} className="relative z-10">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
              <div className="lg:col-span-4">
                <label htmlFor="search" className="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Destination or Hotel</label>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <Icon name="search" size="sm" className="text-[#A0717F]/50" />
                  </div>
                  <input type="text" id="search" placeholder="Where to?" value={form.search}
                    onChange={e => setForm({ ...form, search: e.target.value })}
                    className={`${inputCls} pl-12 pr-4`} />
                </div>
              </div>

              <div className="lg:col-span-3">
                <label htmlFor="city" className="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Location</label>
                <select id="city" value={form.city} onChange={e => setForm({ ...form, city: e.target.value })}
                  className={`${inputCls} appearance-none cursor-pointer`}>
                  <option value="">Global (All Cities)</option>
                  {cities.map(c => <option key={c} value={c}>{c}</option>)}
                </select>
              </div>

              <div className="lg:col-span-3 grid grid-cols-2 gap-4">
                <div>
                  <label htmlFor="min_price" className="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Min ($)</label>
                  <input type="number" id="min_price" placeholder="Min" min="0" value={form.min_price}
                    onChange={e => setForm({ ...form, min_price: e.target.value })} className={inputCls} />
                </div>
                <div>
                  <label htmlFor="max_price" className="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Max ($)</label>
                  <input type="number" id="max_price" placeholder="Max" min="0" value={form.max_price}
                    onChange={e => setForm({ ...form, max_price: e.target.value })} className={inputCls} />
                </div>
              </div>

              <div className="lg:col-span-2 flex flex-col gap-3">
                <button type="submit"
                  className="w-full bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.2em] text-[10px] py-4 rounded-xl shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                  Refine
                </button>
                <button type="button"
                  onClick={() => { setForm({ search: '', city: '', min_price: '', max_price: '' }); setSearchParams({}) }}
                  className="w-full text-center py-3 text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-all duration-300">
                  Clear Filters
                </button>
              </div>
            </div>
          </form>
        </div>

        {/* Hotels Grid */}
        <div>
          <h2 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
            <span className="w-8 h-[1px] bg-[#A0717F]"></span>
            Portfolio
            <span className="text-xs font-bold font-sans uppercase tracking-[0.2em] text-[#A0717F] opacity-60 bg-[#A0717F]/10 px-3 py-1 rounded-full">{hotels?.total ?? 0}</span>
          </h2>

          {!data ? (
            <p className="text-[#CFCBCA]">Loading…</p>
          ) : hotels.data.length > 0 ? (
            <>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {hotels.data.map(hotel => (
                  <Link key={hotel.id} to={`/guest/hotels/${hotel.id}`}
                    className="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl overflow-hidden hover:border-[#A0717F]/50 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col">

                    {/* Hotel Image Box */}
                    <div className="h-56 bg-[#383537] relative overflow-hidden flex items-center justify-center">
                      <div className="absolute inset-0 bg-gradient-to-tr from-[#A0717F]/20 to-transparent opacity-50 group-hover:opacity-100 transition-opacity duration-500"></div>
                      <Icon name="home" size="2xl" className="text-[#A0717F]/30 group-hover:scale-110 group-hover:text-[#A0717F]/60 transition-all duration-500 relative z-10 w-16 h-16" />

                      <div className="absolute top-4 right-4 bg-[#2A2729]/80 backdrop-blur-sm border border-[#4E3B46] px-3 py-1.5 rounded-full z-20 flex items-center gap-1.5">
                        <Icon name="map-pin" size="xs" className="text-[#A0717F] w-3 h-3" />
                        <span className="text-[9px] font-bold uppercase tracking-widest text-[#EAD3CD]">{hotel.city}</span>
                      </div>
                    </div>

                    {/* Hotel Info */}
                    <div className="p-8 flex flex-col flex-1 relative">
                      <div className="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-[#4E3B46] to-transparent"></div>

                      <h3 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-3 group-hover:text-[#A0717F] transition-colors duration-300">{hotel.name}</h3>
                      <p className="text-[11px] text-[#CFCBCA] mb-6 line-clamp-2 leading-relaxed italic opacity-80 flex-1">
                        "{limit(hotel.description, 100)}"
                      </p>

                      <div className="pt-6 border-t border-[#4E3B46]/30 flex justify-between items-end">
                        <div>
                          <p className="text-[8px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] mb-1 opacity-60">Availability</p>
                          <span className={`inline-block px-3 py-1 rounded text-[9px] font-bold uppercase tracking-widest ${hotel.available_rooms_count > 0
                            ? 'bg-green-500/10 text-green-400 border border-green-500/30'
                            : 'bg-red-500/10 text-red-400 border border-red-500/30'}`}>
                            {hotel.available_rooms_count} Suites
                          </span>
                        </div>

                        <div className="text-right">
                          <p className="text-[8px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] mb-1 opacity-60">From</p>
                          {hotel.min_price ? (
                            <div className="text-xl font-bold font-serif text-[#EAD3CD]">
                              ${money(hotel.min_price, 0)}<span className="text-[9px] font-sans font-normal text-[#CFCBCA] uppercase tracking-widest ml-1 opacity-50">/night</span>
                            </div>
                          ) : (
                            <div className="text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA] opacity-50 pt-2">
                              N/A
                            </div>
                          )}
                        </div>
                      </div>
                    </div>
                  </Link>
                ))}
              </div>

              <Pagination meta={hotels} onPage={goPage} />
            </>
          ) : (
            <div className="rounded-3xl p-16 text-center border border-[rgba(234,211,205,0.1)] relative overflow-hidden mt-8" style={{ backgroundColor: '#2A2729' }}>
              <div className="relative z-10">
                <div className="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                  <Icon name="search" className="w-8 h-8 text-[#A0717F]" />
                </div>
                <h3 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">No Destinations Found</h3>
                <p className="text-xs uppercase tracking-widest mb-10 leading-loose mx-auto" style={{ color: 'rgba(207, 203, 202, 0.5)', maxWidth: '28rem' }}>
                  Our current portfolio does not match your precise criteria.
                  Consider refining your search parameters.
                </p>
                <button type="button"
                  onClick={() => { setForm({ search: '', city: '', min_price: '', max_price: '' }); setSearchParams({}) }}
                  className="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1 bg-[#4E3B46] hover:bg-[#68525F]">
                  Clear Criteria
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}
