<?php
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Logout - TripPlanner</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: url('https://images.unsplash.com/photo-1465156799763-2c087c332922?auto=format&fit=crop&w=1500&q=80') center center/cover no-repeat,
                        linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            position: relative;
        }
        .logout-frame {
            max-width: 400px;
            margin: 80px auto;
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
            padding: 32px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        h2 {
            color: #185a9d;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            border: none;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(116, 235, 213, 0.15);
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="logout-frame">
        <h2>Logged Out</h2>
        <p class="mb-4">You have been successfully logged out.<br>Thank you for using TripPlanner!</p>
        <a href="login.php" class="btn btn-primary">Login Again</a>
    </div>
</body>
</html>