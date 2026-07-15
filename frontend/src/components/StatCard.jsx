export default function StatCard({ icon, trend, label, value, footer }) {
  return (
    <div className="bg-[#383537] border border-[#4E3B46] rounded-2xl p-6 shadow-sm flex items-center gap-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
      <div className="flex items-start justify-between mb-4">
        {icon && (
          <div className="inline-flex items-center justify-center w-12 h-12 rounded-lg" style={{ backgroundColor: '#D9B5C4', color: 'white' }}>
            {icon}
          </div>
        )}

        {trend && (
          <span className={`text-xs font-semibold px-2 py-1 rounded-full ${trend.positive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
            {trend.text}
          </span>
        )}
      </div>

      <h3 className="text-sm font-semibold text-[#1A1515] mb-1">{label}</h3>
      <p className="text-3xl font-bold text-[#1A1515]">{value}</p>

      {footer && <p className="text-xs text-[#1A1515] mt-3">{footer}</p>}
    </div>
  )
}
