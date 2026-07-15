import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import Alert from '../../../components/Alert'
import { money, fmtDate } from './../fmt'

export default function GuestPaymentsForm() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [data, setData] = useState(null)
  const [payForm, setPayForm] = useState({ amount: '', cardholder_name: '', card_number: '', expiry_date: '', cvv: '' })
  const [payErrors, setPayErrors] = useState({})
  const [payError, setPayError] = useState('')
  const [submittingPay, setSubmittingPay] = useState(false)

  const [refundForm, setRefundForm] = useState({ amount: '', reason: '' })
  const [refundErrors, setRefundErrors] = useState({})
  const [refundError, setRefundError] = useState('')
  const [refundOk, setRefundOk] = useState('')
  const [submittingRefund, setSubmittingRefund] = useState(false)

  const load = () => api.get(`/guest/bookings/${id}/payment`).then(res => {
    setData(res.data)
    setPayForm(f => ({ ...f, amount: res.data.remainingBalance }))
    setRefundForm(f => ({ ...f, amount: res.data.amountPaid }))
  }).catch(() => setData(false))

  useEffect(() => { load() }, [id])

  if (data === null) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>
  if (data === false) return <Layout><p className="text-[#CFCBCA]">Booking not found.</p></Layout>

  const { booking, remainingBalance, amountPaid } = data

  const submitPayment = async (e) => {
    e.preventDefault()
    setSubmittingPay(true)
    setPayError('')
    setPayErrors({})
    try {
      await api.post(`/guest/bookings/${id}/payment`, payForm)
      navigate('/guest/bookings')
    } catch (err) {
      if (err.response?.status === 422 && err.response.data.errors) {
        setPayErrors(err.response.data.errors)
      } else {
        setPayError(err.response?.data?.message ?? 'Payment failed')
      }
    } finally {
      setSubmittingPay(false)
    }
  }

  const submitRefund = async (e) => {
    e.preventDefault()
    setSubmittingRefund(true)
    setRefundError('')
    setRefundOk('')
    setRefundErrors({})
    try {
      const res = await api.post(`/guest/bookings/${id}/refund-request`, refundForm)
      setRefundOk(res.data.message)
    } catch (err) {
      if (err.response?.status === 422 && err.response.data.errors) {
        setRefundErrors(err.response.data.errors)
      } else {
        setRefundError(err.response?.data?.message ?? 'Could not submit refund request')
      }
    } finally {
      setSubmittingRefund(false)
    }
  }

  return (
    <Layout>
      <div className="max-w-4xl mx-auto">
        <Breadcrumbs links={[
          { label: 'My Bookings', url: '/guest/bookings' },
          { label: 'Process Payment', url: '#' },
        ]} />

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Column: Payment Form */}
          <div className="lg:col-span-2 space-y-8">
            {remainingBalance > 0 ? (
              <div className="relative overflow-hidden rounded-2xl border border-[#4E3B46] bg-[#383537] shadow-xl">
                <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#A0717F] to-transparent opacity-50"></div>

                <div className="p-8">
                  <header className="mb-8">
                    <h2 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-2 text-center">Complete Payment</h2>
                    <p className="text-[#CFCBCA] text-sm text-center">Enter your secure payment details below.</p>
                  </header>

                  {payError && (
                    <div className="mb-6">
                      <Alert variant="error">{payError}</Alert>
                    </div>
                  )}

                  <form onSubmit={submitPayment} className="space-y-6">
                    <div className="bg-[#2A2729] rounded-xl p-6 border border-[#4E3B46] space-y-6">
                      <div className="space-y-2">
                        <label className="text-xs font-bold uppercase tracking-wider text-[#A0717F]">Payment Amount</label>
                        <div className="relative">
                          <span className="absolute left-4 top-1/2 -translate-y-1/2 text-[#A0717F] font-bold text-xl">$</span>
                          <input type="number" step="0.01" required value={payForm.amount} max={remainingBalance}
                            onChange={e => setPayForm({ ...payForm, amount: e.target.value })}
                            className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-xl pl-10 pr-5 py-4 text-2xl font-bold focus:outline-none focus:border-[#A0717F] transition-all" />
                        </div>
                        <p className="text-[10px] text-[#CFCBCA] italic text-right">Remaining Balance: ${money(remainingBalance)}</p>
                        {payErrors.amount && <p className="text-xs text-red-400 mt-1">{payErrors.amount[0]}</p>}
                      </div>

                      <div className="space-y-4">
                        <div className="space-y-2">
                          <label className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Cardholder Name</label>
                          <input type="text" required value={payForm.cardholder_name} placeholder="James Sterling"
                            onChange={e => setPayForm({ ...payForm, cardholder_name: e.target.value })}
                            className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-3 focus:outline-none focus:border-[#A0717F] transition-all placeholder-[#4E3B46]" />
                          {payErrors.cardholder_name && <p className="text-xs text-red-400 mt-1">{payErrors.cardholder_name[0]}</p>}
                        </div>

                        <div className="space-y-2">
                          <label className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Card Number</label>
                          <div className="relative">
                            <input type="text" maxLength={16} required value={payForm.card_number} placeholder="0000 0000 0000 0000"
                              onChange={e => setPayForm({ ...payForm, card_number: e.target.value })}
                              className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-3 focus:outline-none focus:border-[#A0717F] transition-all placeholder-[#4E3B46]" />
                            <div className="absolute right-4 top-1/2 -translate-y-1/2 flex gap-2">
                              <Icon name="phone" size="sm" className="text-[#4E3B46]" />
                            </div>
                          </div>
                          {payErrors.card_number && <p className="text-xs text-red-400 mt-1">{payErrors.card_number[0]}</p>}
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                          <div className="space-y-2">
                            <label className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Expiry (MM/YY)</label>
                            <input type="text" required placeholder="12/26" maxLength={5} value={payForm.expiry_date}
                              onChange={e => setPayForm({ ...payForm, expiry_date: e.target.value })}
                              className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-3 focus:outline-none focus:border-[#A0717F] transition-all placeholder-[#4E3B46]" />
                            {payErrors.expiry_date && <p className="text-xs text-red-400 mt-1">{payErrors.expiry_date[0]}</p>}
                          </div>
                          <div className="space-y-2">
                            <label className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">CVV</label>
                            <input type="password" required placeholder="***" maxLength={3} value={payForm.cvv}
                              onChange={e => setPayForm({ ...payForm, cvv: e.target.value })}
                              className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-3 focus:outline-none focus:border-[#A0717F] transition-all placeholder-[#4E3B46]" />
                            {payErrors.cvv && <p className="text-xs text-red-400 mt-1">{payErrors.cvv[0]}</p>}
                          </div>
                        </div>
                      </div>
                    </div>

                    <button type="submit" disabled={submittingPay}
                      className="w-full bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-widest text-xs py-5 rounded-xl shadow-xl transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                      <Icon name="checkmark" size="sm" />
                      {submittingPay ? 'Processing…' : 'Authorize Transaction'}
                    </button>
                  </form>
                </div>
              </div>
            ) : (
              <div className="rounded-2xl border border-[#4E3B46] bg-[#383537] p-12 text-center space-y-4 shadow-xl">
                <div className="mx-auto w-16 h-16 rounded-full bg-green-950/30 flex items-center justify-center text-green-500 mb-6">
                  <Icon name="checkmark" size="lg" />
                </div>
                <h2 className="text-3xl font-bold font-serif text-[#EAD3CD]">Booking Fully Paid</h2>
                <p className="text-[#CFCBCA] max-w-sm mx-auto">This reservation is secured. We look forward to your arrival at {booking.hotel?.name}.</p>
                <Link to="/guest/bookings" className="inline-block pt-6 text-[#A0717F] font-bold uppercase tracking-widest text-[10px] hover:text-[#EAD3CD] transition-colors">Return to Bookings</Link>
              </div>
            )}

            {/* Refund Request Section */}
            {amountPaid > 0 && (
              <div className="rounded-2xl border border-[#4E3B46] bg-[#2A2729] p-8 space-y-6 shadow-xl opacity-80 hover:opacity-100 transition-opacity">
                <header className="flex items-center justify-between border-b border-[#4E3B46] pb-4">
                  <h3 className="text-lg font-bold font-serif text-[#EAD3CD]">Request a Refund</h3>
                  <Icon name="sparkles" size="sm" className="text-[#A0717F]" />
                </header>

                <p className="text-xs text-[#CFCBCA] leading-relaxed italic">
                  Refunds must be reviewed and approved by the hotel's curators. Please allow up to 48 hours for verification.
                </p>

                {refundOk && <Alert variant="success">{refundOk}</Alert>}
                {refundError && <Alert variant="error">{refundError}</Alert>}

                <form onSubmit={submitRefund} className="space-y-4">
                  <div className="space-y-4">
                    <div className="space-y-2">
                      <label className="text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA]">Amount to Refund</label>
                      <div className="relative">
                        <span className="absolute left-4 top-1/2 -translate-y-1/2 text-[#A0717F] font-bold">$</span>
                        <input type="number" step="0.01" required value={refundForm.amount} max={amountPaid}
                          onChange={e => setRefundForm({ ...refundForm, amount: e.target.value })}
                          className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-xl pl-9 pr-5 py-3 focus:outline-none focus:border-[#A0717F] transition-all" />
                      </div>
                      {refundErrors.amount && <p className="text-xs text-red-400 mt-1">{refundErrors.amount[0]}</p>}
                    </div>

                    <div className="space-y-2">
                      <label className="text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA]">Reason for Request</label>
                      <textarea rows={3} required minLength={10} value={refundForm.reason}
                        placeholder="Please explain why you are requesting a refund..."
                        onChange={e => setRefundForm({ ...refundForm, reason: e.target.value })}
                        className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-xl px-5 py-3 text-xs focus:outline-none focus:border-[#A0717F] transition-all placeholder-[#4E3B46] resize-none" />
                      {refundErrors.reason && <p className="text-xs text-red-400 mt-1">{refundErrors.reason[0]}</p>}
                    </div>
                  </div>

                  <button type="submit" disabled={submittingRefund}
                    className="w-full border border-[#A0717F] text-[#A0717F] hover:bg-[#A0717F] hover:text-white font-bold uppercase tracking-widest text-[10px] py-4 rounded-xl transition-all disabled:opacity-50">
                    {submittingRefund ? 'Submitting…' : 'Submit Refund Request'}
                  </button>
                </form>
              </div>
            )}
          </div>

          {/* Right Column: Booking Summary */}
          <div className="space-y-6">
            <div className="rounded-2xl border border-[#4E3B46] bg-[#2A2729] p-6 shadow-xl sticky top-8">
              <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-6">Reservation Summary</h3>

              <div className="space-y-6">
                <div className="flex gap-4">
                  <div className="w-16 h-16 rounded-xl bg-[#383537] border border-[#4E3B46] flex items-center justify-center text-[#A0717F] shrink-0">
                    <Icon name="building" size="md" />
                  </div>
                  <div>
                    <h4 className="text-[#EAD3CD] font-serif italic">{booking.hotel?.name}</h4>
                    <p className="text-[10px] text-[#CFCBCA]">{booking.hotel?.city}, {booking.hotel?.country}</p>
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-4 py-4 border-t border-[#4E3B46]">
                  <div>
                    <p className="text-[10px] uppercase text-[#4E3B46] font-bold tracking-tighter">Check-in</p>
                    <p className="text-sm text-[#EAD3CD]">{fmtDate(booking.check_in_date)}</p>
                  </div>
                  <div>
                    <p className="text-[10px] uppercase text-[#4E3B46] font-bold tracking-tighter">Check-out</p>
                    <p className="text-sm text-[#EAD3CD]">{fmtDate(booking.check_out_date)}</p>
                  </div>
                </div>

                <div className="space-y-3 pt-4 border-t border-[#4E3B46]">
                  <div className="flex justify-between text-xs">
                    <span className="text-[#CFCBCA]">Total Cost</span>
                    <span className="text-[#EAD3CD]">${money(booking.total_amount)}</span>
                  </div>
                  <div className="flex justify-between text-xs">
                    <span className="text-[#CFCBCA]">Deposits Paid</span>
                    <span className="text-green-500">-${money(amountPaid)}</span>
                  </div>
                  <div className="flex justify-between items-end pt-2">
                    <span className="text-xs font-bold uppercase tracking-widest text-[#A0717F]">Due Now</span>
                    <span className="text-2xl font-bold text-[#EAD3CD]">${money(remainingBalance)}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
