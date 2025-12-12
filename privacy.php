<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Privacy & License – ShareSplit</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    :root {
    --navy: #0b2545;
    --navy-2: #153a6b;
    --gold: #b8860b;
    --card-bg: rgba(255, 255, 255, 0.92);
}

html,
body {
    height: 100%;
    margin: 0;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(120deg, rgba(9, 25, 45, 0.72), rgba(11, 37, 69, 0.76)),
        url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80')
            no-repeat center/cover;
    color: #222;
}

/* HEADER */
.site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 72px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 22px;

    background: linear-gradient(180deg, rgba(11, 37, 69, 0.18), rgba(11, 37, 69, 0.04));
    backdrop-filter: blur(6px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);

    z-index: 50;
}

.site-header .brand {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff;
    text-decoration: none;
}

.site-header .brand img {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
}

.site-header .nav-links a {
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    padding: 8px 12px;
    font-weight: 600;
}

.header-spacer {
    height: 88px;
}

/* LAYOUT */
.container {
    max-width: 980px;
    margin: 0 auto;
    padding: 2.2rem 1.2rem;
}

.card {
    background: var(--card-bg);
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 8px 32px rgba(2, 7, 15, 0.16);
    border: 1px solid rgba(0, 0, 0, 0.04);
}

/* HERO */
.hero {
    display: flex;
    gap: 16px;
    align-items: center;
    padding: 18px;
    border-radius: 12px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.02));
}

.hero .logo {
    width: 88px;
    height: 88px;
    border-radius: 12px;

    display: grid;
    place-items: center;

    background: linear-gradient(90deg, var(--navy), var(--navy-2));
    box-shadow: 0 12px 34px rgba(2, 7, 15, 0.45);
}

.hero h1 {
    margin: 0;
    color: #153a6bff;
    font-size: 1.3rem;
}

.hero p {
    margin: 6px 0 0;
    color: #153a6bff;
}

.section-title {
    color: var(--navy);
    font-weight: 700;
    margin-top: 18px;
    margin-bottom: 8px;
}

pre.license {
    white-space: pre-wrap;
    background: #f7f7f7;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #eee;
    max-height: 260px;
    overflow: auto;
}

/* RESPONSIVE */
@media (max-width: 700px) {
    .hero {
        flex-direction: column;
        text-align: center;
    }
}

  </style>
</head>
<body>
  <header class="site-header">
    <a class="brand" href="index.php">
      <img src="ShareSplit%20Logo.png" alt="ShareSplit logo" />
      <div style="font-weight:700;color:#fff">ShareSplit</div>
    </a>
    <nav class="nav-links d-none d-md-flex">
      <a href="index.php">Home</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="group.php">Groups</a>
      <a href="login.php">Login</a>
    </nav>
    <div style="display:flex;align-items:center;gap:12px">
      <?php if (isset($_SESSION['user_name'])): ?>
        <span style="color: #153a6bff">Logged in as <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline-light btn-sm">Login</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="header-spacer"></div>

  <main class="container">
    <div class="card">
      <div class="hero">
        <div class="logo"><img src="ShareSplit%20Logo.png" alt="logo" style="width:100%; height:100%; object-fit:cover" onerror="this.style.display='none'"></div>
        <div>
          <h1>Privacy & License</h1>
          <p>How ShareSplit collects and handles data, and the open-source license that governs ShareSplit's code.</p>
        </div>
      </div>

      <h3 class="section-title">Privacy Policy — Summary</h3>
      <p>ShareSplit collects only basic account information (username, email, display name), group membership and expense records required to deliver the core service. We do not sell personal data. We may store your IP address and use third-party services (e.g., exchange-rate APIs) — see the full policy below.</p>

      <h5 class="section-title">What we collect</h5>
      <ul>
        <li>Account details: username, password hash, email, full name — required to authenticate.</li>
        <li>Groups and expenses: group metadata, member lists, expenses, and balances — used to compute splits and provide summaries.</li>
        <li>Aggregate metrics: anonymous usage data to improve features.</li>
      </ul>

      <h5 class="section-title">Third-party services</h5>
      <p>We use third-party APIs to provide features, such as exchange-rates. These external services may collect data according to their own privacy policies. We recommend reviewing those providers’ privacy statements.</p>

      <h5 class="section-title">Security & Retention</h5>
      <p>Passwords are stored hashed using PHP's password_hash(). We retain user data until the user deletes their account or the group is removed; backups may persist for a limited time.</p>

      <h5 class="section-title">Contact & Requests</h5>
      <p>To request deletion of data, corrections, or to ask privacy questions, contact: <a href="mailto:privacy@sharesplit.example">privacy@sharesplit.example</a>.</p>

      <h3 class="section-title">License</h3>
      <p>ShareSplit's code is provided under the MIT license. You can download the license below or view it inline.</p>

      <div class="d-flex gap-2 mt-3 mb-2 flex-wrap">
        <a href="LICENSE.txt" class="btn btn-primary"><i class="bi bi-download"></i> Download License</a>
        <button id="viewLicenseBtn" class="btn btn-outline-secondary">View License</button>
      </div>

      <div id="licenseContainer" style="display:none;margin-top:12px">
        <pre class="license" id="licenseText">Loading…</pre>
      </div>

      <div class="mt-4 text-muted">
        <strong>Note:</strong> This policy is a simple, high-level summary intended to prevent a broken link from appearing. It is not legal advice. For a production system, consult a privacy professional.
      </div>
    </div>
  </main>

<script>
document.getElementById('viewLicenseBtn').addEventListener('click', function(){
  const c = document.getElementById('licenseContainer');
  const p = document.getElementById('licenseText');
  if (c.style.display === 'none') {
    fetch('LICENSE.txt')
      .then(r => r.text())
      .then(txt => { p.textContent = txt; c.style.display = 'block'; })
      .catch(()=> { p.textContent = 'Failed to load license.'; c.style.display = 'block'; });
  } else {
    c.style.display = 'none';
  }
});
</script>
</body>
</html>