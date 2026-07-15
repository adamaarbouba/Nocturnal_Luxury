// Ports Laravel's paginator links() for admin pages, styled per users/index.blade.php .pagination CSS.
export default function Pagination({ paginator, onPage }) {
  if (!paginator || paginator.last_page <= 1) return null

  const { current_page: current, last_page: last } = paginator
  const pages = Array.from({ length: last }, (_, i) => i + 1)

  const base = {
    display: 'flex', alignItems: 'center', justifyContent: 'center',
    minWidth: '2.5rem', height: '2.5rem', padding: '0 0.75rem',
    fontSize: '0.75rem', fontWeight: 'bold', textTransform: 'uppercase', letterSpacing: '0.1em',
    border: '1px solid #4E3B46', borderRadius: '0.75rem',
    transition: 'all 0.3s ease', color: '#CFCBCA', backgroundColor: '#2A2729', cursor: 'pointer',
  }
  const active = { ...base, backgroundColor: '#A0717F', color: '#FFFFFF', borderColor: '#A0717F', boxShadow: '0 4px 12px rgba(160, 113, 127, 0.3)', cursor: 'default' }
  const disabled = { ...base, opacity: 0.3, cursor: 'not-allowed', backgroundColor: 'transparent' }

  return (
    <ul style={{ display: 'flex', gap: '0.5rem', justifyContent: 'center', flexWrap: 'wrap', listStyle: 'none' }}>
      <li>
        <button type="button" style={current === 1 ? disabled : base} disabled={current === 1} onClick={() => onPage(current - 1)}>
          &lsaquo;
        </button>
      </li>
      {pages.map((p) => (
        <li key={p}>
          <button type="button" style={p === current ? active : base} disabled={p === current} onClick={() => onPage(p)}>
            {p}
          </button>
        </li>
      ))}
      <li>
        <button type="button" style={current === last ? disabled : base} disabled={current === last} onClick={() => onPage(current + 1)}>
          &rsaquo;
        </button>
      </li>
    </ul>
  )
}
