<?php
session_start();

// Check authentication
if (!isset($_SESSION['authenticated'])) {
    header("Location: admin.php");
    exit();
}

// Path to bookings JSON file
$bookingsFile = 'bookings.json';

// Function to read bookings from JSON
function getBookings() {
    global $bookingsFile;
    if (!file_exists($bookingsFile)) {
        return [];
    }
    $data = file_get_contents($bookingsFile);
    return json_decode($data, true) ?: [];
}

// Function to save bookings to JSON
function saveBookings($bookings) {
    global $bookingsFile;
    file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));
}

// Handle booking deletion
if (isset($_GET['delete'])) {
    $deleteIndex = (int)$_GET['delete'];
    $bookings = getBookings();
    
    if (isset($bookings[$deleteIndex])) {
        array_splice($bookings, $deleteIndex, 1);
        saveBookings($bookings);
    }
    
    header("Location: manage_bookings.php");
    exit();
}

$bookings = getBookings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Bookings</title>
    <link rel="stylesheet" href="css/adminstyle.css">
</head>
<body>
    <header class="main-header">
        <div class="brand">
            <span class="brand-name">CinemaBooking</span>
            <span class="separator">|</span>
            <span class="admin-panel">Admin Panel</span>
        </div>

        <div class="tabs">
            <a href="admin.php" class="tab-link">Movies</a>
            <a href="manage_bookings.php" class="tab-link active">Bookings</a>
        </div>

        <div>
            <nav>
                <a href="index.php">Home</a>
                <a href="admin.php?logout=true">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <h2 class="form-title-manage-bookings">Manage Bookings</h2>
        <section class="admin-subsection">
            <table class="admin-table">
                <tr>
                    <th>Booking ID</th>
                    <th>Movie Title</th>
                    <th>Showtime</th>
                    <th>Tickets</th>
                    <th>Timestamp</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($bookings as $index => $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($index + 1) ?></td>
                    <td><?= htmlspecialchars($booking['movie']) ?></td>
                    <td><?= htmlspecialchars($booking['date'] . ' ' . $booking['time']) ?></td>
                    <td><?= htmlspecialchars($booking['tickets']) ?></td>
                    <td><?= htmlspecialchars($booking['timestamp']) ?></td>
                    <td><a href="?delete=<?= $index ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. Admin Panel.</p>
    </footer>
</body>
</html>
