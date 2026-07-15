import { BrowserRouter, Routes, Route } from 'react-router-dom'
import { AuthProvider } from './context/AuthContext'
import publicRoutes from './routes/public'
import profileRoutes from './routes/profile'
import adminRoutes from './routes/admin'
import ownerRoutes from './routes/owner'
import guestRoutes from './routes/guest'
import receptionistRoutes from './routes/receptionist'
import staffopsRoutes from './routes/staffops'

const routes = [
  ...publicRoutes,
  ...profileRoutes,
  ...adminRoutes,
  ...ownerRoutes,
  ...guestRoutes,
  ...receptionistRoutes,
  ...staffopsRoutes,
]

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          {routes.map(r => <Route key={r.path} path={r.path} element={r.element} />)}
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  )
}
