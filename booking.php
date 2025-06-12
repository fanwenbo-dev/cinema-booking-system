<?php
session_start();
require 'openai.php';

$movies = json_decode(file_get_contents('movies.json'), true);
$selectedMovie = null;
$summary = "No summary available.";

if (isset($_GET['title'])) {
    foreach ($movies as $movie) {
        if ($movie['title'] === $_GET['title']) {
            $selectedMovie = $movie;
            break;
        }
    }
}

// Fetch AI summary only if the movie is found
if ($selectedMovie) {
    $summary = getMovieSummary($selectedMovie['title'], $selectedMovie['synopsis']);
}

// Handle booking before output starts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    if ($selectedMovie) {
        $selectedDate = $_POST['date'];
        $selectedTime = $_POST['time'];
        $numTickets = intval($_POST['tickets']);

        if (!isset($selectedMovie['showtimes'][$selectedDate]) || !in_array($selectedTime, $selectedMovie['showtimes'][$selectedDate])) {
            $error = "Invalid date or time selected.";
        } else {
            $dates = array_keys($selectedMovie['showtimes']);
            $times = $selectedMovie['showtimes'][$selectedDate];
            $dateIndex = array_search($selectedDate, $dates);
            $timeIndex = array_search($selectedTime, $times);
            $slotIndex = ($dateIndex * count($times)) + $timeIndex;

            $slots = explode(",", $selectedMovie['slots']);
            if ($slots[$slotIndex] >= $numTickets) {
                $_SESSION['booking'] = [
                    'movie' => $selectedMovie['title'],
                    'date' => $selectedDate,
                    'time' => $selectedTime,
                    'tickets' => $numTickets,
                    'slotIndex' => $slotIndex
                ];
                header("Location: payment.php");
                exit();
            } else {
                $error = "Not enough tickets available.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Movie</title>
    <link rel="stylesheet" href="css/bookstyle.css">
</head>
<body>
<header>
    <div class="logo">
        <a href="index.php" style="text-decoration: none; color: inherit;">CinemaBooking</a>
    </div>
</header>

<main>
    <?php if ($selectedMovie): ?>
        <div class="booking-container">
            <div class="booking-poster">
                <img src="<?php echo htmlspecialchars($selectedMovie['image']); ?>" alt="Movie Poster">
            </div>
            <div class="booking-details">
                <h1><?php echo htmlspecialchars($selectedMovie['title']); ?></h1>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($selectedMovie['duration']); ?></p>
                <p><?php echo htmlspecialchars($selectedMovie['synopsis']); ?></p>
                <p><strong>Summary of previous titles, if any: </strong><?php echo htmlspecialchars($summary); ?></p>
                <div style="font-size: 12px;">Summary powered by AI. May provide incorrect information.</div>

                <!-- Booking Form -->
                <form method="POST" class="booking-form">
                    <label for="date">Select Date:</label>
                    <select name="date" id="date" required>
                        <option value="">Choose a date</option>
                        <?php foreach ($selectedMovie['showtimes'] as $date => $times): ?>
                            <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="time">Select Time:</label>
                    <select name="time" id="time" required>
                        <option value="">Choose a time</option>
                    </select>

                    <label for="tickets">Number of Tickets ($12 per ticket):</label>
                    <select name="tickets" id="tickets" required>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>

                    <button type="submit" name="book">Book Tickets</button>
                </form>

                <?php if (isset($error)): ?>
                    <p class="availability-message" style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p>Movie not found.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 CinemaBooking. All Rights Reserved.</p>
</footer>

<script>
    const dateSelect = document.getElementById('date');
    const timeSelect = document.getElementById('time');
    const showtimes = <?php echo json_encode($selectedMovie ? $selectedMovie['showtimes'] : []); ?>;

    dateSelect.addEventListener('change', function() {
        const selectedDate = this.value;
        timeSelect.innerHTML = '<option value="">Choose a time</option>';

        if (showtimes[selectedDate]) {
            showtimes[selectedDate].forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time;
                timeSelect.appendChild(option);
            });
        }
    });
</script>
</body>
</html>
