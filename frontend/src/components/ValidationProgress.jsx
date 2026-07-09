function ValidationProgress() {
  return (
    <div style={styles.container}>
      <div style={styles.content}>
        <div style={styles.spinner}></div>
        <h2 style={styles.title}>Validating Data...</h2>
        <p style={styles.subtitle}>This may take a few moments</p>
        
        <div style={styles.checklistContainer}>
          <div style={styles.checklistItem}>
            <div style={styles.spinner2}></div>
            <span>Checking revenue consistency (Q1-B)</span>
          </div>
          <div style={styles.checklistItem}>
            <div style={styles.spinner2}></div>
            <span>Validating invoice uniqueness (Q2-A)</span>
          </div>
          <div style={styles.checklistItem}>
            <div style={styles.spinner2}></div>
            <span>Verifying master data consistency (Q3-C)</span>
          </div>
          <div style={styles.checklistItem}>
            <div style={styles.spinner2}></div>
            <span>Ensuring single period per upload (Q5-B)</span>
          </div>
          <div style={styles.checklistItem}>
            <div style={styles.spinner2}></div>
            <span>Matching transaction date with period</span>
          </div>
          <div style={styles.checklistItem}>
            <div style={styles.spinner2}></div>
            <span>Detecting duplicates within CSV</span>
          </div>
          <div style={styles.checklistItem}>
            <div style={styles.spinner2}></div>
            <span>Checking for missing values</span>
          </div>
        </div>
      </div>
    </div>
  )
}

const styles = {
  container: {
    backgroundColor: 'white',
    borderRadius: '8px',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
    padding: '60px 40px',
  },
  content: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    gap: '24px',
  },
  spinner: {
    border: '6px solid #f3f3f3',
    borderTop: '6px solid #007bff',
    borderRadius: '50%',
    width: '80px',
    height: '80px',
    animation: 'spin 1s linear infinite',
  },
  title: {
    fontSize: '24px',
    fontWeight: 'bold',
    color: '#333',
    marginTop: '16px',
  },
  subtitle: {
    fontSize: '16px',
    color: '#666',
  },
  checklistContainer: {
    display: 'flex',
    flexDirection: 'column',
    gap: '12px',
    marginTop: '24px',
    width: '100%',
    maxWidth: '500px',
  },
  checklistItem: {
    display: 'flex',
    alignItems: 'center',
    gap: '12px',
    fontSize: '14px',
    color: '#666',
    padding: '8px',
  },
  spinner2: {
    border: '3px solid #f3f3f3',
    borderTop: '3px solid #007bff',
    borderRadius: '50%',
    width: '20px',
    height: '20px',
    animation: 'spin 1s linear infinite',
    flexShrink: 0,
  },
}

const spinKeyframes = `
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
`

if (!document.getElementById('spin-animation')) {
  const styleSheet = document.createElement('style')
  styleSheet.id = 'spin-animation'
  styleSheet.textContent = spinKeyframes
  document.head.appendChild(styleSheet)
}

export default ValidationProgress
