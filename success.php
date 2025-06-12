<?php
session_start();
if (!isset($_SESSION['booking'])) {
    header("Location: booking.php");
    exit();
}

$movies = json_decode(file_get_contents('movies.json'), true);
$booking = $_SESSION['booking'];
$title = $booking['movie'];
$slotIndex = $booking['slotIndex'];
$numTickets = $booking['tickets'];

foreach ($movies as &$movie) {  // update slots
    if ($movie['title'] === $title) {
        $slots = explode(",", $movie['slots']);
        $slots[$slotIndex] -= $numTickets;
        $movie['slots'] = implode(",", $slots);
        file_put_contents('movies.json', json_encode($movies, JSON_PRETTY_PRINT));
        break;
    }
}

$bookingLog = [ // update log
    'movie' => $booking['movie'],
    'date' => $booking['date'],
    'time' => $booking['time'],
    'tickets' => $booking['tickets'],
    'timestamp' => date("Y-m-d H:i:s")
];

$logFile = 'bookings.json';
$bookings = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
$bookings[] = $bookingLog;
file_put_contents($logFile, json_encode($bookings, JSON_PRETTY_PRINT));

session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <link rel="stylesheet" href="css/successstyle.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php" style="text-decoration: none; color: inherit;">CinemaBooking</a>
        </div>
    </header>

    <main class="success-container">
        <h1>Booking Confirmed!</h1>
        <p>You have successfully booked <?php echo htmlspecialchars($numTickets); ?> ticket(s) for "<?php echo htmlspecialchars($booking['movie']); ?>" on <?php echo htmlspecialchars($booking['date']); ?> at <?php echo htmlspecialchars($booking['time']); ?>.</p>
        <a href="index.php" class="back-home">Back to Home</a>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. All Rights Reserved.</p>
    </footer>
</body>
</html>
