<?php

session_start();
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}
require_once 'db_connect.php';


function getExchangeRate($from_currency, $to_currency) {
    static $cache = [];
    $ttl = 3600; // seconds
    $apiKey = 'd6e133f7d45d2ff6774610d3';

    if ($from_currency === $to_currency) return 1;

    // Cached and fresh
    if (isset($cache[$from_currency]['rates'])
        && isset($cache[$from_currency]['rates'][$to_currency])
        && (time() - $cache[$from_currency]['fetched_at']) < $ttl
    ) {
        return $cache[$from_currency]['rates'][$to_currency];
    }

    // If we already have the whole table and it's fresh, return
    if (isset($cache[$from_currency]['rates'])
        && (time() - $cache[$from_currency]['fetched_at']) < $ttl
    ) {
        return isset($cache[$from_currency]['rates'][$to_currency]) ? $cache[$from_currency]['rates'][$to_currency] : null;
    }

    // Fetch the full conversion table for from_currency once (cURL with timeout)
    $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$from_currency}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6); // 6s total
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // 3s connect
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['conversion_rates']) && is_array($data['conversion_rates'])) {
            $cache[$from_currency]['rates'] = $data['conversion_rates'];
            $cache[$from_currency]['fetched_at'] = time();
            return isset($data['conversion_rates'][$to_currency]) ? $data['conversion_rates'][$to_currency] : null;
        }
    }

    return null;
}

if (!isset($_GET['group_id']) || !is_numeric($_GET['group_id'])) {
    echo "Invalid group ID.";
    exit();
}

$group_id = intval($_GET['group_id']);


$stmt = $conn->prepare('SELECT group_name, creator, country, currency, created_at FROM groups WHERE id = ?');
$stmt->bind_param('i', $group_id);
$stmt->execute();
$stmt->bind_result($group_name, $creator, $country, $currency, $created_at);
if (!$stmt->fetch()) {
    echo "Group not found.";
    exit();
}
$stmt->close();

$memStmt = $conn->prepare('SELECT user_name, email, full_name, status FROM group_members WHERE group_id = ?');
$memStmt->bind_param('i', $group_id);
$memStmt->execute();
$memStmt->bind_result($user_name, $email, $full_name, $status);

$members = [];
while ($memStmt->fetch()) {
    $members[] = [
        'user_name' => $user_name,
        'email' => $email,
        'full_name' => $full_name,
        'status' => $status
    ];
}
$memStmt->close();

$member_currencies = [];
$usernames = array_column($members, 'user_name');
if (count($usernames) > 0) {
    $in = str_repeat('?,', count($usernames) - 1) . '?';
    $types = str_repeat('s', count($usernames));
    $stmt = $conn->prepare("SELECT user_name, currency FROM users WHERE user_name IN ($in)");
    $stmt->bind_param($types, ...$usernames);
    $stmt->execute();
    $stmt->bind_result($uname, $ucurrency);
    while ($stmt->fetch()) {
        $member_currencies[$uname] = $ucurrency;
    }
    $stmt->close();
}

if (isset($_POST['add_expense'])) {
    $paid_by = $_POST['paid_by'];
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    $split = $amount / count($members);

    $expStmt = $conn->prepare('INSERT INTO expenses (group_id, paid_by, amount, description, split_amount) VALUES (?, ?, ?, ?, ?)');
    $expStmt->bind_param('isdsd', $group_id, $paid_by, $amount, $description, $split);
    $expStmt->execute();
    $expStmt->close();

    header("Location: group_dashboard1.php?group_id=$group_id");
    exit();
}

if (isset($_POST['clear_expenses'])) {
    $delStmt = $conn->prepare('DELETE FROM expenses WHERE group_id = ?');
    $delStmt->bind_param('i', $group_id);
    $delStmt->execute();
    $delStmt->close();
    header("Location: group_dashboard1.php?group_id=$group_id");
    exit();
}

$expenseStmt = $conn->prepare('SELECT paid_by, amount, description, split_amount FROM expenses WHERE group_id = ?');
$expenseStmt->bind_param('i', $group_id);
$expenseStmt->execute();
$expenseStmt->bind_result($paid_by, $amount, $description, $split_amount);

$expenses = [];
while ($expenseStmt->fetch()) {
    $expenses[] = [
        'paid_by' => $paid_by,
        'amount' => $amount,
        'description' => $description,
        'split_amount' => $split_amount
    ];
}
$expenseStmt->close();

