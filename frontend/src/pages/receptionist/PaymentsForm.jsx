import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Alert from '../../components/Alert'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import api from '../../lib/api'
import { useAuth } from '../../context/AuthContext'
import { money, pad5 } from './util'

export default function PaymentsForm() {
  const { id } = useParams()
  const [data, setData] = useState(null)
  const [type, setType] = useState('payment')
  const [amount, setAmount] = useState('')
  const [notes, setNotes] = useState('')
  const [errors, setErrors] = useState({})
  const [error, setError] = useState(null)
  const [flash, setFlash] = useState(null)
  const navigate = useNavigate()
  const { user } = useAuth()

  const load = () =>
    api.get(`/receptionist/bookings/${id}/payment`).then((res) => {
      setData(res.data)
      setAmount(res.data.remainingBalance > 0 ? String(res.data.remainingBalance) : '')
    })

  useEffect(() => { load() }, [id])

  if (!data) return <Layout><div /></Layout>
  const { booking, hotel, amountPaid, amountRefunded, amountNetPaid, remainingBalance } = data

  const submit = async (e) => {
    e.preventDefault()
    setErrors({})
    setError(null)
    try {
      const res = await api.post(`/receptionist/bookings/${id}/payment`, { amount, type, notes })
      setFlash({ variant: 'success', message: res.data.message })
      setNotes('')
      load()
    } catch (err) {
      if (err.response?.status === 422 && err.response.data.errors) setErrors(err.response.data.errors)
      else setError(err.response?.data?.message ?? 'Error recording payment.')
    }
  }

  const deletePayment = async (paymentId) => {
    if (!window.confirm('Revoke this transaction logging?')) return
    try {
      const res = await api.delete(`/receptionist/bookings/${id}/payments/${paymentId}`)
      setFlash({ variant: 'success', message: res.data.message })
      load()
    } catch (err) {
      setError(err.response?.data?.message ?? 'Error deleting payment.')
    }
  }

  return (
    <Layout>
      <div className="fixed top-0 right-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 left-0 w-[400px] h-[400px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-8 max-w-5xl relative z-10">
        <Breadcrumbs links={[
          { label: 'Receptionist Dashboard', url: '/receptionist/dashboard' },
          { label: 'Bookings', url: '/receptionist/bookings' },
          { label: `Folio #${pad5(booking.id)}`, url: `/receptionist/bookings/${booking.id}` },
          { label: 'Settle Account', url: '#' },
        ]} />

        <div className="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div>
            <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
              Folio #{pad5(booking.id)} &mdash; {hotel.name}
            </p>
            <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Settle Account</h1>
          </div>
          <Link to={`/receptionist/bookings/${booking.id}`} className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300 shrink-0"
            style={{ color: '#CFCBCA', border: '1px solid rgba(207, 203, 202, 0.2)', letterSpacing: '0.1em' }}>
            <Icon name="arrow-left" className="w-4 h-4" /> Return to Folio
          </Link>
        </div>

        {flash && <div className="mb-8"><Alert variant={flash.variant}>{flash.message}</Alert></div>}
        {error && (
          <div className="mb-8 p-4 rounded-xl" style={{ backgroundColor: 'rgba(239, 68, 68, 0.05)', border: '1px solid rgba(239, 68, 68, 0.2)' }}>
            <p className="text-red-400 font-medium tracking-wide">✗ {error}</p>
          </div>
        )}

        <div className="grid grid-cols-1 lg:grid-cols-5 gap-8">
          <div className="lg:col-span-3 space-y-8">
            <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
              <h2 className="text-xl font-bold mb-6 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                <span className="p-2 rounded-lg" style={{ backgroundColor: '#4E3B46' }}><Icon name="user" className="w-5 h-5" style={{ color: '#A0717F' }} /></span>
                Guest Identifiers
              </h2>
              <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div className="col-span-2">
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Guest Name</p>
                  <p className="text-base font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{booking.user.name}</p>
                </div>
                <div className="col-span-2">
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Email</p>
                  <p className="text-sm" style={{ color: '#CFCBCA' }}>{booking.user.email}</p>
                </div>
                <div className="col-span-2">
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Arrival</p>
                  <p className="text-sm font-semibold" style={{ color: '#EAD3CD' }}>{new Date(booking.check_in_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}</p>
                </div>
                <div className="col-span-2">
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Departure</p>
                  <p className="text-sm font-semibold" style={{ color: '#EAD3CD' }}>{new Date(booking.check_out_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}</p>
                </div>
              </div>
            </div>

            <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
              <h2 className="text-xl font-bold mb-8 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                <span className="p-2 rounded-lg" style={{ backgroundColor: '#4E3B46' }}><Icon name="checkmark" className="w-5 h-5" style={{ color: '#A0717F' }} /></span>
                Transaction Capture
              </h2>

              <form onSubmit={submit} className="space-y-8">
                <div className="p-5 rounded-xl border border-[rgba(160,113,127,0.15)]" style={{ backgroundColor: '#2E2530' }}>
                  <label className="block text-xs font-semibold uppercase mb-4" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Mode of Transaction</label>
                  <div className="grid grid-cols-2 gap-4">
                    <label className="flex items-center cursor-pointer p-3 rounded-lg transition-colors border"
                      style={{ backgroundColor: '#383537', borderColor: type === 'payment' ? '#A0717F' : 'rgba(160,113,127,0.3)' }}>
                      <input type="radio" name="type" value="payment" checked={type === 'payment'} onChange={() => setType('payment')} className="w-4 h-4 rounded-full" style={{ accentColor: '#A0717F' }} />
                      <span className="ml-3 text-sm font-bold uppercase tracking-wider" style={{ color: '#EAD3CD' }}>Record Payment</span>
                    </label>
                    <label className="flex items-center cursor-pointer p-3 rounded-lg transition-colors border"
                      style={{ backgroundColor: '#383537', borderColor: type === 'refund' ? '#A0717F' : 'rgba(160,113,127,0.3)' }}>
                      <input type="radio" name="type" value="refund" checked={type === 'refund'} onChange={() => setType('refund')} className="w-4 h-4 rounded-full" style={{ accentColor: '#A0717F' }} />
                      <span className="ml-3 text-sm font-bold uppercase tracking-wider" style={{ color: '#CFCBCA' }}>Issue Refund</span>
                    </label>
                  </div>
                </div>

                <div>
                  <div className="flex justify-between items-end mb-2">
                    <label className="block text-xs font-semibold uppercase relative top-1" style={{ color: '#A0717F', letterSpacing: '0.15em' }}>Transaction Amount</label>
                    <span className="text-xs font-medium" style={{ color: 'rgba(207, 203, 202, 0.4)' }}>
                      {remainingBalance > 0 ? `(Max Allowed: $${money(remainingBalance)})` : '(Account is fully paid)'}
                    </span>
                  </div>
                  <div className="relative flex items-center p-2 rounded-lg border transition-colors duration-300" style={{ backgroundColor: '#2E2530', borderColor: 'rgba(160, 113, 127, 0.2)' }}>
                    <span className="px-4 text-xl font-bold" style={{ color: '#A0717F', fontFamily: "'Georgia', serif" }}>$</span>
                    <input type="number" step="0.01" min="0" placeholder="0.00" disabled={remainingBalance <= 0}
                      value={amount} onChange={(e) => setAmount(e.target.value)} required
                      className="w-full bg-transparent text-3xl font-bold placeholder-gray-500 focus:outline-none"
                      style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }} />
                  </div>
                  {errors.amount && <p className="text-red-400 text-xs mt-2">{errors.amount[0]}</p>}
                </div>

                <div>
                  <label className="block text-xs font-semibold uppercase mb-2" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Internal Memo / Remarks</label>
                  <textarea maxLength={500} rows={2} value={notes} onChange={(e) => setNotes(e.target.value)}
                    placeholder="e.g. Card ending in 4242 processed..."
                    className="w-full px-5 py-4 rounded-xl focus:outline-none transition-all duration-300"
                    style={{ backgroundColor: '#2E2530', color: '#EAD3CD', border: '1px solid rgba(160, 113, 127, 0.2)', resize: 'none' }} />
                </div>

                <div className="pt-6" style={{ borderTop: '1px solid rgba(160, 113, 127, 0.1)' }}>
                  <button type="submit" disabled={remainingBalance <= 0}
                    className={`w-full py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 ${remainingBalance <= 0 ? 'opacity-50 cursor-not-allowed' : ''}`}
                    style={{ backgroundColor: '#A0717F', color: '#FFFFFF', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(160, 113, 127, 0.3)' }}>
                    Finalize Transaction
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div className="lg:col-span-2 space-y-8">
            <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
              <h3 className="text-xl font-bold mb-6" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Ledger Overview</h3>
              <div className="space-y-4">
                <div className="rounded-xl p-5" style={{ backgroundColor: '#2E2530', border: '1px solid rgba(160, 113, 127, 0.2)' }}>
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Total Billing Target</p>
                  <p className="text-3xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>${money(booking.total_amount)}</p>
                </div>

                <div className="rounded-xl p-5" style={{ backgroundColor: '#2E2530', border: '1px solid rgba(160, 113, 127, 0.05)' }}>
                  <div className="flex justify-between mb-3">
                    <span className="text-sm font-semibold uppercase tracking-wider" style={{ color: '#CFCBCA' }}>Collected</span>
                    <span className="font-bold" style={{ color: '#4ADE80' }}>${money(amountPaid)}</span>
                  </div>
                  {amountRefunded > 0 && (
                    <div className="flex justify-between mb-3">
                      <span className="text-sm font-semibold uppercase tracking-wider" style={{ color: '#CFCBCA' }}>Refunded</span>
                      <span className="font-bold" style={{ color: '#FACC15' }}>-${money(amountRefunded)}</span>
                    </div>
                  )}
                  {amountNetPaid > 0 && (
                    <div className="flex justify-between pt-3 border-t border-[rgba(234,211,205,0.05)]">
                      <span className="text-sm font-semibold uppercase tracking-wider" style={{ color: '#A0717F' }}>Net Realized</span>
                      <span className="font-bold" style={{ color: '#A0717F' }}>${money(amountNetPaid)}</span>
                    </div>
                  )}
                </div>

                {remainingBalance > 0 ? (
                  <div className="rounded-xl p-5" style={{ backgroundColor: 'rgba(239, 68, 68, 0.05)', border: '1px solid rgba(239, 68, 68, 0.2)' }}>
                    <p className="text-xs font-bold uppercase mb-1" style={{ color: '#F87171', letterSpacing: '0.15em' }}>Outstanding Balance</p>
                    <p className="text-3xl font-bold" style={{ color: '#F87171', fontFamily: "'Georgia', serif" }}>${money(remainingBalance)}</p>
                  </div>
                ) : (
                  <div className="rounded-xl p-5" style={{ backgroundColor: 'rgba(34, 197, 94, 0.05)', border: '1px solid rgba(34, 197, 94, 0.2)' }}>
                    <p className="text-xs font-bold uppercase mb-1" style={{ color: '#4ADE80', letterSpacing: '0.15em' }}>Account Status</p>
                    <p className="text-3xl font-bold" style={{ color: '#4ADE80', fontFamily: "'Georgia', serif" }}>Settled</p>
                  </div>
                )}
              </div>
            </div>

            <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
              <h3 className="text-xl font-bold mb-6" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Transaction Log</h3>

              {booking.payments?.length > 0 ? (
                <div className="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                  {booking.payments.map((payment) => (
                    <div key={payment.id} className="rounded-xl p-4 border"
                      style={{ backgroundColor: '#2E2530', borderColor: payment.type === 'payment' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)' }}>
                      <div className="flex justify-between items-start mb-3">
                        <div>
                          <p className="font-bold text-sm uppercase tracking-wider" style={{ color: payment.type === 'payment' ? '#4ADE80' : '#F87171' }}>
                            {payment.type === 'payment' ? 'Payment' : 'Refund'}
                          </p>
                          <p className="text-xs mt-1" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
                            {new Date(payment.payment_date).toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: '2-digit' })}
                          </p>
                        </div>
                        <span className="text-lg font-bold" style={{ color: payment.type === 'payment' ? '#4ADE80' : '#F87171' }}>
                          {payment.type === 'payment' ? '+' : '-'}${money(payment.amount)}
                        </span>
                      </div>

                      {payment.notes && <p className="text-sm italic mb-4" style={{ color: '#CFCBCA' }}>"{payment.notes}"</p>}

                      <div className="flex justify-between items-center pt-3 border-t border-[rgba(234,211,205,0.05)]">
                        <div className="flex items-center gap-2">
                          <Icon name="user" className="w-3 h-3 text-gray-500" />
                          <span className="text-xs font-semibold uppercase tracking-wider" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>{payment.processed_by_user?.name ?? payment.processedBy?.name}</span>
                        </div>
                        {payment.processed_by === user?.id && (
                          <button type="button" onClick={() => deletePayment(payment.id)} className="text-xs uppercase font-bold transition-colors" style={{ color: '#F87171', letterSpacing: '0.1em' }}>
                            Revoke
                          </button>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="rounded-xl p-8 text-center" style={{ backgroundColor: '#2E2530', border: '1px solid rgba(160, 113, 127, 0.1)' }}>
                  <p className="text-sm italic" style={{ color: 'rgba(207, 203, 202, 0.4)' }}>No financial operations have been securely logged.</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
