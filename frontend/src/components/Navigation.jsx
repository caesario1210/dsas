function Navigation({ user }) {
  const isAdmin = user?.role?.name === 'Admin'

  return (
    <nav style={styles.nav}>
      <div style={styles.navContainer}>
        <div style={styles.brand}>
          <h2 style={styles.brandText}>DSAS</h2>
        </div>
        
        <div style={styles.links}>
          <a href="/dashboard" style={styles.link}>
            📊 Dashboard
          </a>
          
          {isAdmin && (
            <>
              <a href="/upload" style={styles.link}>
                📤 Upload Data
              </a>
              <a href="/admin" style={styles.link}>
                ⚙️ Admin
              </a>
            </>
          )}
        </div>

        <div style={styles.userSection}>
          <span style={styles.userName}>{user?.name}</span>
          <span style={styles.userRole}>({user?.role?.name})</span>
          <button 
            style={styles.logoutBtn} 
            onClick={() => {
              localStorage.removeItem('token')
              window.location.href = '/login'
            }}
          >
            Logout
          </button>
        </div>
      </div>
    </nav>
  )
}

const styles = {
  nav: {
    backgroundColor: '#2c3e50',
    boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
  },
  navContainer: {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: '16px 40px',
    maxWidth: '1400px',
    margin: '0 auto',
  },
  brand: {
    marginRight: '40px',
  },
  brandText: {
    color: 'white',
    fontSize: '24px',
    fontWeight: 'bold',
    margin: 0,
  },
  links: {
    display: 'flex',
    gap: '24px',
    flex: 1,
  },
  link: {
    color: 'white',
    textDecoration: 'none',
    fontSize: '16px',
    padding: '8px 16px',
    borderRadius: '4px',
    transition: 'background-color 0.2s',
  },
  userSection: {
    display: 'flex',
    alignItems: 'center',
    gap: '12px',
  },
  userName: {
    color: 'white',
    fontSize: '14px',
  },
  userRole: {
    color: '#95a5a6',
    fontSize: '12px',
  },
  logoutBtn: {
    padding: '8px 16px',
    backgroundColor: '#e74c3c',
    color: 'white',
    border: 'none',
    borderRadius: '4px',
    cursor: 'pointer',
    fontSize: '14px',
    fontWeight: '500',
  },
}

export default Navigation
