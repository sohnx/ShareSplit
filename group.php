<?php

session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

$user_name = $_SESSION['user_name'];

// Handle accepting group invitation
if (isset($_GET['accept']) && is_numeric($_GET['accept'])) {
    $group_id = intval($_GET['accept']);
    $stmt = $conn->prepare('UPDATE group_members SET status = "accepted" WHERE group_id = ? AND user_name = ?');
    $stmt->bind_param('is', $group_id, $user_name);
    if ($stmt->execute()) {
        header("Location: group.php");
        exit();
    }
    $stmt->close();
}

// Handle deleting groups (only if user is creator)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_groups']) && !empty($_POST['delete_group_ids'])) {
    $delete_ids = $_POST['delete_group_ids'];
    foreach ($delete_ids as $gid) {
        // Only allow deletion if user is the creator
        $checkStmt = $conn->prepare('SELECT creator FROM groups WHERE id = ?');
        $checkStmt->bind_param('i', $gid);
        $checkStmt->execute();
        $checkStmt->bind_result($creator);
        if ($checkStmt->fetch() && $creator === $user_name) {
            $checkStmt->close();
            // Delete group members
            $delMembers = $conn->prepare('DELETE FROM group_members WHERE group_id = ?');
            $delMembers->bind_param('i', $gid);
            $delMembers->execute();
            $delMembers->close();
            // Delete group
            $delGroup = $conn->prepare('DELETE FROM groups WHERE id = ?');
            $delGroup->bind_param('i', $gid);
            $delGroup->execute();
            $delGroup->close();
        } else {
            $checkStmt->close();
        }
    }
    header("Location: group.php");
    exit();
}

// Get pending invitations
$invStmt = $conn->prepare('SELECT gm.group_id, g.group_name, g.creator FROM group_members gm JOIN groups g ON gm.group_id = g.id WHERE gm.user_name = ? AND gm.status = "invited"');
$invStmt->bind_param('s', $user_name);
$invStmt->execute();
$invResult = $invStmt->get_result();

$invitations = [];
while ($row = $invResult->fetch_assoc()) {
    $invitations[] = $row;
}
$invStmt->close();

// Get accepted groups
$grpStmt = $conn->prepare('SELECT g.id, g.group_name, g.creator FROM group_members gm JOIN groups g ON gm.group_id = g.id WHERE gm.user_name = ? AND gm.status = "accepted"');
$grpStmt->bind_param('s', $user_name);
$grpStmt->execute();
$grpResult = $grpStmt->get_result();

