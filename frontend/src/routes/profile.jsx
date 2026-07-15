import ProtectedRoute from '../components/ProtectedRoute'
import ProfileShow from '../pages/profile/Show'
import ProfileEdit from '../pages/profile/Edit'
import ProfileChangePassword from '../pages/profile/ChangePassword'
import ProfileConfirmDelete from '../pages/profile/ConfirmDelete'

export default [
  { path: '/profile', element: <ProtectedRoute><ProfileShow /></ProtectedRoute> },
  { path: '/profile/edit', element: <ProtectedRoute><ProfileEdit /></ProtectedRoute> },
  { path: '/profile/password', element: <ProtectedRoute><ProfileChangePassword /></ProtectedRoute> },
  { path: '/profile/delete', element: <ProtectedRoute><ProfileConfirmDelete /></ProtectedRoute> },
]
