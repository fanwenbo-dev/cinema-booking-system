<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if not authenticated
    exit;
}

$membersFile = 'members.json';

// Load user data
if (file_exists($membersFile) && filesize($membersFile) > 0) {
    $members = json_decode(file_get_contents($membersFile), true);
} else {
    die("Error: No user data found.");
}

$userData = null;

// Find the logged-in user's details
foreach ($members['members'] as $member) {
    if ($member['username'] === $_SESSION['username']) {
        $userData = $member;
        break;
    }
}

// If user data is not found
if (!$userData) {
    die("Error: User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #f9f9f9;
        }
        .points {
            font-size: 2rem;
            font-weight: bold;
            color: #ff9800;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($userData['name'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Age:</strong> <?php echo (int)$userData['age']; ?></p>
    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($userData['phone_number'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p class="points">Total Points: <?php echo (int)$userData['points']; ?></p>
    <a href="logout.php">Logout</a>
</div>

</body>
</html>
