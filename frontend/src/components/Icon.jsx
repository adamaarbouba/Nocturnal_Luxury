const sizes = {
  xs: 'w-3 h-3',
  sm: 'w-4 h-4',
  md: 'w-6 h-6',
  lg: 'w-8 h-8',
  xl: 'w-10 h-10',
  '2xl': 'w-16 h-16',
}

// name -> [svgProps, paths]; stroke icons default, fill icons flagged
const icons = {
  user: { stroke: true, paths: ['M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'] },
  briefcase: { stroke: true, paths: ['M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'] },
  'chevron-down': { stroke: true, paths: ['M19 14l-7 7m0 0l-7-7m7 7V3'] },
  check: { stroke: false, paths: ['M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'] },
  checkmark: { stroke: true, paths: ['M5 13l4 4L19 7'] },
  'map-pin': { stroke: true, paths: ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'M15 11a3 3 0 11-6 0 3 3 0 016 0z'] },
  home: { stroke: false, paths: ['M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V20a2 2 0 002 2h12a2 2 0 002-2v-9.586l.707.707a1 1 0 001.414-1.414l-7-7z'] },
  building: { stroke: false, paths: ['M10.5 1.5H5a1.5 1.5 0 00-1.5 1.5v2H1.5a.5.5 0 000 1h1v2h-1a.5.5 0 000 1h1v2h-1a.5.5 0 000 1h1v2h-1a.5.5 0 000 1h1.5V15a1.5 1.5 0 001.5 1.5h10a1.5 1.5 0 001.5-1.5V3a1.5 1.5 0 00-1.5-1.5zm0 13H5V3h5.5v11z'] },
  sparkles: { stroke: true, paths: ['M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z'] },
  'clipboard-check': { stroke: true, paths: ['M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'] },
  star: { stroke: false, paths: ['M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z'] },
  search: { stroke: true, paths: ['M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'] },
  calendar: { stroke: true, paths: ['M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'] },
  menu: { stroke: true, paths: ['M4 6h16M4 12h16M4 18h16'] },
  close: { stroke: true, paths: ['M6 18L18 6M6 6l12 12'] },
  settings: { stroke: true, paths: ['M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z'] },
  logout: { stroke: true, paths: ['M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'] },
  'arrow-left': { stroke: true, paths: ['M15 19l-7-7 7-7'] },
  'arrow-right': { stroke: true, paths: ['M9 5l7 7-7 7'] },
  plus: { stroke: true, paths: ['M12 4v16m8-8H4'] },
  'chart-bar': { stroke: true, paths: ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'] },
  'check-circle': { stroke: true, paths: ['M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'] },
  'magnifying-glass': { stroke: true, paths: ['M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'] },
  users: { stroke: true, paths: ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'] },
  identification: { stroke: true, paths: ['M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M15 11h3m-3 4h2'] },
  'building-office': { stroke: true, paths: ['M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'] },
  'shield-check': { stroke: true, paths: ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'] },
  'shield-exclamation': { stroke: true, paths: ['M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01'] },
  phone: { stroke: true, paths: ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'] },
}

export default function Icon({ name, size = 'md', className = '', hoverable = false, ...props }) {
  const icon = icons[name]
  const cls = `${sizes[size] ?? sizes.md} ${hoverable ? 'group-hover:text-[#D9B5C4] transition-colors' : ''} ${className}`

  if (!icon) {
    return (
      <svg className={cls} fill="currentColor" viewBox="0 0 24 24" {...props}>
        <circle cx="12" cy="12" r="10" />
      </svg>
    )
  }

  return (
    <svg
      className={cls}
      viewBox="0 0 24 24"
      fill={icon.stroke ? 'none' : 'currentColor'}
      stroke={icon.stroke ? 'currentColor' : undefined}
      {...props}
    >
      {icon.paths.map((d, i) =>
        icon.stroke ? (
          <path key={i} strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d={d} />
        ) : (
          <path key={i} d={d} />
        )
      )}
    </svg>
  )
}
