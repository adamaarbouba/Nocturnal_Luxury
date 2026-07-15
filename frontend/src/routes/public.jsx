import Welcome from '../pages/public/Welcome'
import Login from '../pages/public/Login'
import Register from '../pages/public/Register'

export default [
  { path: '/', element: <Welcome /> },
  { path: '/login', element: <Login /> },
  { path: '/register', element: <Register /> },
]
