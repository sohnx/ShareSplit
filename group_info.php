 <?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}
require_once 'db_connect.php';

if (!isset($_GET['group_id']) || !is_numeric($_GET['group_id'])) {
    echo "Invalid group ID.";
    exit();
}

$group_id = intval($_GET['group_id']);

$stmt = $conn->prepare('SELECT group_name, creator, created_at FROM groups WHERE id = ?');
$stmt->bind_param('i', $group_id);
$stmt->execute();
$stmt->bind_result($group_name, $creator, $created_at);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Group Info - TripPlanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            /* Background photo with subtle overlay */
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.4) 100%),
                url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            color: #2c3e50;
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        .navbar {
            background: linear-gradient(90deg, #ecf0f1 0%, #bdc3c7 100%);
            box-shadow: 0 2px 16px 0 rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid #95a5a6;
            border-radius: 0 0 22px 22px;
            padding: 0.7rem 0;
            margin-bottom: 2rem;
        }
        
        .navbar-brand {
            color: #16a085;
            font-weight: bold;
            font-size: 2rem;
            letter-spacing: 0.04em;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1.2rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-radius: 22px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid #95a5a6;
        }
        
        .page-header h2 {
            color: #16a085;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .page-header h2 i {
            font-size: 2.2rem;
        }
        
        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1.05rem;
            gap: 0.8rem;
        }
        
        .info-row i {
            color: #153a6bff;
            font-size: 1.3rem;
            min-width: 24px;
        }
        
        .info-row strong {
            color: #2c3e50;
            font-weight: 600;
            min-width: 120px;
        }
        
        .info-row span {
            color: #34495e;
        }
        
        .block-title {
            font-size: 1.5rem;
            color: #16a085;
            margin-bottom: 1.5rem;
            margin-top: 2rem;
            border-left: 5px solid #1abc9c;
            padding-left: 1rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        
        .table-container {
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-radius: 18px;
            padding: 2rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border: 1px solid #95a5a6;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
            border-collapse: collapse;
        }
        
        .table thead {
            background: linear-gradient(90deg, #16a085 0%, #1abc9c 100%);
            color: #fff;
        }
        
        .table thead th {
            border: none;
            padding: 1rem 1.2rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-align: left;
            vertical-align: middle;
        }
        
        .table thead th i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }
        
        .table tbody td {
            padding: 1rem 1.2rem;
            border: 1px solid #bdc3c7;
            background: rgba(255, 255, 255, 0.95);
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: background 0.3s, transform 0.2s;
        }
        
        .table tbody tr:hover {
            background: rgba(22, 160, 133, 0.05) !important;
            transform: translateX(4px);
        }
        
        .table tbody tr:last-child td {
            border-bottom: 1px solid #bdc3c7;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: capitalize;
            letter-spacing: 0.02em;
        }
        
        .status-accepted {
            background: linear-gradient(90deg, #d5f4e6 0%, #a9dfbf 100%);
            color: #186a3b;
        }
        
        .status-invited {
            background: linear-gradient(90deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }
        
        .status-other {
            background: linear-gradient(90deg, #e2e3e5 0%, #d3d6d8 100%);
            color: #383d41;
        }
        
        .empty-message {
            text-align: center;
            padding: 2rem;
            color: #34495e;
            font-size: 1.05rem;
            font-style: italic;
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }
        
        .btn {
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 0.6rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(90deg, #16a085 0%, #1abc9c 100%);
            color: #fff;
        }
        
        .btn-primary:hover {
            background: linear-gradient(90deg, #1abc9c 0%, #16a085 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(22, 160, 133, 0.3);
        }
        
        .btn-outline-secondary {
            background: transparent;
            color: #2c3e50;
            border: 2px solid #95a5a6;
        }
        
        .btn-outline-secondary:hover {
            background: #34495e;
            color: #fff;
            border-color: #34495e;
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: linear-gradient(90deg, #2c3e50 0%, #34495e 100%);
            color: #ecf0f1;
        }
        
        .btn-info:hover {
            background: linear-gradient(90deg, #34495e 0%, #2c3e50 100%);
            color: #ecf0f1;
            transform: translateY(-2px);
        }
        
        .no-members-container {
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-radius: 18px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border: 1px solid #95a5a6;
            margin-bottom: 2.5rem;
        }
        
        .no-members-container i {
            font-size: 3rem;
            color: #95a5a6;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }
            
            .table thead th, .table tbody td {
                padding: 0.8rem 0.6rem;
                font-size: 0.95rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="dashboard.php"><i class="bi bi-globe"></i> TripPlanner</a>
        </div>
    </nav>
    
    <div class="container py-4">
    <div class="page-header">
        <h2><i class="bi bi-info-circle-fill"></i> <?php echo htmlspecialchars($group_name); ?></h2>
        <div class="info-row">
            <i class="bi bi-person-fill"></i>
            <strong>Created by:</strong>
            <span><?php echo htmlspecialchars($creator); ?></span>
        </div>
        <div class="info-row">
            <i class="bi bi-calendar-fill"></i>
            <strong>Created at:</strong>
            <span><?php echo htmlspecialchars($created_at); ?></span>
        </div>
    </div>

    <h3 class="block-title"><i class="bi bi-people-fill"></i> Group Members</h3>
    
    <?php if (count($members) > 0): ?>
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="bi bi-person-badge"></i> User Name</th>
                            <th><i class="bi bi-person"></i> Full Name</th>
                            <th><i class="bi bi-envelope"></i> Email</th>
                            <th><i class="bi bi-check-circle"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($m['user_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($m['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($m['email']); ?></td>
                                <td>
                                    <?php 
                                        if ($m['status'] === 'accepted') {
                                            echo '<span class="status-badge status-accepted">' . htmlspecialchars($m['status']) . '</span>';
                                        } elseif ($m['status'] === 'invited') {
                                            echo '<span class="status-badge status-invited">' . htmlspecialchars($m['status']) . '</span>';
                                        } else {
                                            echo '<span class="status-badge status-other">' . htmlspecialchars($m['status']) . '</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="no-members-container">
            <i class="bi bi-people-slash"></i>
            <p class="empty-message">No members in this group yet.</p>
        </div>
    <?php endif; ?>
    
    <div class="button-group">
        <a href="group.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to My Groups</a>
    </div>
</div>
</body>
</html>