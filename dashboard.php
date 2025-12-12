<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}
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

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'], $_POST['members'], $_POST['country'], $_POST['currency'])) {
    $group_name = trim($_POST['group_name']);
    $members = array_map('trim', explode(',', $_POST['members']));
    $creator = $_SESSION['user_name'];
    $country = trim($_POST['country']);
    $currency = trim($_POST['currency']);
    $stmt = $conn->prepare('INSERT INTO groups (group_name, creator, country, currency) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $group_name, $creator, $country, $currency);
    if ($stmt->execute()) {
        $group_id = $stmt->insert_id;

        $userStmt = $conn->prepare('SELECT user_name, email, full_name FROM users WHERE user_name = ?');
        $userStmt->bind_param('s', $creator);
        $userStmt->execute();
        $userStmt->bind_result($uname, $email, $full_name);
        if ($userStmt->fetch()) {
            $userStmt->close();
            $stmt2 = $conn->prepare('INSERT INTO group_members (group_id, user_name, email, full_name) VALUES (?, ?, ?, ?)');
            $stmt2->bind_param('isss', $group_id, $uname, $email, $full_name);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $userStmt->close();
        }
        foreach ($members as $member) {
            if ($member !== $creator && $member !== '') {
                $checkStmt = $conn->prepare('SELECT user_name, email, full_name FROM users WHERE user_name = ?');
                $checkStmt->bind_param('s', $member);
                $checkStmt->execute();
                $checkStmt->bind_result($uname, $email, $full_name);
                if ($checkStmt->fetch()) {
                    $checkStmt->close();
                    $stmt2 = $conn->prepare('INSERT INTO group_members (group_id, user_name, email, full_name) VALUES (?, ?, ?, ?)');
                    $stmt2->bind_param('isss', $group_id, $uname, $email, $full_name);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    $checkStmt->close();
                }
            }
        }
        $message = 'Group created successfully!';
        header('Location: group.php');
        exit();

    } else {
        $message = 'Error creating group.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - ShareSplit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --navy: #0b2545;
      --navy-2: #153a6b;
      --gold: #b8860b;
      --muted: #95a5a6;
      --card-bg: rgba(255,255,255,0.92);
    }

    html, body { height: 100%; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
      color: #22303a;
      background: linear-gradient(120deg, rgba(9,25,45,0.72), rgba(11,37,69,0.76)), url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }

    /* Global header */
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
    .site-header .brand .title { font-weight:700; font-size:1.05rem; color: #fff; letter-spacing: 0.02em; }

    .site-header .nav-links { display:flex; gap:8px; align-items:center; }
    .site-header .nav-links a { color: rgba(255,255,255,0.92); text-decoration:none; padding:8px 12px; font-weight:600; }
    .site-header .nav-links a:hover { color: var(--gold); text-decoration:underline; }

    .site-header .auth { display:flex; align-items:center; gap:12px; }

    .header-spacer { height: 88px; }

    .dashboard-container {
      max-width: 980px;
      margin: 0 auto;
      padding: 2.2rem 1.2rem;
    }

    .card-shell {
      background: var(--card-bg);
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(2,7,15,0.22);
      padding: 24px;
      margin-bottom: 18px;
      border: 1px solid rgba(0,0,0,0.06);
    }

    .welcome {
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 12px;
      color: var(--navy);
      letter-spacing: 0.01em;
    }

    .form-label {
      font-weight: 700;
      color: #294055;
    }

    .form-control, .form-select {
      border-radius: 10px;
      border: 1px solid #eaeaea;
      padding: 10px 12px;
      background: #fff;
    }

    .btn-primary {
      background: linear-gradient(90deg, var(--navy), var(--navy-2));
      border: none;
      color: #fff;
      font-weight: 700;
      border-radius: 10px;
      padding: 10px 14px;
      box-shadow: 0 8px 30px rgba(11,37,69,0.22);
    }

    .btn-outline-secondary {
      border: 1px solid rgba(0,0,0,0.06);
      background: transparent;
      color: #2c3e50;
      padding: 8px 12px;
      border-radius: 8px;
    }

    .member-list { margin-top: 12px; min-height: 32px; }
    .member-item { display:inline-block; background: linear-gradient(90deg, #f4f6f8 0%, #ffffff 100%); padding: 7px 14px; border-radius: 14px; margin: 4px 6px 4px 0; color: #22303a; font-weight:700; }

    @media (max-width: 900px) {
      .dashboard-container { padding: 1rem; }
    }
  </style>
</head>
<body>
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

  <div class="dashboard-container">
    <div class="card-shell">
      <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</div>

      <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <h4 style="color:var(--navy); font-weight:700; margin-bottom:14px;">Create a Group</h4>
      <form method="POST" class="mb-4">
        <div class="mb-3">
          <label for="group_name" class="form-label">Group Name</label>
          <input type="text" class="form-control" id="group_name" name="group_name" required>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="country" class="form-label">Destination Country</label>
              <select class="form-select" id="country" name="country" onchange="updateCurrency()" required>
                <option value="">Select Country</option>
                <?php foreach ($country_currency as $c => $cur): ?>
                  <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="currency" class="form-label">Currency</label>
              <select class="form-select" id="currency" name="currency" required>
                <option value="">Select Currency</option>
                <?php
                  $unique_currencies = array_unique(array_values($country_currency));
                  sort($unique_currencies);
                  foreach ($unique_currencies as $cur): ?>
                    <option value="<?php echo $cur; ?>"><?php echo $cur; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Add Members</label>
          <div class="input-group">
            <input type="text" class="form-control" id="search_user" placeholder="Enter user name">
            <button type="button" class="btn btn-outline-secondary" id="add_member_btn">Add</button>
          </div>
          <div id="search_result" class="mt-2"></div>
          <div class="member-list" id="member_list"></div>
        </div>

        <input type="hidden" name="members" id="members_hidden">
        <button type="submit" class="btn btn-primary">Create Group</button>
      </form>

      <div class="row g-3 mt-3">
        <div class="col-12 col-md-6 d-grid">
          <a href="group.php" class="btn btn-info w-100 btn-lg">View My Groups & Invitations</a>
        </div>
        <div class="col-12 col-md-6 d-grid">
          <a href="logout.php" class="btn btn-outline-secondary w-100 btn-lg">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function updateCurrency() {
      var country = document.getElementById('country').value;
      var currencyMap = <?php echo json_encode($country_currency); ?>;
      var currencySelect = document.getElementById('currency');
      var currency = currencyMap[country] || '';
      for (var i = 0; i < currencySelect.options.length; i++) {
          if (currencySelect.options[i].value === currency) {
              currencySelect.selectedIndex = i;
              break;
          }
      }
    }

    let members = [];
    $('#add_member_btn').click(function() {
      let user = $('#search_user').val().trim();
      if (!user || members.includes(user)) return;
      $.get('search_user.php', {user_name: user}, function(data) {
          if (data === 'found') {
              members.push(user);
              updateMemberList();
              $('#search_result').html('<span class="text-success">User added!</span>');
              $('#search_user').val('');
          } else {
              $('#search_result').html('<span class="text-danger">User not found!</span>');
          }
      });
    });
    function updateMemberList() {
      $('#member_list').html('');
      members.forEach(function(user, idx) {
        $('#member_list').append(
          `<span class="member-item">${user} <span class="remove-member" onclick="removeMember(${idx})">&times;</span></span>`
        );
      });
      $('#members_hidden').val(members.join(','));
    }
    window.removeMember = function(idx) {
      members.splice(idx, 1);
      updateMemberList();
    }
  </script>
</body>
</html>