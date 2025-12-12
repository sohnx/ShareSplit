<?php
session_start();
if (!isset($_SESSION['user_name'])) exit('Not logged in');
require_once 'db_connect.php';

if (!isset($_GET['group_id']) || !is_numeric($_GET['group_id'])) exit('Invalid group ID.');

$group_id = intval($_GET['group_id']);

$stmt = $conn->prepare('SELECT group_name, creator, created_at FROM groups WHERE id = ?');
$stmt->bind_param('i', $group_id);
$stmt->execute();
$stmt->bind_result($group_name, $creator, $created_at);
if (!$stmt->fetch()) exit('Group not found.');
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
<style>
    .group-info-container {
        animation: fadeIn 0.7s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .group-info-container h4 {
        color: #153a6bff;
        font-weight: 700;
        letter-spacing: 0.03em;
        margin-bottom: 1.5rem;
        font-size: 1.6rem;
        border-left: 4px solid #153a6bff;
        padding-left: 1rem;
    }
    
    .group-info-container .info-item {
        margin-bottom: 0.8rem;
        font-size: 1.05rem;
        color: #2c3e50;
    }
    
    .group-info-container .info-item strong {
        color: #153a6bff;
        font-weight: 600;
    }
    
    .group-info-container h5 {
        color: #2c3e50;
        font-weight: 700;
        letter-spacing: 0.02em;
        margin-top: 1.8rem;
        margin-bottom: 1.2rem;
        font-size: 1.3rem;
    }
    
    .group-info-container .table-responsive {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .group-info-container .table {
        margin-bottom: 0;
        border-collapse: collapse;
    }
    
    .group-info-container .table thead {
        background: linear-gradient(90deg, #16a085 0%, #1abc9c 100%);
        color: #fff;
    }
    
    .group-info-container .table thead th {
        border: none;
        padding: 1rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        font-size: 0.95rem;
        text-transform: uppercase;
        vertical-align: middle;
    }
    
    .group-info-container .table tbody td {
        padding: 0.9rem 1rem;
        border: 1px solid #e0e0e0;
        background: #ffffff;
        color: #2c3e50;
        font-size: 0.95rem;
        vertical-align: middle;
    }
    
    .group-info-container .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .group-info-container .table tbody tr:hover {
        background: rgba(22, 160, 133, 0.05);
        transform: translateX(4px);
        box-shadow: inset 4px 0 0 #153a6bff;
    }
    
    .group-info-container .table tbody tr:last-child td {
        border-bottom: 1px solid #153a6bff;
    }
    
    .group-info-container .status-badge {
        display: inline-block;
        padding: 0.35rem 0.9rem;
        border-radius: 18px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: capitalize;
        letter-spacing: 0.01em;
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
    
    .group-info-container .table tbody td:first-child {
        font-weight: 600;
        color: #153a6bff            ;
    }
    
    .no-members-message {
        text-align: center;
        padding: 2rem;
        color: #34495e;
        font-size: 1.05rem;
        font-style: italic;
        background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
        border-radius: 12px;
        border: 1px solid #95a5a6;
    }
</style>

<div class="group-info-container">
    <h4>📍 <?php echo htmlspecialchars($group_name); ?></h4>
    <div class="info-item">
        <strong>👤 Created by:</strong> <?php echo htmlspecialchars($creator); ?>
    </div>
    <div class="info-item">
        <strong>📅 Created at:</strong> <?php echo htmlspecialchars($created_at); ?>
    </div>
    
    <h5>👥 Group Members</h5>
    <?php if (count($members) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
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
    <?php else: ?>
        <div class="no-members-message">
            No members in this group yet.
        </div>
    <?php endif; ?>
</div>

