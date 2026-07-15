export default function Table({ headers = [], children }) {
  return (
    <div className="overflow-hidden rounded-lg border border-[#4E3B46] bg-[#383537] shadow-sm">
      <table className="w-full">
        <thead className="border-b border-[#4E3B46] bg-[#2A2729]">
          <tr>
            {headers.map((header, i) => (
              <th key={i} className="px-6 py-4 text-left text-sm font-semibold text-[#EAD3CD]">
                {header}
              </th>
            ))}
          </tr>
        </thead>
        <tbody className="divide-y divide-[#4E3B46]">{children}</tbody>
      </table>
    </div>
  )
}

export function TableRow({ className = '', children }) {
  return (
    <tr className={`hover:bg-[#F5EAE1]/50 transition ${className}`}>
      <td className="px-6 py-4 text-sm text-[#1A1515]">{children}</td>
    </tr>
  )
}
