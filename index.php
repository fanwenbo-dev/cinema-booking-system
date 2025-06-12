<?php session_start(); ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php" style="text-decoration: none; color: inherit;">CinemaBooking</a>
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="profile.php">
                <img src="assets\/Screenshot2025-02-06015006.png" alt="Profile" style="width: 20px; height: 20px;"> 
                Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a></li>
                <?php else: ?>
                <li><a href="login.php">Member Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>

    </header>

    <main>
        <section class="hero">
            <h1>Book Your Favorite Movies Online</h1>
            <p>Easy, Fast, and Secure Booking</p>
        </section>

        <section class="movies" id="movie-list">
            <?php include 'storemovie.php'; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. All Rights Reserved.</p>
    </footer>
</body>
</html>