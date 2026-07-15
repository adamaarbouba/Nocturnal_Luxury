const fieldClass =
  'w-full px-4 py-3 border border-[#4E3B46] bg-[#2A2729] rounded-lg text-[#CFCBCA] placeholder:text-[#CFCBCA]/60 focus:outline-none focus:border-[#A0717F] focus:ring-2 focus:ring-[#A0717F]/20 transition'

export default function FormInput({ label, name, type = 'text', required = false, placeholder = '', rows = 4, hint, children, ...props }) {
  return (
    <div className="mb-6">
      {label && (
        <label htmlFor={name} className="block text-sm font-semibold text-[#CFCBCA] mb-2">
          {label}
          {required && <span className="text-[#A0717F]">*</span>}
        </label>
      )}

      {type === 'textarea' ? (
        <textarea id={name} name={name} rows={rows} placeholder={placeholder} className={fieldClass} {...props} />
      ) : type === 'select' ? (
        <select
          id={name}
          name={name}
          className="w-full px-4 py-3 border border-[#4E3B46] bg-[#2A2729] rounded-lg text-[#CFCBCA] focus:outline-none focus:border-[#A0717F] focus:ring-2 focus:ring-[#A0717F]/20 transition"
          {...props}
        >
          {children}
        </select>
      ) : (
        <input type={type} id={name} name={name} placeholder={placeholder} className={fieldClass} {...props} />
      )}

      {hint && <p className="mt-1 text-xs text-[#CFCBCA]">{hint}</p>}
    </div>
  )
}
