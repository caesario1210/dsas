function CleaningSummary({ result }) {
  const { status, summary, errors } = result

  const isSuccess = status === 'success'
  const hasErrors = errors && errors.length > 0

  return (
    <div style={styles.container}>
      <div style={styles.header}>
        <h2 style={styles.title}>
          {isSuccess ? '✅ Data Cleaning Completed' : '⚠️ Data Cleaning Completed with Errors'}
        </h2>
        <div style={isSuccess ? styles.successBadge : styles.warningBadge}>
          {isSuccess ? 'SUCCESS' : 'PARTIAL'}
        </div>
      </div>

      <div style={styles.summaryCards}>
        <div style={styles.card}>
          <div style={styles.cardValue}>{summary.total_rows}</div>
          <div style={styles.cardLabel}>Total Rows</div>
        </div>
        
        <div style={{...styles.card, borderColor: '#28a745'}}>
          <div style={{...styles.cardValue, color: '#28a745'}}>
            {summary.cleaned_rows}
          </div>
          <div style={styles.cardLabel}>Cleaned</div>
        </div>
        
        {summary.failed_rows > 0 && (
          <div style={{...styles.card, borderColor: '#dc3545'}}>
            <div style={{...styles.cardValue, color: '#dc3545'}}>
              {summary.failed_rows}
            </div>
            <div style={styles.cardLabel}>Failed</div>
          </div>
        )}
      </div>

      {hasErrors && (
        <div style={styles.errorsSection}>
          <h3 style={styles.errorTitle}>Cleaning Errors ({errors.length})</h3>
          <div style={styles.errorList}>
            {errors.slice(0, 20).map((err, index) => (
              <div key={index} style={styles.errorItem}>
                <span style={styles.errorLine}>Line {err.line}</span>
                <span style={styles.errorMessage}>{err.error}</span>
              </div>
            ))}
            {errors.length > 20 && (
              <div style={styles.moreErrors}>
                + {errors.length - 20} more errors
              </div>
            )}
          </div>
        </div>
      )}

      <div style={styles.infoBox}>
        <h4 style={styles.infoTitle}>Data Transformations Applied:</h4>
        <ul style={styles.infoList}>
          <li>✓ Text normalized (trimmed, standardized case)</li>
          <li>✓ Dates formatted to YYYY-MM-DD</li>
          <li>✓ Numeric values cleaned (removed commas, spaces)</li>
          <li>✓ Dealer names normalized to match master data</li>
        </ul>
      </div>
    </div>
  )
}

const styles = {
  container: {
    backgroundColor: 'white',
    borderRadius: '8px',
    boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
    overflow: 'hidden',
  },
  header: {
    padding: '24px',
    borderBottom: '1px solid #e0e0e0',
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  title: {
    fontSize: '20px',
    fontWeight: 'bold',
    color: '#333',
    margin: 0,
  },
  successBadge: {
    padding: '6px 16px',
    backgroundColor: '#d4edda',
    color: '#155724',
    borderRadius: '4px',
    fontSize: '14px',
    fontWeight: 'bold',
  },
  warningBadge: {
    padding: '6px 16px',
    backgroundColor: '#fff3cd',
    color: '#856404',
    borderRadius: '4px',
    fontSize: '14px',
    fontWeight: 'bold',
  },
  summaryCards: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))',
    gap: '16px',
    padding: '24px',
  },
  card: {
    padding: '20px',
    border: '2px solid #e0e0e0',
    borderRadius: '8px',
    textAlign: 'center',
  },
  cardValue: {
    fontSize: '32px',
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '8px',
  },
  cardLabel: {
    fontSize: '14px',
    color: '#666',
    textTransform: 'uppercase',
    letterSpacing: '0.5px',
  },
  errorsSection: {
    padding: '0 24px 24px',
  },
  errorTitle: {
    fontSize: '16px',
    fontWeight: 'bold',
    color: '#dc3545',
    marginBottom: '12px',
  },
  errorList: {
    backgroundColor: '#fff5f5',
    border: '1px solid #f5c6cb',
    borderRadius: '4px',
    padding: '16px',
    maxHeight: '300px',
    overflowY: 'auto',
  },
  errorItem: {
    display: 'flex',
    gap: '12px',
    padding: '8px 0',
    borderBottom: '1px solid #f5c6cb',
    fontSize: '13px',
  },
  errorLine: {
    fontWeight: 'bold',
    color: '#dc3545',
    minWidth: '80px',
  },
  errorMessage: {
    color: '#666',
    flex: 1,
  },
  moreErrors: {
    textAlign: 'center',
    padding: '12px',
    color: '#666',
    fontSize: '13px',
    fontStyle: 'italic',
  },
  infoBox: {
    padding: '24px',
    backgroundColor: '#f8f9fa',
    borderTop: '1px solid #e0e0e0',
  },
  infoTitle: {
    fontSize: '14px',
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '12px',
  },
  infoList: {
    margin: 0,
    paddingLeft: '20px',
    fontSize: '13px',
    color: '#555',
    lineHeight: '1.8',
  },
}

export default CleaningSummary
