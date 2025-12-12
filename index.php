<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>TripPlanner - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --navy: #0b2545;
      --gold: #b8860b;
      --muted: #95a5a6;
      --card-bg: rgba(255,255,255,0.85);
      --glass: rgba(9,25,45,0.56);
    }
    html,body { height:100%; margin:0; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }
    body {
      background: linear-gradient(120deg, rgba(7,19,38,0.75), rgba(9,25,45,0.85)),
                  url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }
    .site-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 72px;
      z-index: 10;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 0 20px;
      background: linear-gradient(180deg, rgba(11,37,69,0.18), rgba(11,37,69,0.06));
      backdrop-filter: blur(6px);
      border-bottom:1px solid rgba(255,255,255,0.04);
    }
    .brand { display:flex; gap:12px; align-items:center; color:#fff; text-decoration:none; }
    .brand img { width:48px; height:48px; border-radius:8px; object-fit:cover; box-shadow: 0 6px 18px rgba(0,0,0,0.4); }
    .brand .title { font-weight:700; font-size:1.1rem; color:#fff; letter-spacing:0.02em; }
    .nav-links a { color:rgba(255,255,255,0.9); text-decoration:none; padding:10px 12px; font-weight:600; }
    .nav-links a:hover { color:var(--gold); text-decoration:underline; }
    .hero { height: 88vh; display:flex; align-items:center; justify-content:center; position:relative; z-index:1; padding-top:88px; }
    .hero-card { width:100%; max-width:980px; margin:0 20px; border-radius:16px; background: linear-gradient(90deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03)); padding: 36px; box-shadow: 0 18px 50px rgba(2,7,15,0.5); border:1px solid rgba(255,255,255,0.04); backdrop-filter: blur(6px); }
    .hero-title { font-weight:800; font-size:2.2rem; color:#fff; margin-bottom:10px; }
    .hero-sub { color:rgba(255,255,255,0.85); font-size:1.05rem; margin-bottom:18px; max-width:880px; }
    .cta-row { display:flex; gap:12px; flex-wrap:wrap; }
    .btn-primary { background: linear-gradient(90deg, var(--navy), #153a6b); border: none; color: #fff; font-weight:700; border-radius:10px; padding:12px 18px; box-shadow: 0 10px 30px rgba(11,37,69,0.28); }
    .btn-outline-gold { background: transparent; border: 2px solid rgba(184,134,11,0.18); color: var(--gold); font-weight:700; border-radius:10px; padding:10px 16px; }
    .section { padding: 72px 18px; background:transparent; color:#fff; }
    .content-wrap { max-width: 1100px; margin: 0 auto; }
    .card-ghost { background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; border: 1px solid rgba(255,255,255,0.04); }
    .feature { display:flex; gap:16px; align-items:flex-start; margin-bottom:18px; }
    .feature i { font-size:22px; color:var(--gold); width:40px; height:40px; display:grid; place-items:center; background:rgba(184,134,11,0.1); border-radius:8px; }
    .how-step { background: rgba(255,255,255,0.02); padding:18px; border-radius:12px; border:1px solid rgba(255,255,255,0.03); text-align:center; }
    footer { padding:20px 18px; color:rgba(255,255,255,0.77); text-align:center; font-size:0.95rem; border-top:1px solid rgba(255,255,255,0.03); }
    @media (max-width: 768px) {
      .hero-title { font-size:1.6rem; }
      .hero-card { padding:20px; }
      .brand img { width:42px; height:42px; }
    }
  </style>
</head>
<body>
  <header class="site-header">
    <a class="brand" href="<?php echo isset($_SESSION['user_name']) ? 'dashboard.php' : 'index.php'; ?>">
      <img src="ShareSplit%20Logo.png" alt="ShareSplit logo">
      <div class="title">TripPlanner</div>
    </a>

    <div class="d-flex align-items-center gap-2">
      <nav class="nav-links d-none d-md-flex me-3">
        <a href="#about">About</a>
        <a href="#features">Features</a>
        <a href="#how">How it works</a>
      </nav>

      <?php if (isset($_SESSION['user_name'])): ?>
        <span style="color:#fff; font-weight:700; margin-right:12px;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="dashboard.php" class="btn btn-sm btn-outline-light me-2">Dashboard</a>
        <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-outline-light me-2">Login</a>
        <a href="signup.php" class="btn btn-sm btn-primary">Get Started</a>
      <?php endif; ?>
    </div>
  </header>

  <main>    
    <section class="hero">
      <div class="hero-card">
        <div class="row align-items-center">
          <div class="col-md-7">
            <h1 class="hero-title">Split costs, close accounts, keep friendships intact.</h1>
            <p class="hero-sub">TripPlanner simplifies group budgeting and expense shares — plan trips, add members, and let the app compute balances and who owes who.</p>
            <div class="cta-row">
              <a href="signup.php" class="btn btn-primary">Create Free Account</a>
              <a href="#features" class="btn btn-outline-gold">Explore Features</a>
            </div>
          </div>
          <div class="col-md-5 d-none d-md-block">
            <div class="card-ghost">
              <h5 style="margin-bottom:10px; color:#fff">Quick Example</h5>
              <p style="margin-bottom:8px; color:rgba(255,255,255,0.8)">Add members, add expenses, and TripPlanner calculates split amounts and balances for you — including multi-currency friendly totals.</p>
              <div class="d-flex gap-2 mt-3">
                <div class="how-step" style="flex:1">
                  <strong>1</strong><br> Create Group
                </div>
                <div class="how-step" style="flex:1">
                  <strong>2</strong><br> Add Expenses
                </div>
                <div class="how-step" style="flex:1">
                  <strong>3</strong><br> Settle Up
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="about" class="section">
      <div class="content-wrap">
        <h3 style="margin-bottom:12px">About TripPlanner</h3>
        <p class="card-ghost">TripPlanner helps friends and travelers share expenses more easily. Create groups, invite members, track payments, and generate clear balances — no awkward math.</p>
      </div>
    </section>

    <section id="features" class="section">
      <div class="content-wrap">
        <h3 style="margin-bottom:18px">Features</h3>
        <div class="row">
          <div class="col-md-6">
            <div class="feature">
              <i class="bi bi-people-fill"></i>
              <div>
                <strong>Group Management</strong>
                <div style="color:rgba(255,255,255,0.8)">Create groups, add members, and invite others to collaborate.</div>
              </div>
            </div>
            <div class="feature">
              <i class="bi bi-currency-exchange"></i>
              <div>
                <strong>Multi-Currency Support</strong>
                <div style="color:rgba(255,255,255,0.8)">Set trip country and currency; sums are presented in your chosen currency.</div>
              </div>
            </div>
            <div class="feature">
              <i class="bi bi-file-earmark-text"></i>
              <div>
                <strong>Expense Split</strong>
                <div style="color:rgba(255,255,255,0.8)">Add expenses, choose who paid, and TripPlanner calculates an accurate share for each member.</div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="feature">
              <i class="bi bi-check2-square"></i>
              <div>
                <strong>Settle Up</strong>
                <div style="color:rgba(255,255,255,0.8)">Clear balances using simple "you owe" and "you are owed" views and record payments.</div>
              </div>
            </div>
            <div class="feature">
              <i class="bi bi-shield-check"></i>
              <div>
                <strong>Secure & Private</strong>
                <div style="color:rgba(255,255,255,0.8)">We only store what's needed; database-backed accounts and password hashing.</div>
              </div>
            </div>
            <div class="feature">
              <i class="bi bi-phone"></i>
              <div>
                <strong>Mobile Friendly</strong>
                <div style="color:rgba(255,255,255,0.8)">The interface is responsive and usable on phones and tablets.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="how" class="section">
      <div class="content-wrap">
        <h3 style="margin-bottom:18px">How It Works</h3>
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="how-step"><i class="bi bi-person-plus-fill" style="font-size:24px; color:var(--navy);"></i><br><br>Create a group & add friends.</div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="how-step"><i class="bi bi-coin" style="font-size:24px; color:var(--navy);"></i><br><br>Add expenses & mark who paid.</div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="how-step"><i class="bi bi-receipt" style="font-size:24px; color:var(--navy);"></i><br><br>Review balance and settle up.</div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div style="display:flex; align-items:center; justify-content:center; gap:10px;">
      <img src="ShareSplit%20Logo.png" alt="ShareSplit logo" style="width:28px; height:28px; border-radius:6px; object-fit:cover;">
      <div>© <?php echo date('Y'); ?> TripPlanner. Built with a focus on safe, clear expense sharing.</div>
    </div>
  </footer>

  <script type="module" src="https://unpkg.com/@splinetool/viewer@1.10.77/build/spline-viewer.js"></script>
</body>
</html>
