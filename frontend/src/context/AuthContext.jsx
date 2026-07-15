import { createContext, useContext, useEffect, useState } from 'react'
import api from '../lib/api'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/user')
      .then(res => setUser(res.data.user))
      .catch(() => setUser(null))
      .finally(() => setLoading(false))
  }, [])

  const login = async (credentials) => {
    const res = await api.post('/login', credentials)
    setUser(res.data.user)
    return res.data.user
  }

  const register = async (data) => {
    const res = await api.post('/register', data)
    setUser(res.data.user)
    return res.data.user
  }

  const logout = async () => {
    await api.post('/logout')
    setUser(null)
  }

  return (
    <AuthContext.Provider value={{ user, loading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => useContext(AuthContext)

// Mirrors User::dashboardRoute() role→path mapping
export const dashboardPathFor = (user) => {
  const map = {
    admin: '/admin',
    owner: '/owner',
    receptionist: '/receptionist',
    guest: '/guest',
    cleaner: '/cleaner',
    inspector: '/inspector',
    staff: '/staff',
  }
  return map[user?.role?.slug] ?? '/'
}
