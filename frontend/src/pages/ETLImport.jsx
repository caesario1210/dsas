import { useState, useEffect } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import api from '../services/api'
import Navigation from '../components/Navigation'

function ETLImport({ user }) {
  const location = useLocation()
  const navigate = useNavigate()
  const [importing, setImporting] = useState(true)
  const [result, setResult] = useState(null)
  const [error, setError] = useState('')
  const [importStarted, setImportStarted] = useState(false)

  const { cleanedPath } = location.state || {}

  useEffect(() => {
    if (!cleanedPath) return

    if (!importStarted) {
      startImport()
      setImportStarted(true)
    }
  }, [cleanedPath, importStarted])

  const startImport = async () => {
    try {
      setImporting(true)

      const response = await api.post('/etl/import', {
        cleaned_path: cleanedPath,
      })

      setResult(response.data.data)
      setImporting(false)

    } catch (err) {
      setError(err.response?.data?.message || 'Import failed')
      setImporting(false)
    }
  }

  const handleUploadNew = () => {
    navigate('/upload')
  }

  const handleGoDashboard = () => {
    navigate('/dashboard')
  }

  if (!cleanedPath) {
    return (
      <div style={styles.container}>
        <Navigation user={user} />
        <header style={styles.header}>
          <h1 style={styles.title}>ETL - Import Data</h1>
        </header>
        <div style={styles.content}>
          <div style={styles.errorBox}>
            <h2>No data to import</h2>
            <p>Please complete the cleaning step first.</p>
            <button style={styles.button} onClick={handleUploadNew}>
              Upload New File
            </button>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div style={styles.container}>
      <Navigation user={user} />
      <header style={styles.header}>
        <h1 style={styles.title}>ETL - Import Data</h1>
      </header>

      <div style={styles.content}>
        {importing && (
          <div style={styles.progressBox}>
            <div style={styles.spinner}></div>
            <h2>Importing Data...</h2>
            <p>Writing cleaned data to database. This may take a moment.</p>
          </div>
        )}

        {error && !importing && (
          <div style={styles.errorBox}>
            <h2>❌ Import Failed</h2>
            <p>{error}</p>
            <button style={styles.button} onClick={handleUploadNew}>
              Upload New File
            </button>
          </div>
        )}

        {!importing && !error && result && (
          <div>
            <div style={result.status === 'success' ? styles.successBox : styles.partialBox}>
              <h2>
                {result.status === 'success' ? '✅ Import Successful' : '⚠️ Import Partially Successful'}
              </h2>
              <p>{result.message}</p>
            </div>

            <div style={styles.statsGrid}>
              <div style={styles.statCard}>
                <div style={styles.statValue}>{result.summary.total_rows}</div>
                <div style={styles.statLabel}>Total Rows</div>
              </div>
              <div style={{...styles.statCard, ...styles.statSuccess}}>
                <div style={{...styles.statValue, color: '#28a745'}}>{result.summary.imported}</div>
                <div style={styles.statLabel}>Imported</div>
              </div>
              {result.summary.skipped > 0 && (
                <div style={{...styles.statCard, ...styles.statWarning}}>
                  <div style={{...styles.statValue, color: '#856404'}}>{result.summary.skipped}</div>
                  <div style={styles.statLabel}>Skipped (Duplicates)</div>
                </div>
              )}
              {result.summary.failed > 0 && (
                <div style={{...styles.statCard, ...styles.statDanger}}>
                  <div style={{...styles.statValue, color: '#dc3545'}}>{result.summary.failed}</div>
                  <div style={styles.statLabel}>Failed</div>
                </div>
              )}
              {result.summary.period && (
                <div style={styles.statCard}>
                  <div style={styles.statValue}>
                    {result.summary.period.month}/{result.summary.period.year}
                  </div>
                  <div style={styles.statLabel}>Period</div>
                </div>
              )}
            </div>

            {result.errors && result.errors.length > 0 && (
              <div style={styles.errorsSection}>
                <h3>Import Errors ({result.errors.length})</h3>
                <div style={styles.errorList}>
                  {result.errors.slice(0, 20).map((err, idx) => (
                    <div key={idx} style={styles.errorItem}>
                      <span style={styles.errorLine}>Line {err.line}</span>
                      <span>{err.message}</span>
                    </div>
                  ))}
                  {result.errors.length > 20 && (
                    <div style={styles.moreErrors}>+ {result.errors.length - 20} more errors</div>
                  )}
                </div>
              </div>
            )}

            <div style={styles.actions}>
              <button style={styles.secondaryBtn} onClick={handleUploadNew}>
                Upload Another File
              </button>
              <button style={styles.primaryBtn} onClick={handleGoDashboard}>
                Go to Dashboard
              </button>
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
  progressBox: {
    backgroundColor: 'white',
    padding: '60px',
    borderRadius: '8px',
    textAlign: 'center',
    boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
  },
  spinner: {
    width: '50px',
    height: '50px',
    border: '4px solid #f3f3f3',
    borderTop: '4px solid #007bff',
    borderRadius: '50%',
    animation: 'spin 1s linear infinite',
    margin: '0 auto 24px',
  },
  successBox: {
    backgroundColor: '#d4edda',
    padding: '40px',
    borderRadius: '8px',
    textAlign: 'center',
    border: '2px solid #28a745',
    marginBottom: '24px',
  },
  partialBox: {
    backgroundColor: '#fff3cd',
    padding: '40px',
    borderRadius: '8px',
    textAlign: 'center',
    border: '2px solid #ffc107',
    marginBottom: '24px',
  },
  errorBox: {
    backgroundColor: '#fee',
    padding: '40px',
    borderRadius: '8px',
    textAlign: 'center',
    border: '2px solid #dc3545',
  },
  statsGrid: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))',
    gap: '16px',
    marginBottom: '24px',
  },
  statCard: {
    padding: '20px',
    backgroundColor: 'white',
    borderRadius: '8px',
    textAlign: 'center',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
  },
  statSuccess: {
    backgroundColor: '#d4edda',
    border: '2px solid #28a745',
  },
  statDanger: {
    backgroundColor: '#f8d7da',
    border: '2px solid #dc3545',
  },
  statWarning: {
    backgroundColor: '#fff3cd',
    border: '2px solid #ffc107',
  },
  statValue: {
    fontSize: '32px',
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '8px',
  },
  statLabel: {
    fontSize: '14px',
    color: '#666',
    textTransform: 'uppercase',
  },
  errorsSection: {
    backgroundColor: 'white',
    borderRadius: '8px',
    padding: '24px',
    marginBottom: '24px',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
  },
  errorList: {
    backgroundColor: '#fff5f5',
    border: '1px solid #f5c6cb',
    borderRadius: '4px',
    padding: '12px',
    maxHeight: '300px',
    overflowY: 'auto',
    marginTop: '12px',
  },
  errorItem: {
    padding: '8px 0',
    borderBottom: '1px solid #f5c6cb',
    fontSize: '13px',
    display: 'flex',
    gap: '12px',
  },
  errorLine: {
    fontWeight: 'bold',
    color: '#dc3545',
    minWidth: '70px',
  },
  moreErrors: {
    textAlign: 'center',
    padding: '12px',
    color: '#666',
    fontStyle: 'italic',
  },
  actions: {
    display: 'flex',
    gap: '16px',
    justifyContent: 'center',
  },
  primaryBtn: {
    padding: '12px 32px',
    backgroundColor: '#28a745',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    fontSize: '16px',
    cursor: 'pointer',
    fontWeight: '500',
  },
  secondaryBtn: {
    padding: '12px 32px',
    backgroundColor: '#6c757d',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    fontSize: '16px',
    cursor: 'pointer',
    fontWeight: '500',
  },
  button: {
    padding: '12px 32px',
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

export default ETLImport
