<?php
session_start();
require_once 'db_connect.php';

$error = ""; // Ensure error is always defined

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name']);
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE user_name = ?');
    $stmt->bind_param('s', $user_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_name'] = $user_name;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - TripPlanner</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <style>
       :root {
    --navy: #0b2545;
    --navy-2: #153a6b;
    --gold: #b8860b;
    --muted: #95a5a6;
    --card-bg: rgba(255, 255, 255, 0.92);
    --text-dark: #1f2a33;
}

html,
body {
    height: 100%;
    margin: 0;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
}

body {
    color: var(--text-dark);
    background: linear-gradient(120deg, rgba(9, 25, 45, 0.72), rgba(11, 37, 69, 0.76)),
        url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80')
            no-repeat center center fixed;
    background-size: cover;
}

/* ------------------ GLOBAL HEADER ------------------ */

.site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;

    height: 72px;
    z-index: 40;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 20px;

    background: linear-gradient(180deg, rgba(11, 37, 69, 0.18), rgba(11, 37, 69, 0.06));
    backdrop-filter: blur(6px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.02);
}

.brand {
    display: flex;
    gap: 12px;
    align-items: center;
    color: #fff;
    text-decoration: none;
}

.brand img {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
}

.brand .title {
    font-weight: 700;
    font-size: 1.05rem;
    color: #fff;
}

.nav-links {
    display: flex;
    gap: 10px;
    align-items: center;
}

.nav-links a {
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    font-weight: 600;
    padding: 6px 10px;
}

.nav-links a:hover {
    color: var(--gold);
    text-decoration: underline;
}

.header-spacer {
    height: 88px;
}

/* ------------------ CENTERED CARD ------------------ */

.layout-center {
    min-height: calc(100vh - 88px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.card-shell {
    width: 100%;
    max-width: 520px;

    border-radius: 16px;
    padding: 34px;

    background: var(--card-bg);
    box-shadow: 0 20px 50px rgba(2, 7, 15, 0.44);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

.brand-card {
    display: flex;
    gap: 14px;
    align-items: center;
    margin-bottom: 18px;
}

.brand-card .logo {
    width: 64px;
    height: 64px;

    border-radius: 12px;
    overflow: hidden;

    display: grid;
    place-items: center;

    background: linear-gradient(90deg, var(--navy), var(--navy-2));
    box-shadow: 0 8px 24px rgba(2, 7, 15, 0.45);
}

.brand-card .logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.brand-card h1 {
    margin: 0;
    font-size: 1.25rem;
    font-family: 'Poppins', 'Inter';
    font-weight: 700;
    color: var(--text-dark);
}

.lead-text {
    color: #475A63;
    margin-top: 4px;
    font-size: 0.94rem;
}

/* ------------------ FORM ELEMENTS ------------------ */

.form-label {
    font-weight: 600;
    color: var(--text-dark);
}

.input-group-text {
    background: transparent;
    border: 1px solid #eaf0f2;
    border-right: 0;
    color: var(--muted);

    padding: 12px 10px;
    border-radius: 10px 0 0 10px;
}

.form-control {
    border-radius: 0 10px 10px 0;
    border: 1px solid #eaf0f2;
    padding: 12px 14px;

    background: #fff;
    color: var(--text-dark);
}

.form-control:focus {
    outline: none;
    box-shadow: 0 8px 24px rgba(11, 37, 69, 0.07);
    border-color: var(--navy);
}

#togglePw {
    border-radius: 0 10px 10px 0;
    border: 1px solid #eaf0f2;
    border-left: 0;

    padding: 8px 12px;
    background: transparent;
    color: var(--muted);
}

/* ------------------ BUTTONS ------------------ */

.btn-primary {
    background: linear-gradient(90deg, var(--navy), var(--navy-2));
    border: none;
    border-radius: 12px;

    padding: 12px 14px;
    font-weight: 700;

    box-shadow: 0 12px 34px rgba(11, 37, 69, 0.2);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 44px rgba(11, 37, 69, 0.24);
}

/* ------------------ AUXILIARY LINKS ------------------ */

.aux-row {
    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-top: 12px;
    gap: 8px;
    flex-wrap: wrap;
}

.aux-row a {
    color: var(--navy-2);
    text-decoration: none;
    font-weight: 600;
}

.aux-row a:hover {
    color: var(--navy);
    text-decoration: underline;
}

/* ------------------ ERROR BOX ------------------ */

.error-box {
    background: linear-gradient(90deg, #fff6f6, #fff1f1);
    color: #a83232;

    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid rgba(220, 53, 69, 0.12);

    margin-bottom: 14px;
    font-weight: 600;

    display: flex;
    gap: 10px;
    align-items: center;
}

/* ------------------ LEGAL TEXT ------------------ */

.legal {
    text-align: center;
    font-size: 0.85rem;
    color: #5c6b73;
    margin-top: 12px;
}

/* ------------------ RESPONSIVE ------------------ */

@media (max-width: 520px) {
    .card-shell {
        padding: 22px;
    }

    .brand-card .logo {
        width: 56px;
        height: 56px;
    }
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
        <a href="index.php#about">About</a>
        <a href="index.php#features">Features</a>
        <a href="index.php#how">How it works</a>
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

  <div class="header-spacer"></div>

  <div class="layout-center">
    <div class="card-shell">
      <div class="brand-card">
        <div class="logo">
          <img src="ShareSplit%20Logo.png" alt="ShareSplit logo" onerror="this.style.display='none';">
        </div>
        <div>
          <h1>TripPlanner</h1>
          <div class="lead-text">Sign in to plan & manage your trips and groups</div>
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="error-box"><i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label for="user_name" class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
            <input type="text" class="form-control" id="user_name" name="user_name" required placeholder="Your username" value="<?php echo isset($user_name) ? htmlspecialchars($user_name) : ''; ?>">
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
            <button type="button" class="btn" id="togglePw" title="Show/Hide"><i class="bi bi-eye"></i></button>
          </div>
          <div class="d-flex justify-content-between mt-2">
            <a href="signup.php" class="aux-small">Create account</a>
            <a href="index.php" class="aux-small">Back to home</a>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign in</button>

        <div class="small-muted text-center mt-3">Use your TripPlanner account to sign in.</div>
      </form>

      <div class="legal">© <?php echo date('Y'); ?> TripPlanner • <a href="privacy.php">Privacy</a></div>
    </div>
  </div>

  <script>
    document.getElementById('togglePw').addEventListener('click', function(){
      var pw = document.getElementById('password');
      var icon = this.querySelector('i');
      if (pw.type === 'password') { pw.type = 'text'; icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
      else { pw.type = 'password'; icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
    });
  </script>
</body>
</html>