<?php

$jsonFile = 'members.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $phone_number = trim($_POST['phone_number']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Read existing data
    $data = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : ["members" => []];

    // Check if username exists
    foreach ($data['members'] as $member) {
        if ($member['username'] === $username) {
            die("Username already taken!");
        }
    }

    // New member entry
    $newMember = [
        "name" => $name,
        "age" => $age,
        "phone_number" => $phone_number,
        "username" => $username,
        "password" => $hashed_password,
        "points" => 100
    ];

    // Append new member
    $data['members'][] = $newMember;

    // Save back to JSON file
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

    echo "Signup successful! <script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cinema Booking</title>
    <link rel="stylesheet" href="css/signupstyle.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php" style="text-decoration: none; color: inherit;">CinemaBooking</a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="signup-container">
            <h1>Sign Up</h1>
            <form method="POST" action="">
                <label for="name">Name:</label>
                <input type="text" name="name" required>
                
                <label for="age">Age:</label>
                <input type="number" name="age" required>
                
                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" required>
                
                <label for="username">Username:</label>
                <input type="text" name="username" required>
                
                <label for="password">Password:</label>
                <input type="password" name="password" required>
                
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" required>
                
                <button type="submit">Sign Up</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. All Rights Reserved.</p>
    </footer>
</body>
</html>