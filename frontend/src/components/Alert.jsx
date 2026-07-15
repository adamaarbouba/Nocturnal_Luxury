import { useState } from 'react'

const variants = {
  success: 'bg-[#1A1515]/10 text-[#1A1515] border-[#1A1515]/30',
  error: 'bg-[#D9B5C4]/10 text-[#D9B5C4] border-[#D9B5C4]/30',
  warning: 'bg-yellow-50 text-yellow-800 border-yellow-200',
  info: 'bg-[#1A1515]/20 text-[#1A1515] border-[#1A1515]/50',
}

export default function Alert({ variant = 'info', dismissible = true, title, className = '', children }) {
  const [visible, setVisible] = useState(true)
  if (!visible) return null

  return (
    <div className={`p-4 border rounded-lg ${variants[variant]} flex items-start gap-3 ${className}`} role="alert">
      <div className="flex-1">
        {title && <h4 className="font-semibold mb-1">{title}</h4>}
        <p className="text-sm">{children}</p>
      </div>
      {dismissible && (
        <button type="button" onClick={() => setVisible(false)} className="text-sm font-semibold hover:opacity-70 transition">
          ✕
        </button>
      )}
    </div>
  )
}
