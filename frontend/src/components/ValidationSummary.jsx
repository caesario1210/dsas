function ValidationSummary({ result }) {
  const { status, summary } = result
  const isPassed = status === 'passed'

  return (
    <div style={styles.container}>
      <div style={styles.header}>
        <h2 style={styles.title}>Validation Result</h2>
        <div style={isPassed ? styles.badgePassed : styles.badgeFailed}>
          {isPassed ? '✓ PASSED' : '✗ FAILED'}
        </div>
      </div>

      <div style={styles.statsGrid}>
        <div style={styles.statCard}>
          <div style={styles.statLabel}>Total Rows</div>
          <div style={styles.statValue}>{summary.total_rows.toLocaleString()}</div>
        </div>

        <div style={{...styles.statCard, ...styles.statSuccess}}>
          <div style={styles.statLabel}>Valid Rows</div>
          <div style={styles.statValue}>{summary.valid_rows.toLocaleString()}</div>
        </div>

        <div style={{...styles.statCard, ...styles.statDanger}}>
          <div style={styles.statLabel}>Invalid Rows</div>
          <div style={styles.statValue}>{summary.invalid_rows.toLocaleString()}</div>
        </div>

        <div style={{...styles.statCard, ...styles.statWarning}}>
          <div style={styles.statLabel}>Duplicates</div>
          <div style={styles.statValue}>{summary.duplicates_within_csv.toLocaleString()}</div>
        </div>

        <div style={{...styles.statCard, ...styles.statWarning}}>
          <div style={styles.statLabel}>Missing Values</div>
          <div style={styles.statValue}>{summary.missing_values.toLocaleString()}</div>
        </div>
      </div>

      {isPassed ? (
        <div style={styles.successMessage}>
          <strong>✓ All validation checks passed!</strong>
          <p>Data is ready for cleaning and import.</p>
        </div>
      ) : (
        <div style={styles.failureMessage}>
          <strong>✗ Validation failed</strong>
          <p>Please review errors below and fix the data before re-uploading.</p>
        </div>
      )}
    </div>
  )
}

const styles = {
  container: {
    backgroundColor: 'white',
    borderRadius: '8px',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
    padding: '24px',
    marginBottom: '24px',
  },
  header: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: '24px',
    paddingBottom: '16px',
    borderBottom: '2px solid #e2e8f0',
  },
  title: {
    fontSize: '20px',
    fontWeight: 'bold',
    color: '#333',
  },
  badgePassed: {
    padding: '8px 20px',
    backgroundColor: '#d4edda',
    color: '#155724',
    borderRadius: '20px',
    fontWeight: 'bold',
    fontSize: '14px',
    border: '2px solid #28a745',
  },
  badgeFailed: {
    padding: '8px 20px',
    backgroundColor: '#f8d7da',
    color: '#721c24',
    borderRadius: '20px',
    fontWeight: 'bold',
    fontSize: '14px',
    border: '2px solid #dc3545',
  },
  statsGrid: {
    display: 'grid',
    gridTemplateColumns: 'repeat(5, 1fr)',
    gap: '16px',
    marginBottom: '24px',
  },
  statCard: {
    padding: '20px',
    backgroundColor: '#f7fafc',
    borderRadius: '8px',
    textAlign: 'center',
    border: '2px solid #e2e8f0',
  },
  statSuccess: {
    backgroundColor: '#d4edda',
    borderColor: '#28a745',
  },
  statDanger: {
    backgroundColor: '#f8d7da',
    borderColor: '#dc3545',
  },
  statWarning: {
    backgroundColor: '#fff3cd',
    borderColor: '#ffc107',
  },
  statLabel: {
    fontSize: '12px',
    color: '#666',
    fontWeight: '600',
    marginBottom: '8px',
    textTransform: 'uppercase',
  },
  statValue: {
    fontSize: '28px',
    fontWeight: 'bold',
    color: '#333',
  },
  successMessage: {
    padding: '16px',
    backgroundColor: '#d4edda',
    color: '#155724',
    borderRadius: '4px',
    border: '1px solid #c3e6cb',
    textAlign: 'center',
  },
  failureMessage: {
    padding: '16px',
    backgroundColor: '#f8d7da',
    color: '#721c24',
    borderRadius: '4px',
    border: '1px solid #f5c6cb',
    textAlign: 'center',
  },
}

export default ValidationSummary
