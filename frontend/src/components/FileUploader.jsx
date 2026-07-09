import { useState, useRef } from 'react'

function FileUploader({ onFileSelect, uploading }) {
  const [dragActive, setDragActive] = useState(false)
  const inputRef = useRef(null)

  const handleDrag = (e) => {
    e.preventDefault()
    e.stopPropagation()
    if (e.type === 'dragenter' || e.type === 'dragover') {
      setDragActive(true)
    } else if (e.type === 'dragleave') {
      setDragActive(false)
    }
  }

  const handleDrop = (e) => {
    e.preventDefault()
    e.stopPropagation()
    setDragActive(false)

    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFile(e.dataTransfer.files[0])
    }
  }

  const handleChange = (e) => {
    e.preventDefault()
    if (e.target.files && e.target.files[0]) {
      handleFile(e.target.files[0])
    }
  }

  const handleFile = (file) => {
    const validTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    const validExtensions = ['.csv', '.xlsx']
    const fileExtension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase()

    if (!validTypes.includes(file.type) && !validExtensions.includes(fileExtension)) {
      alert('Invalid file type. Please upload CSV or XLSX file.')
      return
    }

    if (file.size > 10 * 1024 * 1024) {
      alert('File size exceeds 10MB limit.')
      return
    }

    onFileSelect(file)
  }

  const handleButtonClick = () => {
    inputRef.current?.click()
  }

  return (
    <div style={styles.container}>
      <div
        style={{
          ...styles.dropzone,
          ...(dragActive ? styles.dropzoneActive : {}),
          ...(uploading ? styles.dropzoneUploading : {}),
        }}
        onDragEnter={handleDrag}
        onDragLeave={handleDrag}
        onDragOver={handleDrag}
        onDrop={handleDrop}
        onClick={!uploading ? handleButtonClick : undefined}
      >
        <input
          ref={inputRef}
          type="file"
          style={styles.input}
          onChange={handleChange}
          accept=".csv,.xlsx"
          disabled={uploading}
        />

        {uploading ? (
          <div style={styles.uploadingContent}>
            <div style={styles.spinner}></div>
            <p style={styles.uploadingText}>Uploading and parsing file...</p>
          </div>
        ) : (
          <div style={styles.content}>
            <svg
              style={styles.icon}
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
              />
            </svg>
            <p style={styles.mainText}>
              <span style={styles.link}>Click to upload</span> or drag and drop
            </p>
            <p style={styles.subText}>CSV or XLSX (max 10MB)</p>
          </div>
        )}
      </div>

      <div style={styles.infoBox}>
        <h3 style={styles.infoTitle}>Required Columns (14)</h3>
        <div style={styles.columnList}>
          <div style={styles.column}>
            <span>1. transaction_date</span>
            <span>2. invoice_no</span>
            <span>3. dealer_code</span>
            <span>4. dealer_name</span>
            <span>5. branch</span>
          </div>
          <div style={styles.column}>
            <span>6. product_code</span>
            <span>7. product_name</span>
            <span>8. quantity</span>
            <span>9. unit_price</span>
            <span>10. revenue</span>
          </div>
          <div style={styles.column}>
            <span>11. cost</span>
            <span>12. target</span>
            <span>13. sales_person</span>
            <span>14. sales_month</span>
          </div>
        </div>
      </div>
    </div>
  )
}

const styles = {
  container: {
    display: 'flex',
    flexDirection: 'column',
    gap: '24px',
  },
  dropzone: {
    backgroundColor: 'white',
    border: '2px dashed #cbd5e0',
    borderRadius: '8px',
    padding: '60px 40px',
    textAlign: 'center',
    cursor: 'pointer',
    transition: 'all 0.2s ease',
  },
  dropzoneActive: {
    borderColor: '#007bff',
    backgroundColor: '#f0f8ff',
  },
  dropzoneUploading: {
    cursor: 'not-allowed',
    opacity: 0.7,
  },
  input: {
    display: 'none',
  },
  content: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    gap: '12px',
  },
  icon: {
    width: '64px',
    height: '64px',
    color: '#a0aec0',
  },
  mainText: {
    fontSize: '16px',
    color: '#4a5568',
  },
  link: {
    color: '#007bff',
    fontWeight: '600',
  },
  subText: {
    fontSize: '14px',
    color: '#a0aec0',
  },
  uploadingContent: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    gap: '16px',
  },
  spinner: {
    border: '4px solid #f3f3f3',
    borderTop: '4px solid #007bff',
    borderRadius: '50%',
    width: '50px',
    height: '50px',
    animation: 'spin 1s linear infinite',
  },
  uploadingText: {
    fontSize: '16px',
    color: '#4a5568',
    fontWeight: '500',
  },
  infoBox: {
    backgroundColor: 'white',
    padding: '24px',
    borderRadius: '8px',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
  },
  infoTitle: {
    fontSize: '16px',
    fontWeight: 'bold',
    marginBottom: '16px',
    color: '#333',
  },
  columnList: {
    display: 'grid',
    gridTemplateColumns: 'repeat(3, 1fr)',
    gap: '8px',
    fontSize: '14px',
    color: '#666',
  },
  column: {
    display: 'flex',
    flexDirection: 'column',
    gap: '6px',
  },
}

const spinKeyframes = `
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
`

const styleSheet = document.createElement('style')
styleSheet.textContent = spinKeyframes
document.head.appendChild(styleSheet)

export default FileUploader
