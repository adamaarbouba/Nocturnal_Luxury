// Shared helpers for receptionist pages (formats + Blade-style badge palettes).
export const money = (n, dec = 2) =>
  Number(n ?? 0).toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec })

export const fmtDate = (d) =>
  new Date(d).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })

export const fmtMonthDay = (d) =>
  new Date(d).toLocaleDateString('en-US', { month: 'short', day: '2-digit' })

export const pad5 = (id) => String(id).padStart(5, '0')

export const statusBadgeStyle = (status) =>
  ({
    pending: { background: 'rgba(234, 179, 8, 0.1)', borderColor: 'rgba(234, 179, 8, 0.3)', color: '#FACC15' },
    confirmed: { background: 'rgba(59, 130, 246, 0.1)', borderColor: 'rgba(59, 130, 246, 0.3)', color: '#60A5FA' },
    checked_in: { background: 'rgba(34, 197, 94, 0.1)', borderColor: 'rgba(34, 197, 94, 0.3)', color: '#4ADE80' },
    checked_out: { background: 'rgba(168, 85, 247, 0.1)', borderColor: 'rgba(168, 85, 247, 0.3)', color: '#C084FC' },
  }[status] ?? { background: 'rgba(239, 68, 68, 0.1)', borderColor: 'rgba(239, 68, 68, 0.3)', color: '#F87171' })

export const paymentBadgeStyle = (status) =>
  ({
    paid: { background: 'rgba(34, 197, 94, 0.1)', borderColor: 'rgba(34, 197, 94, 0.3)', color: '#4ADE80' },
    pending: { background: 'rgba(234, 179, 8, 0.1)', borderColor: 'rgba(234, 179, 8, 0.3)', color: '#FACC15' },
  }[status] ?? { background: 'rgba(207, 203, 202, 0.05)', borderColor: 'rgba(207, 203, 202, 0.2)', color: '#CFCBCA' })

// Simple prev/next pagination matching the Blade `links()` container.
export function Pagination({ meta, onPage }) {
  if (!meta || meta.last_page <= 1) return null
  const btn = (label, page, disabled) => (
    <button
      type="button"
      disabled={disabled}
      onClick={() => onPage(page)}
      className={`px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all ${disabled ? 'opacity-40 cursor-not-allowed' : ''}`}
      style={{ backgroundColor: '#4E3B46', color: '#EAD3CD', border: '1px solid rgba(160, 113, 127, 0.2)' }}
    >
      {label}
    </button>
  )
  return (
    <div className="mt-8 rounded-2xl p-5 bg-[#383537] shadow-xl border border-[rgba(234,211,205,0.05)] flex items-center justify-between">
      {btn('Previous', meta.current_page - 1, meta.current_page <= 1)}
      <span className="text-xs uppercase tracking-widest" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
        Page {meta.current_page} of {meta.last_page}
      </span>
      {btn('Next', meta.current_page + 1, meta.current_page >= meta.last_page)}
    </div>
  )
}