$balances = [];
foreach ($members as $payer) {
    foreach ($members as $receiver) {
        if ($payer['user_name'] !== $receiver['user_name']) {
            $balances[$payer['user_name']][$receiver['user_name']] = 0;
        }
    }
}

foreach ($expenses as $exp) {
    foreach ($members as $m) {
        if ($m['user_name'] !== $exp['paid_by']) {
            $balances[$m['user_name']][$exp['paid_by']] += $exp['split_amount'];
        }
    }
}

$net_balances = [];
foreach ($members as $m1) {
    foreach ($members as $m2) {
        if ($m1['user_name'] !== $m2['user_name']) {
            $owed = $balances[$m1['user_name']][$m2['user_name']] - $balances[$m2['user_name']][$m1['user_name']];
            if ($owed > 0) {
                $net_balances[$m1['user_name']][$m2['user_name']] = $owed;
            }
        }
    }
}


$user_currency = 'USD'; 
$userStmt = $conn->prepare('SELECT currency FROM users WHERE user_name = ?');
$userStmt->bind_param('s', $_SESSION['user_name']);
$userStmt->execute();
$userStmt->bind_result($fetched_currency);
if ($userStmt->fetch() && !empty($fetched_currency)) {
    $user_currency = $fetched_currency;
}
$userStmt->close();

