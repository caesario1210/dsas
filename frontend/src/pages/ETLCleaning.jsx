import { useState, useEffect } from 'react'
import { useNavigate, useLocation } from 'react-router-dom'
import api from '../services/api'
import Navigation from '../components/Navigation'
import CleaningProgress from '../components/CleaningProgress'
import CleaningSummary from '../components/CleaningSummary'

function ETLCleaning({ user }) {
  const navigate = useNavigate()
  const location = useLocation()
  const [status, setStatus] = useState('loading')
  const [result, setResult] = useState(null)
  const [error, setError] = useState(null)

  const { tempPath, validRows } = location.state || {}

  useEffect(() => {
    if (!tempPath || !validRows) {
      navigate('/upload')
      return
    }

    startCleaning()
  }, [])

  const startCleaning = async () => {
    try {
      setStatus('cleaning')

      const response = await api.post('/etl/clean', {
        temp_path: tempPath,
        valid_rows: validRows,
      })

      setResult(response.data.data)
      setStatus('completed')

    } catch (err) {
      setError(err.response?.data?.message || 'Cleaning failed')
      setStatus('error')
    }
  }

  const handleBackToUpload = () => {
    navigate('/upload')
  }

  const handleProceedToImport = () => {
    navigate('/etl/import', {
      state: {
        cleanedPath: result.cleaned_path,
        cleanedData: result.cleaned_data,
        summary: result.summary,
      }
    })
  }

  return (
    <div style={styles.container}>
      <Navigation user={user} />
      <header style={styles.header}>
        <h1 style={styles.title}>ETL - Data Cleaning & Transformation</h1>
      </header>

      <div style={styles.content}>
        {status === 'loading' && (
          <div style={styles.messageBox}>
            <p>Loading...</p>
          </div>
        )}

        {status === 'cleaning' && (
          <CleaningProgress />
        )}

        {status === 'error' && (
          <div style={styles.errorBox}>
            <h2>❌ Cleaning Failed</h2>
            <p>{error}</p>
            <button style={styles.button} onClick={handleBackToUpload}>
              Back to Upload
            </button>
          </div>
        )}

        {status === 'completed' && result && (
          <div>
            <CleaningSummary result={result} />
            
            <div style={styles.actions}>
              <button 
                style={styles.secondaryButton} 
                onClick={handleBackToUpload}
              >
                Upload New File
              </button>
              
              {result.status === 'success' && (
                <button 
                  style={styles.primaryButton} 
                  onClick={handleProceedToImport}
                >
                  Proceed to Import →
                </button>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  )
}

const styles = {
  container: {
    minHeight: '100vh',
    backgroundColor: '#f5f5f5',
  },
  header: {
    backgroundColor: 'transparent',
    padding: '20px 40px',
    borderBottom: '1px solid #e0e0e0',
  },
  title: {
    fontSize: '24px',
    fontWeight: 'bold',
    color: '#333',
    margin: 0,
  },
  content: {
    padding: '40px',
    maxWidth: '1200px',
    margin: '0 auto',
  },
  messageBox: {
    backgroundColor: 'white',
    padding: '40px',
    borderRadius: '8px',
    textAlign: 'center',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
  },
  errorBox: {
    backgroundColor: 'white',
    padding: '40px',
    borderRadius: '8px',
    textAlign: 'center',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
    color: '#dc3545',
  },
  actions: {
    display: 'flex',
    gap: '16px',
    justifyContent: 'center',
    marginTop: '32px',
  },
  primaryButton: {
    padding: '12px 24px',
    backgroundColor: '#28a745',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    fontSize: '16px',
    cursor: 'pointer',
    fontWeight: '500',
  },
  secondaryButton: {
    padding: '12px 24px',
    backgroundColor: '#6c757d',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    fontSize: '16px',
    cursor: 'pointer',
    fontWeight: '500',
  },
  button: {
    padding: '12px 24px',
    backgroundColor: '#007bff',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    fontSize: '16px',
    cursor: 'pointer',
    fontWeight: '500',
    marginTop: '20px',
  },
}

export default ETLCleaning
