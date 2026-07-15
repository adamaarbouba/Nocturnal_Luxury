import { useEffect, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import Pagination from './Pagination'
import api from '../../lib/api'

const fmtDate = (s) => new Date(s).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
const ucfirst = (s) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : '')

export default function UsersIndex() {
  const [searchParams, setSearchParams] = useSearchParams()
  const [data, setData] = useState(null)
  const [search, setSearch] = useState(searchParams.get('search') ?? '')
  const [role, setRole] = useState(searchParams.get('role') ?? '')

  const activeSearch = searchParams.get('search') ?? ''
  const activeRole = searchParams.get('role') ?? ''
  const page = searchParams.get('page') ?? '1'

  useEffect(() => {
    api
      .get('/admin/users', { params: { search: activeSearch || undefined, role: activeRole || undefined, page } })
      .then((res) => setData(res.data))
      .catch(() => setData({ error: true }))
  }, [activeSearch, activeRole, page])

  const submit = (e) => {
    e.preventDefault()
    const params = {}
    if (search) params.search = search
    if (role) params.role = role
    setSearchParams(params)
  }

  const users = data?.users
  const roles = data?.roles ?? []

  return (
    <Layout>
      {/* Ambient Gradients */}
      <div className="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="max-w-7xl mx-auto px-6 py-12 relative z-10">
        <div className="mb-12">
          <Breadcrumbs links={[
            { label: 'Admin Dashboard', url: '/admin' },
            { label: 'User Directory', url: '#' },
          ]} />
        </div>

        {/* Header */}
        <div className="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <p className="text-[10px] font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">System Registry</p>
            <h1 className="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">User Directory</h1>
            <p className="text-[10px] uppercase mt-4 text-[#CFCBCA]/60 tracking-[0.15em]">
              Manage access, roles, and privileges across the Nocturnal Luxury network
            </p>
          </div>

          <div className="text-right">
            <p className="text-[9px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/60 mb-2">Total Registered</p>
            <p className="text-3xl font-bold font-serif text-[#EAD3CD]">{users?.total ?? '—'}</p>
          </div>
        </div>

        {/* Search & Filter Card */}
        <div className="bg-[#2A2729] rounded-3xl shadow-xl p-8 mb-12 border border-[#4E3B46] relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none"></div>

          <form onSubmit={submit} className="relative z-10">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end">
              {/* Search Input */}
              <div className="lg:col-span-5">
                <label htmlFor="search" className="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Identity Search</label>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <Icon name="magnifying-glass" size="sm" className="text-[#A0717F]/50" />
                  </div>
                  <input
                    type="text" id="search" placeholder="Query by name, email..."
                    value={search} onChange={(e) => setSearch(e.target.value)}
                    className="w-full pl-12 pr-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 placeholder-[#CFCBCA]/30"
                  />
                </div>
              </div>

              {/* Role Filter */}
              <div className="lg:col-span-4">
                <label htmlFor="role" className="block text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-3">Classification</label>
                <select
                  id="role" value={role} onChange={(e) => setRole(e.target.value)}
                  className="w-full px-4 py-4 border border-[#4E3B46] bg-[#383537] text-[#EAD3CD] rounded-xl focus:outline-none focus:border-[#A0717F] transition-all duration-300 appearance-none cursor-pointer"
                >
                  <option value="">Global (All Roles)</option>
                  {roles.map((r) => (
                    <option key={r.slug} value={r.slug}>{ucfirst(r.slug)}</option>
                  ))}
                </select>
              </div>

              {/* Buttons */}
              <div className="lg:col-span-3 flex flex-col gap-3">
                <button
                  type="submit"
                  className="w-full bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.2em] text-[10px] py-4 rounded-xl shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                >
                  Execute Query
                </button>
                {(activeSearch || activeRole) && (
                  <button
                    type="button"
                    onClick={() => { setSearch(''); setRole(''); setSearchParams({}) }}
                    className="w-full text-center py-3 text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-all duration-300"
                  >
                    Clear Parameters
                  </button>
                )}
              </div>
            </div>
          </form>
        </div>

        {/* Directory Table */}
        <div className="bg-[#2A2729] border border-[#4E3B46] rounded-3xl shadow-2xl overflow-hidden relative">
          <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none"></div>

          {!data ? (
            <div className="p-16 text-center text-[#CFCBCA] relative z-10">Loading…</div>
          ) : users?.data?.length > 0 ? (
            <>
              <div className="overflow-x-auto relative z-10">
                <table className="w-full text-left whitespace-nowrap">
                  <thead>
                    <tr className="border-b border-[#4E3B46]/50 bg-[#383537]/50">
                      {['Identity', 'Contact', 'Classification', 'Registration', 'Action'].map((h) => (
                        <th key={h} className="px-8 py-6 text-[9px] font-bold text-[#A0717F] uppercase tracking-[0.2em]">{h}</th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#4E3B46]/30">
                    {users.data.map((user) => (
                      <tr key={user.id} className="hover:bg-[#383537]/50 transition-colors duration-300 group">
                        <td className="px-8 py-6">
                          <Link to={`/admin/users/${user.id}`} className="flex flex-col">
                            <span className="font-serif text-lg font-bold text-[#EAD3CD] group-hover:text-[#A0717F] transition-colors">{user.name}</span>
                            {user.banned_at ? (
                              <span className="inline-flex items-center gap-1 mt-1 text-[9px] font-bold uppercase tracking-[0.2em] text-red-400">
                                <span className="w-1.5 h-1.5 rounded-full bg-red-400"></span> Sanctioned
                              </span>
                            ) : (
                              <span className="inline-flex items-center gap-1 mt-1 text-[9px] font-bold uppercase tracking-[0.2em] text-green-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span className="w-1.5 h-1.5 rounded-full bg-green-400"></span> Active
                              </span>
                            )}
                          </Link>
                        </td>
                        <td className="px-8 py-6">
                          <div className="flex flex-col">
                            <span className="text-sm text-[#CFCBCA]">{user.email}</span>
                            <span className="text-[10px] text-[#CFCBCA]/60 mt-1 tracking-widest">{user.phone ?? 'Unregistered'}</span>
                          </div>
                        </td>
                        <td className="px-8 py-6">
                          <span className="inline-block px-3 py-1 bg-[#383537] border border-[#4E3B46] rounded-md text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA]">
                            {ucfirst(user.role?.slug)}
                          </span>
                        </td>
                        <td className="px-8 py-6">
                          <span className="text-sm text-[#EAD3CD]">{fmtDate(user.created_at)}</span>
                        </td>
                        <td className="px-8 py-6">
                          <Link
                            to={`/admin/users/${user.id}`}
                            className="inline-flex items-center gap-2 px-4 py-2 border border-[#4E3B46] rounded-lg text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] hover:bg-[#383537] transition-all duration-300"
                          >
                            Dossier →
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {users.last_page > 1 && (
                <div className="px-8 py-6 border-t border-[#4E3B46]/50 bg-[#383537]/20 flex justify-center relative z-10">
                  <Pagination
                    paginator={users}
                    onPage={(p) => {
                      const params = {}
                      if (activeSearch) params.search = activeSearch
                      if (activeRole) params.role = activeRole
                      params.page = String(p)
                      setSearchParams(params)
                    }}
                  />
                </div>
              )}
            </>
          ) : (
            <div className="p-16 text-center relative z-10">
              <div className="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                <Icon name="users" className="w-8 h-8 text-[#A0717F]" />
              </div>
              <h3 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">No Identities Found</h3>
              <p className="text-xs uppercase tracking-widest mb-10 leading-loose mx-auto" style={{ color: 'rgba(207, 203, 202, 0.5)', maxWidth: '28rem' }}>
                The system registry query returned no results matching your exact classification criteria.
              </p>
              <button
                type="button"
                onClick={() => { setSearch(''); setRole(''); setSearchParams({}) }}
                className="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1 border border-[#4E3B46] bg-[#383537] hover:bg-[#4E3B46]"
              >
                Clear Parameters
              </button>
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}