// Precompute conversion rates for unique currencies (reduces repeated API calls)
$currencyRates = [];
$uniqueCurrencies = array_values(array_unique(array_merge([$currency], array_values($member_currencies))));
foreach ($uniqueCurrencies as $curr) {
    // fallback to 1 if API fails or returns null
    $currencyRates[$curr] = getExchangeRate($currency, $curr) ?: 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Group Dashboard – TripPlanner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* NAVY/GOLD THEME (from index.php) */
    :root {
      --navy: #0b2545;
      --navy-2: #153a6b;
      --gold: #b8860b;
      --muted: #95a5a6;
      --card-bg: rgba(255,255,255,0.88);
    }

    html, body { height: 100%; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
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
    .site-header .brand .title { font-weight:700; font-size:1.05rem; color: #fff; letter-spacing: 0.02em; }

    .site-header .nav-links { display:flex; gap:8px; align-items:center; }
    .site-header .nav-links a { color: rgba(255,255,255,0.92); text-decoration:none; padding:8px 12px; font-weight:600; }
    .site-header .nav-links a:hover { color: var(--gold); text-decoration:underline; }

    .site-header .auth { display:flex; align-items:center; gap:12px; }

    /* spacer below fixed header */
    .header-spacer { height: 88px; }

    /* Hero (replaces previous banner image overlay) */
    .hero {
      max-width: 950px;
      margin: 0 auto;
      margin-top: 22px;
      padding: 20px 18px 24px;
      border-radius: 16px;
      background: linear-gradient(90deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02));
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255,255,255,0.04);
      box-shadow: 0 18px 50px rgba(2,7,15,0.4);
      display:flex;
      align-items:center;
      gap:16px;
    }
    .hero .logo {
      width: 96px;
      height: 96px;
      border-radius:14px;
      overflow:hidden;
      box-shadow: 0 12px 34px rgba(2,7,15,0.45);
      background: linear-gradient(90deg, var(--navy), var(--navy-2));
      display:grid;
      place-items:center;
    }
    .hero .logo img { width:100%; height:100%; object-fit:cover; display:block; }
    .hero .hero-body { color: #fff; }
    .hero .hero-title { font-size: 1.3rem; font-weight:700; margin:0; color:#fff; letter-spacing:0.01em; }
    .hero .hero-sub { margin: 6px 0 0; color: rgba(255,255,255,0.9); font-weight:600; }

    /* Page container */
    .container {
      max-width: 980px;
      margin: 0 auto;
      padding: 2.2rem 1.2rem;
    }

    /* Card blocks retain look, but tweaked to fit theme */
    .block {
      background: var(--card-bg);
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(2,7,15,0.22);
      padding: 22px;
      margin-bottom: 22px;
      border: 1px solid rgba(0,0,0,0.06);
    }

    .block-heading {
      font-size: 1.4rem;
      color: var(--navy);
      font-weight:700;
      margin-bottom: 12px;
      letter-spacing: 0.02em;
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
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 18px 34px rgba(11,37,69,0.18); }

    .btn-outline-secondary {
      border: 1px solid rgba(0,0,0,0.06);
      background: transparent;
      color: #2c3e50;
      padding: 8px 12px;
      border-radius: 8px;
    }

    /* Minor tweak: badges & info colors */
    .alert-you-owe { background: linear-gradient(90deg, #ffecec, #ffd6d6); color:#8b2626; border-radius: 12px; padding:10px; }
    .alert-owes-you { background: linear-gradient(90deg, #e6f7ef, #c9efd9); color:#1b6a3d; border-radius:12px; padding:10px; }
    .alert-info-debt { background: linear-gradient(90deg, rgba(0,0,0,0.03), rgba(0,0,0,0.02)); color:#2c3e50; padding:10px; border-radius:12px; }

    /* Table & forms */
    .table thead { background: linear-gradient(90deg, var(--navy), var(--navy-2)); color:#fff; font-weight:700; }
    .form-control, .form-select { border-radius: 10px; border: 1px solid #eaeaea; padding:10px 12px; }
    .modal-header, .modal-footer { background: linear-gradient(90deg, var(--navy), var(--navy-2)); color:#fff; border: none; }

    /* Responsive tweaks */
    @media (max-width: 900px) {
      .hero { flex-direction: column; align-items:center; text-align:center; gap:10px; }
      .hero .logo { width:84px; height:84px; }
      .container { padding: 1rem; }
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

  <div class="container">
    <!-- HERO (new header) -->
    <div class="hero">
      <div class="logo">
        <img src="ShareSplit%20Logo.png" alt="ShareSplit logo" onerror="this.style.display='none';">
      </div>
      <div class="hero-body">
        <h2 class="hero-title">Welcome to ShareSplit</h2>
        <div class="hero-sub">Group: <?php echo htmlspecialchars($group_name); ?></div>
      </div>
      <div class="ms-auto d-flex align-items-center gap-2">
        <a class="btn btn-primary" href="group.php" style="white-space:nowrap;">← Back to My Groups</a>
      </div>
    </div>

    <!-- Group Info Block -->
    <div class="block">
      <div class="block-heading">Group Information</div>
      <div class="row">
        <div class="col-md-3 info-row"><strong>Created by:</strong> <?php echo htmlspecialchars($creator); ?></div>
        <div class="col-md-3 info-row"><strong>Country:</strong> <?php echo htmlspecialchars($country); ?></div>
        <div class="col-md-3 info-row"><strong>Currency:</strong> <?php echo htmlspecialchars($currency); ?></div>
        <div class="col-md-3 info-row"><strong>Created at:</strong> <?php echo htmlspecialchars($created_at); ?></div>
      </div>
    </div>

    <!-- Members Block -->
    <div class="block">
      <div class="d-flex justify-content-between align-items-center">
        <div class="block-heading">Group Members</div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">+ Add Expense</button>
      </div>

      <?php if (count($members) > 0): ?>
        <div class="table-responsive mt-3">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>User Name</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($members as $m): ?>
                <tr>
                  <td><?php echo htmlspecialchars($m['user_name']); ?></td>
                  <td><?php echo htmlspecialchars($m['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($m['email']); ?></td>
                  <td>
                    <?php if ($m['status'] === 'accepted'): ?>
                      <span class="badge bg-success">Accepted</span>
                    <?php elseif ($m['status'] === 'invited'): ?>
                      <span class="badge bg-warning text-dark">Invited</span>
                    <?php else: ?>
                      <span class="badge bg-secondary"><?php echo htmlspecialchars($m['status']); ?></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="mt-3">No members in this group.</p>
      <?php endif; ?>
    </div>

    <!-- Expenses Block -->
    <div class="block">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="block-heading">Group Expenses</div>
        <form method="POST" class="m-0">
          <button type="submit" name="clear_expenses" class="btn btn-outline-secondary btn-sm"
            onclick="return confirm('Are you sure you want to clear all expenses?');">
            <i class="bi bi-trash"></i> Clear Expenses
          </button>
        </form>
      </div>
      <?php if (count($expenses) > 0): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover align-middle expense-table">
            <thead class="table-primary custom-expense-thead">
              <tr>
                <th><i class="bi bi-person-fill"></i> Paid By</th>
                <th><i class="bi bi-card-text"></i> Description</th>
                <th><i class="bi bi-currency-exchange"></i> Amount (<?php echo htmlspecialchars($currency); ?>)</th>
                <th><i class="bi bi-people-fill"></i> Split Amount (<?php echo htmlspecialchars($currency); ?>)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($expenses as $exp): ?>
                <tr>
                  <td>
                    <?php
                      foreach ($members as $m) {
                        if ($m['user_name'] === $exp['paid_by']) {
                          echo '<span class="fw-semibold text-primary">' . htmlspecialchars($m['full_name']) . '</span> <span class="text-muted">(' . htmlspecialchars($m['user_name']) . ')</span>';
                          break;
                        }
                      }
                    ?>
                  </td>
                  <td><?php echo htmlspecialchars($exp['description']); ?></td>
                  <td><span class="badge bg-light text-dark fs-6"><?php echo htmlspecialchars(number_format($exp['amount'], 2)); ?></span></td>
                  <td><span class="badge bg-info text-dark fs-6"><?php echo htmlspecialchars(number_format($exp['split_amount'], 2)); ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p>No expenses added yet.</p>
      <?php endif; ?>
    </div>

    <!-- Balances Block -->
    <div class="block">
      <div class="block-heading">Your Balances</div>
      <?php foreach ($members as $m): ?>
        <?php
          if ($m['user_name'] !== $_SESSION['user_name']) {
            $balance = $balances[$_SESSION['user_name']][$m['user_name']];
            if ($balance < 0) {
              ?>
              <div class="alert alert-you-owe">
                You owe <strong><?php echo htmlspecialchars($m['full_name']); ?></strong>
                <?php echo htmlspecialchars(number_format(abs($balance), 2)); ?> <?php echo htmlspecialchars($currency); ?>
              </div>
              <?php
            } elseif ($balance > 0) {
              ?>
              <div class="alert alert-owes-you">
                <strong><?php echo htmlspecialchars($m['full_name']); ?></strong> owes you
                <?php echo htmlspecialchars(number_format($balance, 2)); ?> <?php echo htmlspecialchars($currency); ?>
              </div>
              <?php
            }
          }
        ?>
      <?php endforeach; ?>

      <div class="block-heading mt-4">Who Owes Whom</div>
      <?php
      foreach ($members as $m1) {
        foreach ($members as $m2) {
          if (
            $m1['user_name'] !== $m2['user_name'] &&
            isset($net_balances[$m1['user_name']][$m2['user_name']]) &&
            $net_balances[$m1['user_name']][$m2['user_name']] > 0
          ) {
            echo '<div class="alert alert-info-debt">';
            echo htmlspecialchars($m1['full_name']) . ' (' . htmlspecialchars($m1['user_name']) . ') owes ';
            echo htmlspecialchars($m2['full_name']) . ' (' . htmlspecialchars($m2['user_name']) . ') ';
            echo '<strong>' . htmlspecialchars(number_format($net_balances[$m1['user_name']][$m2['user_name']], 2)) . '</strong> ';
            echo htmlspecialchars($currency);
            echo '</div>';
          }
        }
      }
      ?>
    </div>

    <!-- Summary Block -->
    <div class="block">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="block-heading">Group Expense Summary</div>
        <button class="btn btn-primary btn-sm" onclick="downloadSummaryPDF()">Download PDF</button>
      </div>
      <div id="summary-table-container">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Member</th>
                <th>Total Paid (<?php echo htmlspecialchars($currency); ?>)</th>
                <th>Share of Expenses (<?php echo htmlspecialchars($currency); ?>)</th>
                <th>Balance (<?php echo htmlspecialchars($currency); ?>)</th>
                <th>Converted Balance (Member Currency)</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // compute totals
              $total_paid = [];
              $share = [];
              foreach ($members as $m) {
                $total_paid[$m['user_name']] = 0;
                $share[$m['user_name']] = 0;
              }
              foreach ($expenses as $exp) {
                $total_paid[$exp['paid_by']] += $exp['amount'];
                foreach ($members as $m) {
                  $share[$m['user_name']] += $exp['split_amount'];
                }
              }
              foreach ($members as $m) {
                $bal = $total_paid[$m['user_name']] - $share[$m['user_name']];
                $bal_str = ($bal >= 0 ? '+' : '') . number_format($bal, 2);
                $member_currency = isset($member_currencies[$m['user_name']]) ? $member_currencies[$m['user_name']] : $currency;
                $rate = getExchangeRate($currency, $member_currency);
                $converted = $bal * $rate;
                $converted_str = ($converted >= 0 ? '+' : '') . number_format($converted, 2) . ' ' . $member_currency;

                echo '<tr>';
                echo '<td>' . htmlspecialchars($m['full_name']) . ' (' . htmlspecialchars($m['user_name']) . ')</td>';
                echo '<td>' . htmlspecialchars(number_format($total_paid[$m['user_name']], 2)) . '</td>';
                echo '<td>' . htmlspecialchars(number_format($share[$m['user_name']], 2)) . '</td>';
                echo '<td>';
                if ($bal < 0) {
                  echo '<span class="text-danger">' . $bal_str . ' (owes)</span>';
                } else {
                  echo '<span class="text-success">' . $bal_str . ' (is owed)</span>';
                }
                echo '</td>';
                echo '<td>' . htmlspecialchars($converted_str);

                if ($converted < 0 && isset($net_balances[$m['user_name']])) {
                  foreach ($net_balances[$m['user_name']] as $payee_username => $amount) {
                    if ($amount > 0) {
                      $payee_fullname = '';
                      foreach ($members as $mm) {
                        if ($mm['user_name'] === $payee_username) {
                          $payee_fullname = $mm['full_name'];
                          break;
                        }
                      }
                      $payee_currency = isset($member_currencies[$payee_username]) ? $member_currencies[$payee_username] : $currency;
                      $payee_rate = isset($currencyRates[$payee_currency]) ? $currencyRates[$payee_currency] : 1;
                      $converted_amount = $amount * $payee_rate;
                      $converted_amount_str = number_format($converted_amount, 2) . ' ' . $payee_currency;

                      $qr_message = $m['full_name'] . ' (' . $m['user_name'] . ') needs to pay ' .
                                    $payee_fullname . ' (' . $payee_username . ') ' .
                                    $converted_amount_str;
                      $qr_data = urlencode($qr_message);
                      $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=$qr_data";
                      $qr_id = 'qr_' . md5($m['user_name'] . $payee_username);

                      echo '<br><button class="btn btn-outline-secondary btn-sm qr-button" onclick="toggleQR(\'' . $qr_id . '\')">Show QR</button>';
                      echo '<div id="' . $qr_id . '" style="display:none;margin-top:5px;text-align:center;">';
                      echo '<div class="qr-label mb-1">Pay to: <strong>' . htmlspecialchars($payee_fullname) . ' (' . htmlspecialchars($payee_username) . ')</strong></div>';
                      echo '<img src="' . $qr_url . '" alt="QR Code" title="' . htmlspecialchars($qr_message) . '" /> ';
                      echo '</div>';
                    }
                  }
                }

                echo '</td>';
                echo '</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

   <div class="d-flex justify-content-between mt-3 mb-4">
    <a href="group.php" class="btn btn-primary btn-lg" style="min-width: 200px;">← Back to My Groups</a>

    <a href="logout.php" class="btn btn-primary btn-lg" style="min-width: 200px;">Logout</a>
</div>

  <!-- Expense Modal -->
  <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="expenseModalLabel">Add Expense</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="paid_by" class="form-label">Paid By</label>
              <select class="form-select" id="paid_by" name="paid_by" required>
                <?php foreach ($members as $m): ?>
                  <option value="<?php echo htmlspecialchars($m['user_name']); ?>">
                    <?php echo htmlspecialchars($m['full_name']); ?> (<?php echo htmlspecialchars($m['user_name']); ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="amount" class="form-label">Amount (<?php echo htmlspecialchars($currency); ?>)</label>
              <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <input type="text" class="form-control" id="description" name="description" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- JS scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <script>
    // Fade-in on scroll
    function revealOnScroll() {
      const items = document.querySelectorAll('.fade-in');
      const windowBottom = window.innerHeight;
      items.forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < windowBottom - 100) {
          el.classList.add('visible');
        }
      });
    }
    window.addEventListener('scroll', revealOnScroll);
    window.addEventListener('load', revealOnScroll);

    function toggleQR(id) {
      const el = document.getElementById(id);
      if (!el) return;
      el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
    }

    function downloadSummaryPDF() {
      const container = document.getElementById('summary-table-container');
      if (!container) return;
      html2canvas(container).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const imgWidth = pageWidth - 20;
        const imgHeight = canvas.height * imgWidth / canvas.width;
        pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
        pdf.save('group-expense-summary.pdf');
      });
    }
  </script>

  <footer>
    &copy; <?php echo date('Y'); ?> TripPlanner. All rights reserved.
  </footer>
</body>
</html>



