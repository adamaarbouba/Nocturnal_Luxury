import ProtectedRoute from '../components/ProtectedRoute'
import Dashboard from '../pages/admin/Dashboard'
import UsersIndex from '../pages/admin/UsersIndex'
import UserShow from '../pages/admin/UserShow'
import HotelRequestsIndex from '../pages/admin/HotelRequestsIndex'
import HotelRequestShow from '../pages/admin/HotelRequestShow'
import HotelShow from '../pages/admin/HotelShow'

const guard = (el) => <ProtectedRoute roles={['admin']}>{el}</ProtectedRoute>

export default [
  { path: '/admin', element: guard(<Dashboard />) },
  { path: '/admin/users', element: guard(<UsersIndex />) },
  { path: '/admin/users/:id', element: guard(<UserShow />) },
  { path: '/admin/hotel-requests', element: guard(<HotelRequestsIndex />) },
  { path: '/admin/hotel-requests/:id', element: guard(<HotelRequestShow />) },
  { path: '/admin/hotels/:id', element: guard(<HotelShow />) },
]
