import ProtectedRoute from '../components/ProtectedRoute'
import StaffDashboard from '../pages/staff/Dashboard'
import StaffHotelsIndex from '../pages/staff/hotels/Index'
import StaffHotelsApply from '../pages/staff/hotels/Apply'
import MyApplications from '../pages/staff/MyApplications'
import CleanerDashboard from '../pages/cleaner/Dashboard'
import CleanerCompleteRoom from '../pages/cleaner/CompleteRoom'
import InspectorDashboard from '../pages/inspector/Dashboard'
import InspectorInspectRoom from '../pages/inspector/InspectRoom'

// Browse/apply/my-applications routes are open to cleaner, inspector, receptionist, and staff (web.php: role:cleaner,inspector,receptionist group + separately routed for staff via same controller).
const staffBrowseRoles = ['cleaner', 'inspector', 'receptionist', 'staff']

export default [
  { path: '/staff/dashboard', element: <ProtectedRoute roles={['staff']}><StaffDashboard /></ProtectedRoute> },
  { path: '/staff/hotels', element: <ProtectedRoute roles={staffBrowseRoles}><StaffHotelsIndex /></ProtectedRoute> },
  { path: '/staff/hotels/:id/apply', element: <ProtectedRoute roles={staffBrowseRoles}><StaffHotelsApply /></ProtectedRoute> },
  { path: '/staff/my-applications', element: <ProtectedRoute roles={staffBrowseRoles}><MyApplications /></ProtectedRoute> },
  { path: '/cleaner/dashboard', element: <ProtectedRoute roles={['cleaner']}><CleanerDashboard /></ProtectedRoute> },
  { path: '/cleaner/rooms/:id/complete', element: <ProtectedRoute roles={['cleaner']}><CleanerCompleteRoom /></ProtectedRoute> },
  { path: '/inspector/dashboard', element: <ProtectedRoute roles={['inspector']}><InspectorDashboard /></ProtectedRoute> },
  { path: '/inspector/rooms/:id/inspect', element: <ProtectedRoute roles={['inspector']}><InspectorInspectRoom /></ProtectedRoute> },
]