$groups = [];
while ($row = $grpResult->fetch_assoc()) {
    $groups[] = ['group_id' => $row['id'], 'group_name' => $row['group_name'], 'creator' => $row['creator']];
}
$grpStmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Groups - ShareSplit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    .site-header .brand img { width:48px; height:48px; border-radius:8px; object-fit:cover; box-shadow: 0 6px 18px rgba(0,0,0,0.35); }
    .site-header .title { font-weight:700; font-size:1.05rem; color:#fff; letter-spacing: 0.02em; }

    .site-header .nav-links { display:flex; gap:8px; align-items:center; }
    .site-header .nav-links a { color: rgba(255,255,255,0.92); text-decoration:none; padding:8px 12px; font-weight:600; }
    .site-header .nav-links a:hover { color: var(--gold); text-decoration:underline; }

    .site-header .auth { display:flex; align-items:center; gap:12px; }

    .header-spacer { height: 88px; }

    /* Container and card */
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
      border: 1px solid rgba(0,0,0,0.06);
    }

    h2 {
      color: #153a6bff;
      font-weight: 700;
      margin-bottom: 8px;
      letter-spacing: 0.03em;
    }

    .section-title {
      color: var(--navy);
      font-weight: 700;
      margin-bottom: 12px;
      letter-spacing: 0.02em;
    }

    .selector-tabs {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 18px;
    }
    .selector-tab {
      padding: 10px 20px;
      border-radius: 12px;
      background: rgba(255,255,255,0.06);
      color: #153a6bff;
      font-weight: 700;
      border: 1px solid rgba(255,255,255,0.04);
      cursor: pointer;
      transition: background 0.15s, color 0.15s, transform 0.12s;
    }
    .selector-tab.active {
      background: linear-gradient(90deg, var(--navy), var(--navy-2));
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(11,37,69,0.14);
    }

    .tab-content { display: none; }
    .tab-content.active { display:block; }

    .list-group-item {
      border-radius: 12px;
      margin-bottom: 10px;
      background: rgba(255,255,255,0.96);
      border: 1px solid rgba(0,0,0,0.04);
      box-shadow: 0 6px 18px rgba(2,7,15,0.06);
      color: #22303a;
    }
    .list-group-item .btn {
      border-radius: 10px;
      font-weight: 700;
    }

    .btn-primary {
      background: linear-gradient(90deg, var(--navy), var(--navy-2));
      border: none; color: #fff;
    }
    .btn-primary:hover { transform: translateY(-2px); }

    .btn-accept {
      background: linear-gradient(90deg, var(--navy-2), var(--navy));
      color: #fff;
      border: none;
    }

    .btn-outline-secondary {
      border: 1px solid rgba(0,0,0,0.06);
      background: transparent;
      color: #22303a;
    }

    .delete-checkbox {
      transform: scale(1.15);
      margin-right: 10px;
    }
    .btn-delete-selected {
      background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
      color: #fff;
      font-weight: 700;
      border-radius: 10px;
      border: none;
    }

    .delete-warning {
      color: #c0392b;
      font-size: 0.98em;
      margin-bottom: 10px;
      text-align: center;
    }

    /* Modal */
    .glass-modal {
      background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(255,255,255,0.96));
      border-radius: 12px;
      box-shadow: 0 12px 48px rgba(2,7,15,0.18);
      border: 1px solid rgba(0,0,0,0.04);
    }
    .modal-header-gradient {
      background: linear-gradient(90deg, var(--navy), var(--navy-2));
      color: #fff;
      border-bottom: none;
    }
    .modal-body-glass {
      background: rgba(255,255,255,0.9);
      border-radius: 0 0 12px 12px;
      padding: 1.5rem;
    }

    @media (max-width: 900px) {
      .dashboard-container { padding: 1rem; }
      .card-shell { padding: 18px; }
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

  <div class="dashboard-container">
    <div class="card-shell">
      <h2>Welcome, <?php echo htmlspecialchars($user_name); ?></h2>

      <div class="selector-tabs mt-2">
        <button class="selector-tab active" id="tab-groups" onclick="showTab('groups')">Your Groups</button>
        <button class="selector-tab" id="tab-invitations" onclick="showTab('invitations')">Invitations</button>
        <button class="selector-tab" id="tab-delete" onclick="showTab('delete')">Delete Groups</button>
      </div>

      <div id="groups" class="tab-content active">
        <h4 class="section-title">Your Groups</h4>
        <?php if (count($groups) > 0): ?>
            <ul class="list-group mb-4">
                <?php foreach ($groups as $group): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($group['group_name']); ?></strong>
                            <span class="text-muted"> (Created by <?php echo htmlspecialchars($group['creator']); ?>)</span>
                        </div>
                        <div class="d-flex gap-2">
                          <a href="group_dashboard1.php?group_id=<?php echo intval($group['group_id']); ?>" class="btn btn-sm btn-primary">Open</a>
                          <button class="btn btn-sm btn-outline-secondary" onclick="showGroupInfo(<?php echo intval($group['group_id']); ?>)">View</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">You have not joined any groups yet.</p>
        <?php endif; ?>
      </div>

      <div id="invitations" class="tab-content">
        <h4 class="section-title">Invitations</h4>
        <?php if (count($invitations) > 0): ?>
            <ul class="list-group">
                <?php foreach ($invitations as $invite): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($invite['group_name']); ?></strong>
                            <span class="text-muted"> (Invited by <?php echo htmlspecialchars($invite['creator']); ?>)</span>
                        </div>
                        <a href="group.php?accept=<?php echo intval($invite['group_id']); ?>" class="btn btn-accept btn-sm">Accept</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No invitations at the moment.</p>
        <?php endif; ?>
      </div>

      <div id="delete" class="tab-content">
        <h4 class="section-title">Delete Groups</h4>
        <div class="delete-warning">
            Only groups you created can be deleted. Deleting a group will remove all its data for all members!
        </div>
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete the selected group(s)? This cannot be undone.');">
            <?php
            $hasDeletable = false;
            foreach ($groups as $group) {
                if ($group['creator'] === $user_name) {
                    $hasDeletable = true;
                    break;
                }
            }
            ?>
            <?php if ($hasDeletable): ?>
                <ul class="list-group mb-3">
                    <?php foreach ($groups as $group): ?>
                        <?php if ($group['creator'] === $user_name): ?>
                            <li class="list-group-item d-flex align-items-center">
                                <input type="checkbox" class="delete-checkbox" name="delete_group_ids[]" value="<?php echo intval($group['group_id']); ?>">
                                <span>
                                    <strong><?php echo htmlspecialchars($group['group_name']); ?></strong>
                                    <span class="text-muted"> (You are the creator)</span>
                                </span>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" name="delete_groups" class="btn btn-delete-selected w-100">Delete Selected</button>
            <?php else: ?>
                <p class="text-muted text-center">You have no groups you can delete.</p>
            <?php endif; ?>
        </form>
      </div>

      <div class="mt-3">
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
      </div>
    </div>
  </div>

<!-- Group Info Modal -->
<div class="modal fade" id="groupInfoModal" tabindex="-1" aria-labelledby="groupInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content glass-modal">
      <div class="modal-header modal-header-gradient">
        <div class="d-flex align-items-center gap-2">
          <span style="font-size:1.6rem;"><i class="bi bi-people-fill" style="color:#fff;"></i></span>
          <h5 class="modal-title" id="groupInfoModalLabel" style="color:#fff;font-weight:700;letter-spacing:0.03em;">Group Info</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-body-glass" id="groupInfoContent">
        <div class="text-center text-muted py-4">
          <div class="spinner-border text-primary"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showTab(tab) {
        document.getElementById('groups').classList.remove('active');
        document.getElementById('invitations').classList.remove('active');
        document.getElementById('delete').classList.remove('active');
        document.getElementById('tab-groups').classList.remove('active');
        document.getElementById('tab-invitations').classList.remove('active');
        document.getElementById('tab-delete').classList.remove('active');
        if (tab === 'groups') {
            document.getElementById('groups').classList.add('active');
            document.getElementById('tab-groups').classList.add('active');
        } else if (tab === 'invitations') {
            document.getElementById('invitations').classList.add('active');
            document.getElementById('tab-invitations').classList.add('active');
        } else if (tab === 'delete') {
            document.getElementById('delete').classList.add('active');
            document.getElementById('tab-delete').classList.add('active');
        }
    }

    function showGroupInfo(groupId) {
        var modal = new bootstrap.Modal(document.getElementById('groupInfoModal'));
        document.getElementById('groupInfoContent').innerHTML = '<div class="text-center text-muted py-4"><div class="spinner-border text-primary"></div></div>';
        modal.show();
        fetch('group_info_ajax.php?group_id=' + groupId)
            .then(response => response.text())
            .then(html => {
                document.getElementById('groupInfoContent').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('groupInfoContent').innerHTML = '<div class="alert alert-danger">Failed to load group info.</div>';
            });
    }
</script>
</body>
</html>