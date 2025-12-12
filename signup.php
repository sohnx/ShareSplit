<?php

session_start();
// Assuming db_connect.php handles your database connection, e.g., $conn
require_once 'db_connect.php';

$country_currency = [
    'Afghanistan' => 'AFN',
    'Albania' => 'ALL',
    'Algeria' => 'DZD',
    'Andorra' => 'EUR',
    'Angola' => 'AOA',
    'Antigua and Barbuda' => 'XCD',
    'Argentina' => 'ARS',
    'Armenia' => 'AMD',
    'Aruba' => 'AWG',
    'Australia' => 'AUD',
    'Austria' => 'EUR',
    'Azerbaijan' => 'AZN',
    'Bahamas' => 'BSD',
    'Bahrain' => 'BHD',
    'Bangladesh' => 'BDT',
    'Barbados' => 'BBD',
    'Belarus' => 'BYN',
    'Belgium' => 'EUR',
    'Belize' => 'BZD',
    'Benin' => 'XOF',
    'Bhutan' => 'BTN',
    'Bolivia' => 'BOB',
    'Bosnia and Herzegovina' => 'BAM',
    'Botswana' => 'BWP',
    'Brazil' => 'BRL',
    'Brunei' => 'BND',
    'Bulgaria' => 'BGN',
    'Burkina Faso' => 'XOF',
    'Burundi' => 'BIF',
    'Cabo Verde' => 'CVE',
    'Cambodia' => 'KHR',
    'Cameroon' => 'XAF',
    'Canada' => 'CAD',
    'Central African Republic' => 'XAF',
    'Chad' => 'XAF',
    'Chile' => 'CLP',
    'China' => 'CNY',
    'Colombia' => 'COP',
    'Comoros' => 'KMF',
    'Costa Rica' => 'CRC',
    'Côte d\'Ivoire' => 'XOF',
    'Croatia' => 'EUR',
    'Cuba' => 'CUP',
    'Cyprus' => 'EUR',
    'Czech Republic' => 'CZK',
    'Democratic Republic of the Congo' => 'CDF',
    'Denmark' => 'DKK',
    'Djibouti' => 'DJF',
    'Dominica' => 'XCD',
    'Dominican Republic' => 'DOP',
    'Ecuador' => 'USD',
    'Egypt' => 'EGP',
    'El Salvador' => 'USD',
    'Equatorial Guinea' => 'XAF',
    'Eritrea' => 'ERN',
    'Estonia' => 'EUR',
    'Eswatini' => 'SZL',
    'Ethiopia' => 'ETB',
    'Fiji' => 'FJD',
    'Finland' => 'EUR',
    'France' => 'EUR',
    'Gabon' => 'XAF',
    'Gambia' => 'GMD',
    'Georgia' => 'GEL',
    'Germany' => 'EUR',
    'Ghana' => 'GHS',
    'Greece' => 'EUR',
    'Grenada' => 'XCD',
    'Guatemala' => 'GTQ',
    'Guinea' => 'GNF',
    'Guinea‑Bissau' => 'XOF',
    'Guyana' => 'GYD',
    'Haiti' => 'HTG',
    'Honduras' => 'HNL',
    'Hungary' => 'HUF',
    'Iceland' => 'ISK',
    'India' => 'INR',
    'Indonesia' => 'IDR',
    'Iran' => 'IRR',
    'Iraq' => 'IQD',
    'Ireland' => 'EUR',
    'Israel' => 'ILS',
    'Italy' => 'EUR',
    'Jamaica' => 'JMD',
    'Japan' => 'JPY',
    'Jordan' => 'JOD',
    'Kazakhstan' => 'KZT',
    'Kenya' => 'KES',
    'Kiribati' => 'AUD',
    'Kuwait' => 'KWD',
    'Kyrgyzstan' => 'KGS',
    'Laos' => 'LAK',
    'Latvia' => 'EUR',
    'Lebanon' => 'LBP',
    'Lesotho' => 'LSL',
    'Liberia' => 'LRD',
    'Libya' => 'LYD',
    'Liechtenstein' => 'CHF',
    'Lithuania' => 'EUR',
    'Luxembourg' => 'EUR',
    'Madagascar' => 'MGA',
    'Malawi' => 'MWK',
    'Malaysia' => 'MYR',
    'Maldives' => 'MVR',
    'Mali' => 'XOF',
    'Malta' => 'EUR',
    'Marshall Islands' => 'USD',
    'Mauritania' => 'MRU',
    'Mauritius' => 'MUR',
    'Mexico' => 'MXN',
    'Micronesia' => 'USD',
    'Moldova' => 'MDL',
    'Monaco' => 'EUR',
    'Mongolia' => 'MNT',
    'Montenegro' => 'EUR',
    'Morocco' => 'MAD',
    'Mozambique' => 'MZN',
    'Myanmar' => 'MMK',
    'Namibia' => 'NAD',
    'Nauru' => 'AUD',
    'Nepal' => 'NPR',
    'Netherlands' => 'EUR',
    'New Zealand' => 'NZD',
    'Nicaragua' => 'NIO',
    'Niger' => 'XOF',
    'Nigeria' => 'NGN',
    'North Korea' => 'KPW',
    'North Macedonia' => 'MKD',
    'Norway' => 'NOK',
    'Oman' => 'OMR',
    'Pakistan' => 'PKR',
    'Palau' => 'USD',
    'Panama' => 'PAB',
    'Papua New Guinea' => 'PGK',
    'Paraguay' => 'PYG',
    'Peru' => 'PEN',
    'Philippines' => 'PHP',
    'Poland' => 'PLN',
    'Portugal' => 'EUR',
    'Qatar' => 'QAR',
    'Republic of the Congo' => 'XAF',
    'Romania' => 'RON',
    'Russia' => 'RUB',
    'Rwanda' => 'RWF',
    'Saint Kitts and Nevis' => 'XCD',
    'Saint Lucia' => 'XCD',
    'Saint Vincent and the Grenadines' => 'XCD',
    'Samoa' => 'WST',
    'San Marino' => 'EUR',
    'Sao Tome and Principe' => 'STN',
    'Saudi Arabia' => 'SAR',
    'Senegal' => 'XOF',
    'Serbia' => 'RSD',
    'Seychelles' => 'SCR',
    'Sierra Leone' => 'SLL',
    'Singapore' => 'SGD',
    'Slovakia' => 'EUR',
    'Slovenia' => 'EUR',
    'Solomon Islands' => 'SBD',
    'Somalia' => 'SOS',
    'South Africa' => 'ZAR',
    'South Korea' => 'KRW',
    'South Sudan' => 'SSP',
    'Spain' => 'EUR',
    'Sri Lanka' => 'LKR',
    'Sudan' => 'SDG',
    'Suriname' => 'SRD',
    'Sweden' => 'SEK',
    'Switzerland' => 'CHF',
    'Syria' => 'SYP',
    'Taiwan' => 'TWD',
    'Tajikistan' => 'TJS',
    'Tanzania' => 'TZS',
    'Thailand' => 'THB',
    'Timor‑Leste' => 'USD',
    'Togo' => 'XOF',
    'Tonga' => 'TOP',
    'Trinidad and Tobago' => 'TTD',
    'Tunisia' => 'TND',
    'Turkey' => 'TRY',
    'Turkmenistan' => 'TMT',
    'Tuvalu' => 'AUD',
    'Uganda' => 'UGX',
    'Ukraine' => 'UAH',
    'United Arab Emirates' => 'AED',
    'United Kingdom' => 'GBP',
    'United States' => 'USD',
    'Uruguay' => 'UYU',
    'Uzbekistan' => 'UZS',
    'Vanuatu' => 'VUV',
    'Vatican City' => 'EUR',
    'Venezuela' => 'VES',
    'Vietnam' => 'VND',
    'Yemen' => 'YER',
    'Zambia' => 'ZMW',
    'Zimbabwe' => 'ZWL',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $country = trim($_POST['country']);
    $currency = trim($_POST['currency']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Basic check for database connection
    if (isset($conn) && $conn->connect_error) {
        $error = "Database connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE user_name = ?');
        $stmt->bind_param('s', $user_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already exists!";
        } else {
            $stmt = $conn->prepare('INSERT INTO users (user_name, email, full_name, password, country, currency) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssss', $user_name, $email, $full_name, $hashed_password, $country, $currency);
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $user_name;
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Error creating account! " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - ShareSplit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --navy: #0b2545;
      --navy-2: #153a6bff;
      --gold: #b8860b;
      --muted: #95a5a6;
      --card-bg: rgba(255,255,255,0.92);
    }

    html, body { height: 100%; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
      color: #22303a;
      background: linear-gradient(120deg, rgba(9,25,45,0.72), rgba(11,37,69,0.76)), url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }

    /* Global header (fixed) */
    .site-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 72px;
      z-index: 50;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 0 22px;
      background: linear-gradient(180deg, rgba(11,37,69,0.18), rgba(11,37,69,0.04));
      backdrop-filter: blur(6px);
      border-bottom:1px solid rgba(255,255,255,0.04);
      box-shadow: 0 8px 30px rgba(2,7,15,0.35);
    }
    .site-header .brand { display:flex; align-items:center; gap:12px; text-decoration:none; color:#fff; }
    .site-header .brand img { width:48px; height:48px; border-radius:8px; object-fit:cover; box-shadow: 0 6px 18px rgba(0,0,0,0.4); }
    .site-header .brand .title { font-weight:700; font-size:1.05rem; color: #ffffffff; letter-spacing: 0.02em; }

    .site-header .nav-links { display:flex; gap:8px; align-items:center; }
    .site-header .nav-links a { color: rgba(255,255,255,0.92); text-decoration:none; padding:8px 12px; font-weight:600; }
    .site-header .nav-links a:hover { color: var(--gold); text-decoration:underline; }

    .site-header .auth { display:flex; align-items:center; gap:12px; }

    /* spacer below fixed header */
    .header-spacer { height: 88px; }

    .layout-center { min-height: 100%; display:flex; align-items:center; justify-content:center; padding: 20px 12px; }

    .card-shell {
      width: 100%;
      max-width: 520px;
      border-radius: 14px;
      padding: 28px 32px;
      background: var(--card-bg);
      backdrop-filter: blur(6px) saturate(1.08);
      -webkit-backdrop-filter: blur(6px) saturate(1.08);
      box-shadow: 0 18px 46px rgba(2,7,15,0.36);
      border: 1px solid rgba(255,255,255,0.06);
    }

    .brand-inline {
      display:flex; gap: 14px; align-items:center; margin-bottom: 14px;
    }
    .brand-inline .logo {
      width: 72px; height: 72px; border-radius: 12px; overflow: hidden; display: grid; place-items: center;
      background: linear-gradient(90deg, var(--navy), var(--navy-2));
      box-shadow: 0 12px 34px rgba(2,7,15,0.45);
    }
    .brand-inline .logo img { width:100%; height:100%; object-fit:cover; }
    .brand-inline .title { font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; color: #153a6bff;}
    .brand-inline .sub { font-size: .92rem; color: #153a6bff; margin-top: 4px; font-weight:600; }

    .form-label { color: #294055; font-weight: 700; }
    .form-control, .form-select {
      border-radius: 10px;
      border: 1px solid #eaeaea;
      padding: 12px 14px;
      background: #fff;
    }
    .input-group .input-group-text { background: transparent; border:1px solid #eaeaea; color: var(--muted); padding:10px 12px; border-radius: 10px 0 0 10px; }

    #togglePwSignup {
      border-radius: 0 10px 10px 0;
      border: 1px solid #eaeaea;
      border-left: 0;
      padding: 8px 12px;
      background: transparent;
      color: #6b7b80;
    }

    .btn-primary {
      background: linear-gradient(90deg, var(--navy), var(--navy-2));
      border: none; border-radius: 12px; padding: 12px 14px; font-weight:700; color: #fff;
      box-shadow: 0 14px 38px rgba(11,37,69,0.12);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 22px 40px rgba(11,37,69,0.16); }

    .lead-text { color: rgba(18,29,40,0.76); margin-top: 6px; font-weight:600; }

    .aux-row { display:flex; justify-content:space-between; margin-top:12px; align-items:center; gap:8px; flex-wrap:wrap; }
    .aux-row a { color: var(--navy-2); text-decoration:none; font-weight:600; }
    .aux-row a:hover { color: var(--gold); text-decoration: underline; }

    .error-box {
      background: linear-gradient(90deg,#fff0f0, #fff7f6);
      color:#922b2b;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid rgba(217, 93, 93, 0.12);
      margin-bottom: 12px;
      font-weight: 600;
      display:flex;
      gap:10px;
      align-items:center;
    }

    @media (max-width:520px) {
      .card-shell { padding: 18px 18px; border-radius: 12px; }
    }
  </style>
</head>
<body>

  <!-- Header (nav) -->
  <header class="site-header">
    <a class="brand" href="index.php">
      <img src="ShareSplit%20Logo.png" alt="ShareSplit logo">
      <div class="title">ShareSplit</div>
    </a>
    <div class="d-flex align-items-center auth">
      <nav class="nav-links d-none d-md-flex me-3">
        <a href="dashboard.php">Dashboard</a>
        <a href="group.php">Groups</a>
        <a href="index.php">Home</a>
      </nav>

      <?php if (isset($_SESSION['user_name'])): ?>
        <span style="color: rgba(255,255,255,0.92); font-weight:600; margin-right:12px;">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
        <a href="logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-outline-light me-2">Login</a>
        <a href="signup.php" class="btn btn-sm btn-primary">Get Started</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="header-spacer"></div>

  <div class="layout-center">
    <div class="card-shell">

      <div class="brand-inline">
        <div class="logo">
          <img src="ShareSplit%20Logo.png" alt="ShareSplit logo" onerror="this.style.display='none';">
        </div>
        <div>
          <div class="title">Create an account</div>
          <div class="sub lead-text">Plan and manage trips with friends</div>
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="error-box"><i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label for="user_name" class="form-label">Username</label>
          <input type="text" class="form-control" id="user_name" name="user_name" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
          <label for="full_name" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Set a password">
            <button type="button" class="btn" id="togglePwSignup" title="Show/Hide"><i class="bi bi-eye"></i></button>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" id="country" name="country" onchange="updateCurrency()" required>
                <option value="">Select Country</option>
                <?php foreach ($country_currency as $country => $currency): ?>
                  <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="currency" class="form-label">Currency</label>
              <input type="text" class="form-control" id="currency" name="currency" readonly required>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign Up</button>

        <div class="aux-row">
          <a href="login.php">Already have an account? Login</a>
          <a href="index.php">Back to home</a>
        </div>
      </form>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function updateCurrency() {
      var country = document.getElementById('country').value;
      var currencyMap = <?php echo json_encode($country_currency); ?>;
      document.getElementById('currency').value = currencyMap[country] || '';
    }

    document.getElementById('togglePwSignup').addEventListener('click', function(){
      var pw = document.getElementById('password');
      var icon = this.querySelector('i');
      if (pw.type === 'password') { pw.type = 'text'; icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
      else { pw.type = 'password'; icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
    });
  </script>
</body>
</html>