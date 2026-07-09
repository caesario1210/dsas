function PreviewTable({ columns, data, totalRows }) {
  return (
    <div style={styles.container}>
      <div style={styles.header}>
        <h2 style={styles.title}>Data Preview</h2>
        <p style={styles.subtitle}>
          Showing first {data.length} of {totalRows} rows
        </p>
      </div>

      <div style={styles.tableWrapper}>
        <table style={styles.table}>
          <thead>
            <tr style={styles.headerRow}>
              <th style={styles.headerCell}>#</th>
              {columns.map((column, index) => (
                <th key={index} style={styles.headerCell}>
                  {column}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {data.map((row, rowIndex) => (
              <tr key={rowIndex} style={rowIndex % 2 === 0 ? styles.evenRow : styles.oddRow}>
                <td style={styles.indexCell}>{rowIndex + 1}</td>
                {columns.map((column, colIndex) => (
                  <td key={colIndex} style={styles.cell}>
                    {row[column] || '-'}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {totalRows > data.length && (
        <div style={styles.footer}>
          <p style={styles.footerText}>
            + {totalRows - data.length} more rows (will be processed during validation)
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
    overflow: 'hidden',
  },
  header: {
    padding: '20px 24px',
    borderBottom: '1px solid #e2e8f0',
  },
  title: {
    fontSize: '18px',
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '4px',
  },
  subtitle: {
    fontSize: '14px',
    color: '#666',
  },
  tableWrapper: {
    overflowX: 'auto',
    maxHeight: '600px',
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
    width: '60px',
  },
  footer: {
    padding: '16px 24px',
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

export default PreviewTable
