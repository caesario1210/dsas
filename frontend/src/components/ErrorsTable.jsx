function ErrorsTable({ title, errors, type }) {
  if (!errors || errors.length === 0) return null

  return (
    <div style={styles.container}>
      <div style={styles.header}>
        <h3 style={styles.title}>{title}</h3>
        <span style={styles.badge}>{errors.length} {errors.length === 1 ? 'error' : 'errors'}</span>
      </div>

      <div style={styles.tableWrapper}>
        <table style={styles.table}>
          <thead>
            <tr style={styles.headerRow}>
              <th style={styles.headerCell}>Line</th>
              <th style={styles.headerCell}>Invoice No</th>
              {type === 'validation' && (
                <>
                  <th style={styles.headerCell}>Rule</th>
                  <th style={styles.headerCell}>Field</th>
                </>
              )}
              {type === 'duplicate' && (
                <th style={styles.headerCell}>First Seen At</th>
              )}
              {type === 'missing' && (
                <th style={styles.headerCell}>Missing Fields</th>
              )}
              <th style={styles.headerCell}>Message</th>
            </tr>
          </thead>
          <tbody>
            {errors.slice(0, 100).map((error, index) => (
              <tr key={index} style={index % 2 === 0 ? styles.evenRow : styles.oddRow}>
                <td style={styles.indexCell}>{error.line}</td>
                <td style={styles.cell}>{error.invoice_no || 'N/A'}</td>
                {type === 'validation' && (
                  <>
                    <td style={styles.cell}>{error.rule}</td>
                    <td style={styles.cell}>{error.field}</td>
                  </>
                )}
                {type === 'duplicate' && (
                  <td style={styles.cell}>{error.first_seen_at_line}</td>
                )}
                {type === 'missing' && (
                  <td style={styles.cell}>
                    {error.missing_fields ? error.missing_fields.join(', ') : 'N/A'}
                  </td>
                )}
                <td style={styles.messageCell}>{error.message}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {errors.length > 100 && (
        <div style={styles.footer}>
          <p style={styles.footerText}>
            Showing first 100 of {errors.length} errors
          </p>
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
    marginBottom: '24px',
    overflow: 'hidden',
  },
  header: {
    padding: '16px 24px',
    backgroundColor: '#f8d7da',
    borderBottom: '2px solid #f5c6cb',
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  title: {
    fontSize: '16px',
    fontWeight: 'bold',
    color: '#721c24',
  },
  badge: {
    padding: '4px 12px',
    backgroundColor: '#dc3545',
    color: 'white',
    borderRadius: '12px',
    fontSize: '12px',
    fontWeight: 'bold',
  },
  tableWrapper: {
    overflowX: 'auto',
    maxHeight: '500px',
    overflowY: 'auto',
  },
  table: {
    width: '100%',
    borderCollapse: 'collapse',
    fontSize: '14px',
  },
  headerRow: {
    backgroundColor: '#f7fafc',
    position: 'sticky',
    top: 0,
    zIndex: 1,
  },
  headerCell: {
    padding: '12px 16px',
    textAlign: 'left',
    fontWeight: '600',
    color: '#2d3748',
    borderBottom: '2px solid #e2e8f0',
    whiteSpace: 'nowrap',
  },
  evenRow: {
    backgroundColor: 'white',
  },
  oddRow: {
    backgroundColor: '#f7fafc',
  },
  cell: {
    padding: '12px 16px',
    color: '#4a5568',
    borderBottom: '1px solid #e2e8f0',
    whiteSpace: 'nowrap',
  },
  indexCell: {
    padding: '12px 16px',
    color: '#a0aec0',
    fontWeight: '600',
    borderBottom: '1px solid #e2e8f0',
    textAlign: 'center',
    width: '80px',
  },
  messageCell: {
    padding: '12px 16px',
    color: '#4a5568',
    borderBottom: '1px solid #e2e8f0',
    maxWidth: '400px',
  },
  footer: {
    padding: '12px 24px',
    backgroundColor: '#f7fafc',
    borderTop: '1px solid #e2e8f0',
    textAlign: 'center',
  },
  footerText: {
    fontSize: '14px',
    color: '#718096',
    fontStyle: 'italic',
  },
}

export default ErrorsTable
