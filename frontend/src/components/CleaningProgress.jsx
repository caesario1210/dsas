function CleaningProgress() {
  return (
    <div style={styles.container}>
      <div style={styles.spinner}></div>
      <h2 style={styles.title}>🧹 Cleaning & Transforming Data...</h2>
      <p style={styles.subtitle}>Please wait while we process your data</p>
      
      <div style={styles.checklist}>
        <div style={styles.checkItem}>
          <span style={styles.checkIcon}>✓</span>
          <span>Trimming whitespace</span>
        </div>
        <div style={styles.checkItem}>
          <span style={styles.checkIcon}>✓</span>
          <span>Normalizing text format</span>
        </div>
        <div style={styles.checkItem}>
          <span style={styles.checkIcon}>✓</span>
          <span>Standardizing dates (YYYY-MM-DD)</span>
        </div>
        <div style={styles.checkItem}>
          <span style={styles.checkIcon}>✓</span>
          <span>Formatting numeric values</span>
        </div>
        <div style={styles.checkItem}>
          <span style={styles.checkIcon}>✓</span>
          <span>Normalizing dealer names</span>
        </div>
      </div>
    </div>
  )
}

const styles = {
  container: {
    backgroundColor: 'white',
    padding: '60px 40px',
    borderRadius: '8px',
    textAlign: 'center',
    boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
  },
  spinner: {
    width: '50px',
    height: '50px',
    border: '4px solid #f3f3f3',
    borderTop: '4px solid #28a745',
    borderRadius: '50%',
    animation: 'spin 1s linear infinite',
    margin: '0 auto 24px',
  },
  title: {
    fontSize: '24px',
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '8px',
  },
  subtitle: {
    fontSize: '14px',
    color: '#666',
    marginBottom: '32px',
  },
  checklist: {
    display: 'flex',
    flexDirection: 'column',
    gap: '12px',
    maxWidth: '400px',
    margin: '0 auto',
    textAlign: 'left',
  },
  checkItem: {
    display: 'flex',
    alignItems: 'center',
    gap: '12px',
    fontSize: '14px',
    color: '#555',
  },
  checkIcon: {
    width: '24px',
    height: '24px',
    backgroundColor: '#28a745',
    color: 'white',
    borderRadius: '50%',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontSize: '14px',
    fontWeight: 'bold',
  },
}

if (!document.getElementById('spin-animation')) {
  const styleSheet = document.createElement('style')
  styleSheet.id = 'spin-animation'
  styleSheet.textContent = `
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `
  document.head.appendChild(styleSheet)
}

export default CleaningProgress
