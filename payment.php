<?php
session_start();
if (!isset($_SESSION['booking'])) {
    header("Location: booking.php");
    exit();
}

$booking = $_SESSION['booking'];
$numTickets = $booking['tickets'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Payment Details</title>
    <link rel="stylesheet" href="css/paystyle.css">
</head>
<body>
    <header>
        <div class="logo">CinemaBooking</div>
    </header>

    <main class="payment-container">
        <h1>Enter Payment Details</h1>
        <p>Complete your payment for "<?php echo htmlspecialchars($booking['movie']); ?>" at <?php echo htmlspecialchars($booking['time']); ?> on <?php echo htmlspecialchars($booking['date']); ?>.</p>
        <h3>Total due: <?php echo '$' . (htmlspecialchars((int)$numTickets * 12)) . '.00'; ?></h3>
        <form action="process_payment.php" method="POST">
            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" pattern="\d{16}" maxlength="16" required placeholder="1234 5678 9012 3456"><br>

            <label for="expiry_date">Expiry Date:</label>
            <input type="month" id="expiry_date" name="expiry_date" required><br>

            <label for="cvv">CVV:</label>
            <input type="password" id="cvv" name="cvv" pattern="\d{3}" maxlength="3" required placeholder="123"><br>

            <button type="submit">Proceed</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. All Rights Reserved.</p>
    </footer>
</body>
</html>
