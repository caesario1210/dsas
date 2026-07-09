import { useState, useEffect } from 'react'
import Navigation from '../components/Navigation'
import api from '../services/api'

function Admin({ user }) {
  const [tab, setTab] = useState('dealers')
  const [dealers, setDealers] = useState([])
  const [products, setProducts] = useState([])
  const [branches, setBranches] = useState([])
  const [periods, setPeriods] = useState([])
  const [auditLogs, setAuditLogs] = useState([])

  useEffect(() => {
    if (tab === 'dealers') fetchData('manage/dealers', setDealers)
    else if (tab === 'products') fetchData('manage/products', setProducts)
    else if (tab === 'branches') fetchData('manage/branches', setBranches)
    else if (tab === 'periods') fetchData('manage/periods', setPeriods)
    else if (tab === 'audit') fetchAuditLogs()
  }, [tab])

  const fetchData = async (url, setter) => {
    try { const r = await api.get(url); setter(r.data.data) } catch {}
  }

  const fetchAuditLogs = async () => {
    try { const r = await api.get('/audit'); setAuditLogs(r.data.data) } catch {}
  }

  const recalculateKpi = async () => {
    if (!confirm('Recalculate all KPI?')) return
    await api.post('manage/kpi/recalculate')
    alert('KPI cache cleared! Refresh dashboard.')
  }

  const deletePeriod = async (id, label) => {
    if (!confirm(`Delete period ${label} and all transactions?`)) return
    await api.delete(`manage/periods/${id}`)
    fetchData('manage/periods', setPeriods)
  }

  const deleteItem = async (type, id) => {
    if (!confirm('Delete this item?')) return
    await api.delete(`manage/${type}/${id}`)
    fetchData(`manage/${type}`, type === 'dealers' ? setDealers : type === 'products' ? setProducts : setBranches)
  }

  const formatDate = (d) => new Date(d).toLocaleString()

  if (user?.role?.name !== 'Admin') {
    return <div style={styles.container}><Navigation user={user} /><p style={{textAlign:'center',padding:40}}>Access denied</p></div>
  }

  return (
    <div style={styles.container}>
      <Navigation user={user} />
      <div style={styles.header}>
        <h1 style={styles.title}>Admin Panel</h1>
        <button style={styles.recalcBtn} onClick={recalculateKpi}>🔄 Recalculate KPI</button>
      </div>

      <div style={styles.tabs}>
        <button style={tab === 'dealers' ? styles.tabActive : styles.tab} onClick={() => setTab('dealers')}>Dealers</button>
        <button style={tab === 'products' ? styles.tabActive : styles.tab} onClick={() => setTab('products')}>Products</button>
        <button style={tab === 'branches' ? styles.tabActive : styles.tab} onClick={() => setTab('branches')}>Branches</button>
        <button style={tab === 'periods' ? styles.tabActive : styles.tab} onClick={() => setTab('periods')}>Periods</button>
        <button style={tab === 'audit' ? styles.tabActive : styles.tab} onClick={() => setTab('audit')}>Audit Log</button>
      </div>

      <div style={styles.content}>
        {(tab === 'dealers' || tab === 'products' || tab === 'branches') && (
          <table style={styles.table}>
            <thead>
              <tr>
                <th style={styles.th}>ID</th>
                <th style={styles.th}>Code</th>
                <th style={styles.th}>Name</th>
                <th style={styles.th}>Action</th>
              </tr>
            </thead>
            <tbody>
              {(tab === 'dealers' ? dealers : tab === 'products' ? products : branches).map(item => (
                <tr key={item.id}>
                  <td style={styles.td}>{item.id}</td>
                  <td style={styles.td}>{item.dealer_code || item.product_code || item.branch_code || '-'}</td>
                  <td style={styles.td}>{item.dealer_name || item.product_name || item.branch_name || item.name}</td>
                  <td style={styles.td}><button style={styles.delBtn} onClick={() => deleteItem(tab, item.id)}>Delete</button></td>
                </tr>
              ))}
            </tbody>
          </table>
        )}

        {tab === 'periods' && (
          <table style={styles.table}>
            <thead>
              <tr><th style={styles.th}>ID</th><th style={styles.th}>Period</th><th style={styles.th}>Month</th><th style={styles.th}>Status</th><th style={styles.th}>Rows</th><th style={styles.th}>Transactions</th><th style={styles.th}>Action</th></tr>
            </thead>
            <tbody>
              {periods.map(p => (
                <tr key={p.id}>
                  <td style={styles.td}>{p.id}</td>
                  <td style={styles.td}>{p.year}-{String(p.month).padStart(2,'0')}</td>
                  <td style={styles.td}>{p.month_name}</td>
                  <td style={styles.td}>{p.status}</td>
                  <td style={styles.td}>{p.imported_rows}/{p.total_rows}</td>
                  <td style={styles.td}>{p.transaction_count}</td>
                  <td style={styles.td}><button style={styles.delBtn} onClick={() => deletePeriod(p.id, `${p.year}-${p.month}`)}>Delete</button></td>
                </tr>
              ))}
            </tbody>
          </table>
        )}

        {tab === 'audit' && (
          <table style={styles.table}>
            <thead>
              <tr><th style={styles.th}>Time</th><th style={styles.th}>User</th><th style={styles.th}>Action</th><th style={styles.th}>Description</th></tr>
            </thead>
            <tbody>
              {auditLogs.map(log => (
                <tr key={log.id}>
                  <td style={styles.td}>{formatDate(log.created_at)}</td>
                  <td style={styles.td}>{log.user?.name || 'System'}</td>
                  <td style={styles.td}><code>{log.action}</code></td>
                  <td style={styles.td}>{log.description}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  )
}

const styles = {
  container: { minHeight: '100vh', backgroundColor: '#f5f5f5' },
  header: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '12px 20px', borderBottom: '1px solid #e0e0e0' },
  title: { fontSize: 18, fontWeight: 'bold', color: '#333', margin: 0 },
  tabs: { display: 'flex', gap: 6, padding: '10px 20px', backgroundColor: 'white', borderBottom: '1px solid #e0e0e0', flexWrap: 'wrap' },
  tab: { padding: '6px 14px', border: '1px solid #ddd', borderRadius: 4, cursor: 'pointer', backgroundColor: 'white', fontSize: 13 },
  tabActive: { padding: '6px 14px', border: '1px solid #007bff', borderRadius: 4, cursor: 'pointer', backgroundColor: '#007bff', color: 'white', fontSize: 13 },
  recalcBtn: { padding: '6px 14px', border: '1px solid #28a745', borderRadius: 4, cursor: 'pointer', backgroundColor: '#28a745', color: 'white', fontSize: 13 },
  content: { padding: '16px 20px', maxWidth: 1400, margin: '0 auto' },
  table: { width: '100%', borderCollapse: 'collapse', backgroundColor: 'white', borderRadius: '8px', overflow: 'hidden', boxShadow: '0 2px 4px rgba(0,0,0,0.1)' },
  th: { padding: '12px 16px', textAlign: 'left', fontWeight: '600', color: '#2d3748', borderBottom: '2px solid #e2e8f0', fontSize: '12px', textTransform: 'uppercase', backgroundColor: '#f7fafc' },
  td: { padding: '10px 16px', color: '#4a5568', borderBottom: '1px solid #e2e8f0', fontSize: '14px' },
  delBtn: { padding: '4px 12px', backgroundColor: '#dc3545', color: 'white', border: 'none', borderRadius: '4px', cursor: 'pointer', fontSize: '12px' },
}

export default Admin
