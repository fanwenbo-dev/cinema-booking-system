<?php
session_start();
if (!isset($_SESSION['booking']) || !isset($_SESSION['payment_step']) || $_SESSION['payment_step'] !== 'otp') {
    header("Location: booking.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <link rel="stylesheet" href="css/paystyle.css">
</head>
<body>
    <header>
        <div class="logo">CinemaBooking</div>
    </header>

    <main class="payment-container">
        <h1>Enter OTP</h1>
        <p>A one-time password (OTP) has been sent to your registered mobile number.</p>

        <form action="verify_otp.php" method="POST">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" pattern="\d{5}" maxlength="5" required placeholder="12345">
            <button type="submit">Submit</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. All Rights Reserved.</p>
    </footer>
</body>
</html>
