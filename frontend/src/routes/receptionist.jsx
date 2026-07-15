import ProtectedRoute from '../components/ProtectedRoute'
import Dashboard from '../pages/receptionist/Dashboard'
import BookingsIndex from '../pages/receptionist/BookingsIndex'
import BookingsCreate from '../pages/receptionist/BookingsCreate'
import BookingsShow from '../pages/receptionist/BookingsShow'
import CheckInIndex from '../pages/receptionist/CheckInIndex'
import CheckInShow from '../pages/receptionist/CheckInShow'
import CheckOutIndex from '../pages/receptionist/CheckOutIndex'
import CheckOutShow from '../pages/receptionist/CheckOutShow'
import PaymentsForm from '../pages/receptionist/PaymentsForm'

const guard = (element) => <ProtectedRoute roles={['receptionist']}>{element}</ProtectedRoute>

export default [
  { path: '/receptionist/dashboard', element: guard(<Dashboard />) },

  { path: '/receptionist/check-in', element: guard(<CheckInIndex />) },
  { path: '/receptionist/check-in/:id', element: guard(<CheckInShow />) },

  { path: '/receptionist/check-out', element: guard(<CheckOutIndex />) },
  { path: '/receptionist/check-out/:id', element: guard(<CheckOutShow />) },

  { path: '/receptionist/bookings', element: guard(<BookingsIndex />) },
  { path: '/receptionist/bookings/create', element: guard(<BookingsCreate />) },
  { path: '/receptionist/bookings/status/:status', element: guard(<BookingsIndex />) },
  { path: '/receptionist/bookings/:id', element: guard(<BookingsShow />) },

  { path: '/receptionist/bookings/:id/payment', element: guard(<PaymentsForm />) },
]
