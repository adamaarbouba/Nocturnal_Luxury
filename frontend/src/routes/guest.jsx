import ProtectedRoute from '../components/ProtectedRoute'
import GuestDashboard from '../pages/guest/Dashboard'
import GuestHotelsIndex from '../pages/guest/hotels/Index'
import GuestHotelShow from '../pages/guest/hotels/Show'
import GuestBookingsIndex from '../pages/guest/bookings/Index'
import GuestBookingsCreate from '../pages/guest/bookings/Create'
import GuestBookingsConfirmation from '../pages/guest/bookings/Confirmation'
import GuestPaymentsForm from '../pages/guest/payments/Form'
import GuestReviewsIndex from '../pages/guest/reviews/Index'
import GuestReviewsCreate from '../pages/guest/reviews/Create'
import GuestReviewsShow from '../pages/guest/reviews/Show'

const guard = (el) => <ProtectedRoute roles={['guest']}>{el}</ProtectedRoute>

export default [
  { path: '/guest', element: guard(<GuestDashboard />) },
  { path: '/guest/dashboard', element: guard(<GuestDashboard />) },
  { path: '/guest/hotels', element: guard(<GuestHotelsIndex />) },
  { path: '/guest/hotels/:id', element: guard(<GuestHotelShow />) },
  { path: '/guest/bookings', element: guard(<GuestBookingsIndex />) },
  { path: '/guest/rooms/:id/book', element: guard(<GuestBookingsCreate />) },
  { path: '/guest/bookings/:id/confirmation', element: guard(<GuestBookingsConfirmation />) },
  { path: '/guest/bookings/:id/payment', element: guard(<GuestPaymentsForm />) },
  { path: '/guest/reviews', element: guard(<GuestReviewsIndex />) },
  { path: '/guest/bookings/:id/review', element: guard(<GuestReviewsCreate />) },
  { path: '/guest/reviews/:id', element: guard(<GuestReviewsShow />) },
]
