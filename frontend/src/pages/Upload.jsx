import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../services/api'
import Navigation from '../components/Navigation'
import FileUploader from '../components/FileUploader'
import PreviewTable from '../components/PreviewTable'

function Upload({ user }) {
  const [file, setFile] = useState(null)
  const [uploading, setUploading] = useState(false)
  const [previewData, setPreviewData] = useState(null)
  const [error, setError] = useState('')
  const navigate = useNavigate()

  const handleFileSelect = async (selectedFile) => {
    setFile(selectedFile)
    setError('')
    setPreviewData(null)
    setUploading(true)

    try {
      const formData = new FormData()
      formData.append('file', selectedFile)

      const response = await api.post('/upload/file', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      setPreviewData(response.data.data)
    } catch (err) {
      setError(err.response?.data?.message || 'Upload failed')
    } finally {
      setUploading(false)
    }
  }

  const handleProceed = () => {
    navigate('/etl/validate', { state: { previewData } })
  }

  const handleReset = () => {
    setFile(null)
    setPreviewData(null)
    setError('')
  }

  const handleDownloadTemplate = async () => {
    try {
      const response = await api.get('/upload/template', {
        responseType: 'blob',
      })

      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', 'sales_template.csv')
      document.body.appendChild(link)
      link.click()
      link.remove()
    } catch (err) {
      setError('Failed to download template')
    }
  }

  return (
    <div style={styles.container}>
      <Navigation user={user} />
      <header style={styles.header}>
        <div>
          <h1 style={styles.title}>Upload Data</h1>
          <p style={styles.subtitle}>Upload CSV or XLSX file for ETL processing</p>
        </div>
        <div style={styles.headerActions}>
          <button style={styles.templateBtn} onClick={handleDownloadTemplate}>
            Download Template
          </button>
        </div>
      </header>

      <div style={styles.content}>
        {error && (
          <div style={styles.error}>
            <strong>Error:</strong> {error}
          </div>
        )}

        {!previewData && (
          <FileUploader
            onFileSelect={handleFileSelect}
            uploading={uploading}
          />
        )}

        {previewData && (
          <div style={styles.previewSection}>
            <div style={styles.fileInfo}>
              <h2 style={styles.sectionTitle}>File Information</h2>
              <div style={styles.infoGrid}>
                <div style={styles.infoItem}>
                  <span style={styles.infoLabel}>Filename:</span>
                  <span style={styles.infoValue}>{previewData.filename}</span>
                </div>
                <div style={styles.infoItem}>
                  <span style={styles.infoLabel}>Size:</span>
                  <span style={styles.infoValue}>
                    {(previewData.size / 1024).toFixed(2)} KB
                  </span>
                </div>
                <div style={styles.infoItem}>
                  <span style={styles.infoLabel}>Total Rows:</span>
                  <span style={styles.infoValue}>{previewData.rows_count}</span>
                </div>
                <div style={styles.infoItem}>
                  <span style={styles.infoLabel}>Columns:</span>
                  <span style={styles.infoValue}>{previewData.columns.length}</span>
                </div>
              </div>
            </div>

            <PreviewTable
              columns={previewData.columns}
              data={previewData.preview}
              totalRows={previewData.rows_count}
            />

            <div style={styles.actions}>
              <button style={styles.resetBtn} onClick={handleReset}>
                Upload Another File
              </button>
              <button style={styles.proceedBtn} onClick={handleProceed}>
                Proceed to Validation
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
    backgroundColor: 'white',
    padding: '12px 24px',
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  title: {
    fontSize: '20px',
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '2px',
  },
  subtitle: {
    fontSize: '13px',
    color: '#666',
    margin: 0,
  },
  headerActions: {
    display: 'flex',
    alignItems: 'center',
    gap: '8px',
  },
  templateBtn: {
    padding: '6px 12px',
    backgroundColor: '#28a745',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    cursor: 'pointer',
    fontSize: '13px',
  },
  content: {
    padding: '20px 24px',
    maxWidth: '1400px',
    margin: '0 auto',
  },
  error: {
    padding: '16px',
    backgroundColor: '#fee',
    color: '#c33',
    borderRadius: '4px',
    marginBottom: '20px',
    border: '1px solid #fcc',
  },
  previewSection: {
    display: 'flex',
    flexDirection: 'column',
    gap: '24px',
  },
  fileInfo: {
    backgroundColor: 'white',
    padding: '24px',
    borderRadius: '8px',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
  },
  sectionTitle: {
    fontSize: '18px',
    fontWeight: 'bold',
    marginBottom: '16px',
    color: '#333',
  },
  infoGrid: {
    display: 'grid',
    gridTemplateColumns: 'repeat(4, 1fr)',
    gap: '16px',
  },
  infoItem: {
    display: 'flex',
    flexDirection: 'column',
    gap: '4px',
  },
  infoLabel: {
    fontSize: '12px',
    color: '#666',
    fontWeight: '500',
  },
  infoValue: {
    fontSize: '16px',
    color: '#333',
    fontWeight: 'bold',
  },
  actions: {
    display: 'flex',
    justifyContent: 'center',
    gap: '16px',
  },
  resetBtn: {
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
    backgroundColor: '#007bff',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    cursor: 'pointer',
    fontSize: '16px',
    fontWeight: '500',
  },
}

export default Upload
