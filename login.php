<?php
session_start();

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    // Check if form fields are set
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validate input (prevent empty fields)
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Load the JSON file
        $membersFile = 'members.json';
        if (!file_exists($membersFile) || filesize($membersFile) === 0) {
            $members = ["members" => []];
        } else {
            $members = json_decode(file_get_contents($membersFile), true);
        }

        // Search for the username in members list
        foreach ($members['members'] as $member) {
            if ($member['username'] === $username && password_verify($password, $member['password'])) {
                $_SESSION['username'] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');  // Store sanitized username in session
                header('Location: index.php');  // Redirect to homepage
                exit;
            }
        }
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cinema Booking</title>
    <link rel="stylesheet" href="css/loginstyle.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php" style="text-decoration: none; color: inherit;">CinemaBooking</a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="login-container">
            <h1>Login</h1>
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="username">Username:</label>
                <input type="text" name="username" required>
                
                <label for="password">Password:</label>
                <input type="password" name="password" required>
                
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. All Rights Reserved.</p>
    </footer>
</body>
</html>
