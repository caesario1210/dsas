import { useState, useEffect } from 'react'
import Navigation from '../components/Navigation'
import InsightCards from '../components/InsightCards'
import api from '../services/api'

function Dashboard({ user }) {
  const [data, setData] = useState(null)
  const [insights, setInsights] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [year, setYear] = useState(new Date().getFullYear())
  const [month, setMonth] = useState('')
  const [branchId, setBranchId] = useState('')
  const [dealerId, setDealerId] = useState('')
  const [filters, setFilters] = useState({ branches: [], dealers: [] })
  const [drillMonth, setDrillMonth] = useState(null)
  const [drillData, setDrillData] = useState(null)

  useEffect(() => { loadFilters() }, [year])
  useEffect(() => { loadDashboard() }, [year, month, branchId, dealerId])

  const loadFilters = async () => {
    try {
      const r = await api.get(`/dashboard/filters?year=${year}`)
      setFilters(r.data.data)
    } catch {}
  }

  const loadDashboard = async () => {
    try {
      setLoading(true)
      const params = { year }
      if (month) params.month = month
      if (branchId) params.branch_id = branchId
      if (dealerId) params.dealer_id = dealerId
      const q = new URLSearchParams(params).toString()

      const [dashRes, insightRes] = await Promise.all([
        api.get(`/dashboard?${q}`),
        api.get(`/insights?year=${year}`),
      ])
      setData(dashRes.data.data)
      setInsights(insightRes.data.data)
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load dashboard')
    } finally { setLoading(false) }
  }

  const handleBarClick = async (m) => {
    setDrillMonth(m)
    try {
      const r = await api.get(`/dashboard/drilldown?year=${year}&month=${m}`)
      setDrillData(r.data.data)
    } catch { setDrillData(null) }
  }

  const fmt = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
  const num = (v) => new Intl.NumberFormat('id-ID').format(v)

  const handleExport = async (type) => {
    const token = localStorage.getItem('token')
    const base = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
    const params = { year }
    if (month) params.month = month
    if (branchId) params.branch_id = branchId
    if (dealerId) params.dealer_id = dealerId
    const q = new URLSearchParams(params).toString()
    const url = `${base}/export/${type}?${q}`

    if (type === 'html') {
      const res = await fetch(url, { headers: { Authorization: `Bearer ${token}` } })
      if (!res.ok) return alert('Export failed')
      const blob = await res.blob()
      window.open(URL.createObjectURL(blob), '_blank')
      return
    }

    const res = await fetch(url, { headers: { Authorization: `Bearer ${token}` } })
    if (!res.ok) return alert('Export failed')
    const blob = await res.blob()
    const blobUrl = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = blobUrl
    a.download = `sales_report_${year}.csv`
    a.click()
    URL.revokeObjectURL(blobUrl)
  }

  if (loading) return (
    <div style={styles.container}>
      <Navigation user={user} />
      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '60vh' }}>
        <div style={styles.spinner}></div>
      </div>
    </div>
  )

  if (error) return (
    <div style={styles.container}>
      <Navigation user={user} />
      <div style={{ padding: 40, textAlign: 'center', color: '#dc3545' }}><h2>Error</h2><p>{error}</p></div>
    </div>
  )

  const kpi = data?.kpi_cards || {}
  const trend = data?.monthly_trend || []
  const dealers = data?.dealer_rankings || { top_10: [], bottom_5: [] }
  const products = data?.product_rankings || { top_10: [], bottom_5: [] }

  return (
    <div style={styles.container}>
      <Navigation user={user} />
      <div style={styles.header}>
        <h1 style={styles.title}>Dashboard</h1>
        <div style={{ display: 'flex', gap: 8, alignItems: 'center', flexWrap: 'wrap' }}>
          <select value={year} onChange={e => { setYear(Number(e.target.value)); setDrillMonth(null); setDrillData(null) }} style={styles.sel}>
            {[2024,2025,2026,2027].map(y => <option key={y} value={y}>{y}</option>)}
          </select>
          <select value={month} onChange={e => { setMonth(e.target.value); setDrillMonth(null); setDrillData(null) }} style={styles.sel}>
            <option value="">All Months</option>
            {[1,2,3,4,5,6,7,8,9,10,11,12].map(m => <option key={m} value={m}>{new Date(2000,m-1,1).toLocaleString('en',{month:'long'})}</option>)}
          </select>
          <select value={branchId} onChange={e => { setBranchId(e.target.value); setDrillMonth(null); setDrillData(null) }} style={styles.sel}>
            <option value="">All Branches</option>
            {filters.branches.map(b => <option key={b.id} value={b.id}>{b.branch_name}</option>)}
          </select>
          <select value={dealerId} onChange={e => { setDealerId(e.target.value); setDrillMonth(null); setDrillData(null) }} style={styles.sel}>
            <option value="">All Dealers</option>
            {filters.dealers.map(d => <option key={d.id} value={d.id}>{d.dealer_name}</option>)}
          </select>
          <button style={styles.exportBtn} onClick={() => handleExport('csv')}>📥 CSV</button>
          <button style={styles.exportBtn} onClick={() => handleExport('html')}>📄 Print Report</button>
        </div>
      </div>

      <div style={styles.content}>
        <div style={styles.kpiGrid}>
          {[
            { label: kpi.total_revenue?.label, value: fmt(kpi.total_revenue?.value || 0), color: '#007bff' },
            { label: kpi.total_profit?.label, value: fmt(kpi.total_profit?.value || 0), color: '#28a745' },
            { label: kpi.profit_margin?.label, value: (kpi.profit_margin?.value || 0) + '%', color: '#ffc107' },
            { label: kpi.target_achievement?.label, value: (kpi.target_achievement?.value || 0) + '%', color: '#17a2b8' },
            { label: kpi.total_quantity?.label, value: num(kpi.total_quantity?.value || 0), color: '#6f42c1' },
            { label: kpi.sales_growth?.label, value: (kpi.sales_growth?.value || 0) + '%', color: (kpi.sales_growth?.value || 0) >= 0 ? '#28a745' : '#dc3545' },
          ].map((c, i) => (
            <div key={i} style={{ ...styles.kpiCard, borderLeft: `4px solid ${c.color}` }}>
              <div style={styles.kpiLabel}>{c.label}</div>
              <div style={{ ...styles.kpiValue, color: c.color }}>{c.value}</div>
            </div>
          ))}
        </div>

        <div style={styles.row}>
          <div style={styles.chartCard}>
            <h3 style={styles.sectionTitle}>Monthly Revenue Trend ({year})</h3>
            <div style={styles.barChart}>
              {trend.map((m) => {
                const maxR = Math.max(...trend.map(x => x.revenue), 1)
                const h = (m.revenue / maxR) * 100
                return (
                  <div key={m.month} style={styles.barCol} onClick={() => m.revenue > 0 && handleBarClick(m.month)} title={`${m.month_name}: ${fmt(m.revenue)}`}>
                    <div style={styles.barValue}>{m.revenue > 0 ? fmt(m.revenue).replace(/^Rp\s/, '') : ''}</div>
                    <div style={{ ...styles.bar, height: `${Math.max(h, 2)}%`, backgroundColor: drillMonth === m.month ? '#ffc107' : '#28a745', cursor: m.revenue > 0 ? 'pointer' : 'default' }}></div>
                    <div style={styles.barLabel}>{m.month_name.slice(0, 3)}</div>
                  </div>
                )
              })}
            </div>
            {drillMonth && drillData && (
              <div style={styles.drilldown}>
                <h4 style={{ margin: '0 0 8px' }}>{trend.find(m => m.month === drillMonth)?.month_name} {year} — Top Contributors</h4>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, fontSize: 12 }}>
                  <div><strong>Top Dealers</strong>{drillData.dealers?.map((d, i) => <div key={i} style={{ padding: '2px 0' }}>{d.dealer_name}: <strong>{fmt(d.revenue)}</strong></div>)}</div>
                  <div><strong>Top Products</strong>{drillData.products?.map((p, i) => <div key={i} style={{ padding: '2px 0' }}>{p.product_name}: <strong>{fmt(p.revenue)}</strong></div>)}</div>
                  <div><strong>Branches</strong>{drillData.branches?.map((b, i) => <div key={i} style={{ padding: '2px 0' }}>{b.branch_name}: <strong>{fmt(b.revenue)}</strong></div>)}</div>
                </div>
              </div>
            )}
          </div>
        </div>

        <div style={styles.row}>
          <div style={styles.tableCard}>
            <h3 style={styles.sectionTitle}>Top 10 Dealers</h3>
            <table style={styles.table}><thead><tr><th style={styles.th}>#</th><th style={styles.th}>Dealer</th><th style={styles.th}>Revenue</th><th style={styles.th}>Profit</th><th style={styles.th}>Margin</th></tr></thead><tbody>
              {dealers.top_10.map((d, i) => (
                <tr key={i} style={i % 2 ? { background: '#f7fafc' } : {}}>
                  <td style={styles.td}>{i + 1}</td><td style={styles.td}>{d.dealer_name}</td>
                  <td style={styles.td}>{fmt(d.revenue)}</td><td style={styles.td}>{fmt(d.profit)}</td><td style={styles.td}>{d.margin}%</td>
                </tr>
              ))}
            </tbody></table>
          </div>
          <div style={styles.tableCard}>
            <h3 style={styles.sectionTitle}>Top 10 Products</h3>
            <table style={styles.table}><thead><tr><th style={styles.th}>#</th><th style={styles.th}>Product</th><th style={styles.th}>Revenue</th><th style={styles.th}>Profit</th><th style={styles.th}>Qty</th></tr></thead><tbody>
              {products.top_10.map((p, i) => (
                <tr key={i} style={i % 2 ? { background: '#f7fafc' } : {}}>
                  <td style={styles.td}>{i + 1}</td><td style={styles.td}>{p.product_name}</td>
                  <td style={styles.td}>{fmt(p.revenue)}</td><td style={styles.td}>{fmt(p.profit)}</td><td style={styles.td}>{num(p.quantity)}</td>
                </tr>
              ))}
            </tbody></table>
          </div>
        </div>

        {insights.length > 0 && (
          <div style={{ marginTop: 16 }}>
            <h3 style={styles.sectionTitle}>Business Insights</h3>
            <InsightCards insights={insights} />
          </div>
        )}
      </div>
    </div>
  )
}

