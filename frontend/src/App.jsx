import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { useState, useEffect } from 'react'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import Upload from './pages/Upload'
import ETLValidation from './pages/ETLValidation'
import ETLCleaning from './pages/ETLCleaning'
import ETLImport from './pages/ETLImport'
import Admin from './pages/Admin'
import api from './services/api'

function App() {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const token = localStorage.getItem('token')
    if (token) {
      api.get('/user')
        .then(response => {
          setUser(response.data.user)
          setLoading(false)
        })
        .catch(() => {
          localStorage.removeItem('token')
          setLoading(false)
        })
    } else {
      setLoading(false)
    }
  }, [])

  const PrivateRoute = ({ children }) => {
    if (loading) return <div>Loading...</div>
    return user ? children : <Navigate to="/login" />
  }

  const AdminRoute = ({ children }) => {
    if (loading) return <div>Loading...</div>
    if (!user) return <Navigate to="/login" />
    return user.role?.name === 'Admin' ? children : <Navigate to="/dashboard" />
  }

  if (loading) {
    return <div>Loading...</div>
  }

  return (
    <Router>
      <Routes>
        <Route path="/login" element={user ? <Navigate to="/dashboard" /> : <Login setUser={setUser} />} />
        <Route path="/dashboard" element={<PrivateRoute><Dashboard user={user} /></PrivateRoute>} />
        <Route path="/upload" element={<AdminRoute><Upload user={user} /></AdminRoute>} />
        <Route path="/etl/validate" element={<AdminRoute><ETLValidation user={user} /></AdminRoute>} />
        <Route path="/etl/clean" element={<AdminRoute><ETLCleaning user={user} /></AdminRoute>} />
        <Route path="/etl/import" element={<AdminRoute><ETLImport user={user} /></AdminRoute>} />
        <Route path="/admin" element={<AdminRoute><Admin user={user} /></AdminRoute>} />
        <Route path="/" element={<Navigate to="/dashboard" />} />
      </Routes>
    </Router>
  )
}

export default App
