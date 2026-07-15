import { Link } from 'react-router-dom'
import { Fragment } from 'react'

export default function Breadcrumbs({ links = [] }) {
  if (!links.length) return null

  return (
    <nav className="flex items-center flex-wrap gap-2 text-[10px] font-bold uppercase tracking-widest mb-6" aria-label="Breadcrumb">
      {links.map((link, index) =>
        index !== links.length - 1 ? (
          <Fragment key={index}>
            <Link to={link.url} className="text-[#A0717F] hover:text-[#EAD3CD] transition-colors duration-200" style={{ letterSpacing: '0.15em' }}>
              {link.label}
            </Link>
            <span className="text-[#4E3B46] mx-1">/</span>
          </Fragment>
        ) : (
          <span key={index} className="text-[#CFCBCA] opacity-70" aria-current="page" style={{ letterSpacing: '0.15em' }}>
            {link.label}
          </span>
        )
      )}
    </nav>
  )
}
