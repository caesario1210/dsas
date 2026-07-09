import { useState, useEffect } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import api from '../services/api'
import Navigation from '../components/Navigation'
import ValidationProgress from '../components/ValidationProgress'
import ValidationSummary from '../components/ValidationSummary'
import ErrorsTable from '../components/ErrorsTable'

function ETLValidation({ user }) {
  const location = useLocation()
  const navigate = useNavigate()
  const [validating, setValidating] = useState(true)
  const [validationResult, setValidationResult] = useState(null)
  const [error, setError] = useState('')

  useEffect(() => {
    if (!location.state?.previewData) {
      navigate('/upload')
      return
    }

    validateData()
  }, [])

  const validateData = async () => {
    try {
      const { temp_path } = location.state.previewData

      const response = await api.post('/etl/validate', { temp_path })

      setValidationResult(response.data.data)
      setValidating(false)

    } catch (err) {
      setError(err.response?.data?.message || 'Validation failed')
      setValidating(false)
    }
  }

  const handleProceedToCleaning = () => {
    navigate('/etl/clean', {
      state: {
        tempPath: location.state.previewData.temp_path,
        validRows: validationResult.valid_rows,
      },
    })
  }

  const handleBackToUpload = () => {
    navigate('/upload')
  }

  return (
    <div style={styles.container}>
      <Navigation user={user} />
      <header style={styles.header}>
        <h1 style={styles.title}>ETL Validation</h1>
        <p style={styles.subtitle}>Validating uploaded data against business rules</p>
      </header>

      <div style={styles.content}>
        {error && (
          <div style={styles.errorBox}>
            <strong>Error:</strong> {error}
            <button style={styles.backBtn} onClick={handleBackToUpload}>
              Back to Upload
            </button>
          </div>
        )}

        {validating && !error && <ValidationProgress />}

        {!validating && !error && validationResult && (
          <>
            <ValidationSummary result={validationResult} />

            {validationResult.batch_error && (
              <div style={styles.batchError}>
                <h3 style={styles.errorTitle}>Critical Error (Entire File Rejected)</h3>
                <div style={styles.errorContent}>
                  <strong>Rule:</strong> {validationResult.batch_error.rule}<br />
                  <strong>Message:</strong> {validationResult.batch_error.message}<br />
                  {validationResult.batch_error.detected_periods && (
                    <>
                      <strong>Detected Periods:</strong>{' '}
                      {validationResult.batch_error.detected_periods.join(', ')}
                    </>
                  )}
                </div>
              </div>
            )}

            {validationResult.errors && validationResult.errors.length > 0 && (
              <ErrorsTable
                title="Validation Errors"
                errors={validationResult.errors}
                type="validation"
              />
            )}

            {validationResult.duplicates && validationResult.duplicates.length > 0 && (
              <ErrorsTable
                title="Duplicate Invoices (Within CSV)"
                errors={validationResult.duplicates}
                type="duplicate"
              />
            )}

            {validationResult.missing_values && validationResult.missing_values.length > 0 && (
              <ErrorsTable
                title="Missing Required Values"
                errors={validationResult.missing_values}
                type="missing"
              />
            )}

            <div style={styles.actions}>
              <button style={styles.backBtn} onClick={handleBackToUpload}>
                Upload New File
              </button>

              {validationResult.status === 'passed' && (
                <button style={styles.proceedBtn} onClick={handleProceedToCleaning}>
                  Proceed to Cleaning & Import
                </button>
              )}

              {validationResult.status === 'failed' && (
                <div style={styles.failedMessage}>
                  <p>Cannot proceed to next step. Please fix errors and upload again.</p>
                </div>
              )}
            </div>
          </>
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
    backgroundColor: 'transparent',
  },
  title: {
    fontSize: '24px',
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '4px',
  },
  subtitle: {
    fontSize: '14px',
    color: '#666',
    margin: 0,
  },
  content: {
    padding: '40px',
    maxWidth: '1400px',
    margin: '0 auto',
  },
  errorBox: {
    padding: '20px',
    backgroundColor: '#fee',
    color: '#c33',
    borderRadius: '8px',
    border: '1px solid #fcc',
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  batchError: {
    backgroundColor: 'white',
    padding: '24px',
    borderRadius: '8px',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
    marginBottom: '24px',
    border: '2px solid #dc3545',
  },
  errorTitle: {
    fontSize: '18px',
    fontWeight: 'bold',
    color: '#dc3545',
    marginBottom: '12px',
  },
  errorContent: {
    fontSize: '14px',
    color: '#333',
    lineHeight: '1.6',
  },
  actions: {
    display: 'flex',
    justifyContent: 'center',
    gap: '16px',
    marginTop: '32px',
    alignItems: 'center',
  },
  backBtn: {
    padding: '12px 32px',
    backgroundColor: '#6c757d',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    cursor: 'pointer',
    fontSize: '16px',
    fontWeight: '500',
  },
  proceedBtn: {
    padding: '12px 32px',
    backgroundColor: '#28a745',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    cursor: 'pointer',
    fontSize: '16px',
    fontWeight: '500',
  },
  failedMessage: {
    padding: '12px 24px',
    backgroundColor: '#fff3cd',
    color: '#856404',
    borderRadius: '4px',
    border: '1px solid #ffeaa7',
  },
}

export default ETLValidation
