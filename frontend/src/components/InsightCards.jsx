function InsightCards({ insights }) {
  if (!insights || insights.length === 0) {
    return (
      <div style={styles.empty}>
        <p>No insights available. Upload data to generate insights.</p>
      </div>
    )
  }

  const bgColors = {
    success: '#d4edda',
    danger: '#f8d7da',
    warning: '#fff3cd',
    info: '#d1ecf1',
  }

  const borderColors = {
    success: '#28a745',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#17a2b8',
  }

  const icons = {
    success: '✅',
    danger: '❌',
    warning: '⚠️',
    info: 'ℹ️',
  }

  return (
    <div style={styles.grid}>
      {insights.map((insight, i) => (
        <div key={i} style={{...styles.card, backgroundColor: bgColors[insight.type] || '#f8f9fa', borderLeft: `4px solid ${borderColors[insight.type] || '#6c757d'}`}}>
          <div style={styles.cardHeader}>
            <span style={styles.icon}>{icons[insight.type] || '📊'}</span>
            <h4 style={styles.cardTitle}>{insight.title}</h4>
          </div>
          <p style={styles.cardMessage}>{insight.message}</p>
        </div>
      ))}
    </div>
  )
}

const styles = {
  empty: { padding: '40px', textAlign: 'center', color: '#666', backgroundColor: 'white', borderRadius: '8px' },
  grid: { display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '12px' },
  card: { padding: '16px', borderRadius: '8px', boxShadow: '0 1px 3px rgba(0,0,0,0.1)' },
  cardHeader: { display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' },
  icon: { fontSize: '18px' },
  cardTitle: { fontSize: '14px', fontWeight: 'bold', color: '#333', margin: 0 },
  cardMessage: { fontSize: '13px', color: '#555', margin: 0, lineHeight: '1.5' },
}

export default InsightCards