const styles = {
  container: { minHeight: '100vh', backgroundColor: '#f5f5f5' },
  spinner: { width: 40, height: 40, border: '4px solid #f3f3f3', borderTop: '4px solid #007bff', borderRadius: '50%', animation: 'spin 1s linear infinite' },
  header: { padding: '16px 24px', borderBottom: '1px solid #e0e0e0', display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 8 },
  title: { fontSize: 20, fontWeight: 'bold', color: '#333', margin: 0 },
  sel: { padding: '6px 12px', border: '1px solid #ddd', borderRadius: 4, fontSize: 13, backgroundColor: 'white' },
  exportBtn: { padding: '6px 12px', border: '1px solid #ddd', borderRadius: 4, fontSize: 13, cursor: 'pointer', backgroundColor: 'white' },
  content: { padding: '16px 24px', maxWidth: 1400, margin: '0 auto' },
  kpiGrid: { display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: 12, marginBottom: 16 },
  kpiCard: { backgroundColor: 'white', padding: 16, borderRadius: 6, boxShadow: '0 1px 3px rgba(0,0,0,0.08)' },
  kpiLabel: { fontSize: 11, color: '#666', textTransform: 'uppercase', fontWeight: 600, marginBottom: 4 },
  kpiValue: { fontSize: 20, fontWeight: 'bold' },
  row: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 },
  chartCard: { backgroundColor: 'white', padding: 16, borderRadius: 6, gridColumn: '1 / -1', boxShadow: '0 1px 3px rgba(0,0,0,0.08)' },
  sectionTitle: { fontSize: 14, fontWeight: 'bold', color: '#333', marginBottom: 16 },
  barChart: { display: 'flex', alignItems: 'flex-end', gap: 4, height: 200, padding: '0 4px' },
  barCol: { flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', height: '100%', justifyContent: 'flex-end' },
  bar: { width: '100%', maxWidth: 50, borderRadius: '3px 3px 0 0', transition: 'height 0.3s', minHeight: 2 },
  barLabel: { fontSize: 10, color: '#666', marginTop: 4, textAlign: 'center' },
  barValue: { fontSize: 9, color: '#666', marginBottom: 2, textAlign: 'center', whiteSpace: 'nowrap' },
  drilldown: { marginTop: 12, padding: 12, backgroundColor: '#f8f9fa', borderRadius: 6, border: '1px solid #e9ecef' },
  tableCard: { backgroundColor: 'white', padding: 16, borderRadius: 6, boxShadow: '0 1px 3px rgba(0,0,0,0.08)', overflow: 'hidden' },
  table: { width: '100%', borderCollapse: 'collapse', fontSize: 13 },
  th: { padding: '8px 12px', textAlign: 'left', fontWeight: 600, color: '#2d3748', borderBottom: '2px solid #e2e8f0', fontSize: 11, textTransform: 'uppercase' },
  td: { padding: '6px 12px', color: '#4a5568', borderBottom: '1px solid #e2e8f0' },
}

export default Dashboard
