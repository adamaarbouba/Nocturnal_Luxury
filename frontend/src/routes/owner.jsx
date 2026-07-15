import ProtectedRoute from '../components/ProtectedRoute'
import Dashboard from '../pages/owner/Dashboard'
import HotelsIndex from '../pages/owner/hotels/Index'
import HotelShow from '../pages/owner/hotels/Show'
import HotelManage from '../pages/owner/hotels/Manage'
import HotelRequestCreate from '../pages/owner/hotel-requests/Create'
import MyRequests from '../pages/owner/hotel-requests/MyRequests'
import MaintenanceIndex from '../pages/owner/maintenance/Index'
import MaintenanceShow from '../pages/owner/maintenance/Show'
import StaffIndex from '../pages/owner/staff/Index'
import StaffApplications from '../pages/owner/staff/Applications'

const guard = (children) => <ProtectedRoute roles={['owner']}>{children}</ProtectedRoute>

export default [
  { path: '/owner', element: guard(<Dashboard />) },
  { path: '/owner/dashboard', element: guard(<Dashboard />) },
  { path: '/owner/hotels', element: guard(<HotelsIndex />) },
  { path: '/owner/hotels/:id', element: guard(<HotelShow />) },
  { path: '/owner/hotels/:id/manage', element: guard(<HotelManage />) },
  { path: '/owner/hotels/:id/staff', element: guard(<StaffIndex />) },
  { path: '/owner/hotel-requests/create', element: guard(<HotelRequestCreate />) },
  { path: '/owner/hotel-requests', element: guard(<MyRequests />) },
  { path: '/owner/maintenance', element: guard(<MaintenanceIndex />) },
  { path: '/owner/maintenance/:id', element: guard(<MaintenanceShow />) },
  { path: '/owner/staff/applications', element: guard(<StaffApplications />) },
]
