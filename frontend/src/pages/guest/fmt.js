// Tiny formatting helpers shared by the guest pages (ports of Blade's
// number_format / Carbon format calls).
export const money = (n, dec = 2) =>
  Number(n ?? 0).toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec })

export const fmtDate = (d, opts = { month: 'short', day: '2-digit', year: 'numeric' }) =>
  d ? new Date(d).toLocaleDateString('en-US', opts) : '-'

export const nightsBetween = (a, b) =>
  Math.round(Math.abs(new Date(b) - new Date(a)) / 86400000)

export const ucfirst = (s) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : '')

export const limit = (s, n) => (s && s.length > n ? s.slice(0, n) + '...' : s ?? '')
