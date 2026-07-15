export default function Card({ image, badge, title, subtitle, content, footer, className = '', children }) {
  return (
    <div className={`rounded-[1.75rem] border border-[#4E3B46] bg-[#383537] shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden ${className}`}>
      {image && (
        <div className="relative overflow-hidden h-48 bg-gradient-to-br from-[#4E3B46] to-[#383537]">
          <img src={image} alt={title ?? 'Card'} className="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
        </div>
      )}

      <div className="p-6">
        {badge && (
          <span className="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-2" style={{ backgroundColor: '#A0717F', color: 'white' }}>
            {badge}
          </span>
        )}

        {title && <h3 className="text-lg font-bold text-[#EAD3CD] mb-2">{title}</h3>}

        {subtitle && <p className="text-sm text-[#CFCBCA] mb-3 flex items-center gap-2">{subtitle}</p>}

        {content ? (
          <p className="text-sm text-[#CFCBCA] leading-relaxed mb-4">{content}</p>
        ) : (
          <div className="text-sm text-[#CFCBCA] leading-relaxed">{children}</div>
        )}

        {footer && (
          <div className="mt-4 pt-4 border-t border-[#4E3B46] flex justify-between items-center">{footer}</div>
        )}
      </div>
    </div>
  )
}
