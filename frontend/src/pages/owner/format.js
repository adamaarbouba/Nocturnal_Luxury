// ponytail: tiny date helpers instead of a date library
export const fmtDate = (d, withTime = false) =>
  d
    ? new Date(d).toLocaleString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        ...(withTime ? { hour: '2-digit', minute: '2-digit' } : {}),
      })
    : '—'

export const timeAgo = (d) => {
  if (!d) return '—'
  const s = Math.floor((Date.now() - new Date(d).getTime()) / 1000)
  if (s < 60) return 'just now'
  const units = [
    [31536000, 'year'],
    [2592000, 'month'],
    [86400, 'day'],
    [3600, 'hour'],
    [60, 'minute'],
  ]
  for (const [sec, name] of units) {
    if (s >= sec) {
      const n = Math.floor(s / sec)
      return `${n} ${name}${n > 1 ? 's' : ''} ago`
    }
  }
  return 'just now'
}

export const money = (v) => Number(v ?? 0).toFixed(2)

export const ucfirst = (s) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : '')
