<?php
session_start();

// Hardcoded credentials
$USERNAME = 'admin';
$PASSWORD = 'securepassword123'; // Change this to a strong password

// Handle login
if (isset($_POST['login'])) {
    $input_user = $_POST['username'];
    $input_pass = $_POST['password'];

    if ($input_user === $USERNAME && $input_pass === $PASSWORD) {
        $_SESSION['authenticated'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Check authentication
if (!isset($_SESSION['authenticated'])): ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/adminstyle.css">
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>

<?php exit(); endif; ?>

<?php
// Load existing movies
$movies = json_decode(file_get_contents('movies.json'), true);

// Load summaries
$summariesFile = 'cache\/summaries.json';
$summaries = file_exists($summariesFile) ? json_decode(file_get_contents($summariesFile), true) : [];

// Add new movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    $target_dir = "assets/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $showtimes = json_decode($_POST['showtimes'], true);

            $new_movie = [
                "title" => $_POST['title'],
                "genre" => $_POST['genre'],
                "duration" => $_POST['duration'],
                "synopsis" => $_POST['synopsis'],
                "slots" => $_POST['slots'],
                "image" => $target_file,
                "showtimes" => $showtimes
            ];
            $movies[] = $new_movie;
            file_put_contents('movies.json', json_encode($movies, JSON_PRETTY_PRINT));
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "File is not an image.";
    }

    header("Location: admin.php");
    exit();
}

// Delete movie and its summary
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    if (isset($movies[$index])) {
        $title = $movies[$index]['title']; // Get movie title

        // Remove associated image
        if (isset($movies[$index]['image']) && file_exists($movies[$index]['image'])) {
            unlink($movies[$index]['image']);
        }

        // Remove the movie from the array
        array_splice($movies, $index, 1);
        file_put_contents('movies.json', json_encode($movies, JSON_PRETTY_PRINT));

        // Remove movie summary if it exists
        if (isset($summaries[$title])) {
            unset($summaries[$title]);
            file_put_contents($summariesFile, json_encode($summaries, JSON_PRETTY_PRINT));
        }
    }

    header("Location: admin.php");
    exit();
}

// Clear all summaries
if (isset($_POST['clear_summaries'])) {
    file_put_contents($summariesFile, json_encode([], JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Movies</title>
    <link rel="stylesheet" href="css/adminstyle.css?v=1">
    <script>
        function addShowtime() {
            const dateInput = document.getElementById('showtime_date');
            const timeInput = document.getElementById('showtime_time');
            const showtimesList = document.getElementById('showtimes_list');
            const hiddenInput = document.getElementById('showtimes_hidden');

            const date = dateInput.value;
            const time = timeInput.value;

            if (date && time) {
                let showtimes = hiddenInput.value ? JSON.parse(hiddenInput.value) : {};

                if (!showtimes[date]) {
                    showtimes[date] = [];
                }

                showtimes[date].push(time);
                hiddenInput.value = JSON.stringify(showtimes);

                const listItem = document.createElement('li');
                listItem.textContent = `${date} - ${time}`;
                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Delete';
                deleteBtn.onclick = function() {
                    showtimes[date] = showtimes[date].filter(t => t !== time);
                    if (showtimes[date].length === 0) delete showtimes[date];
                    hiddenInput.value = JSON.stringify(showtimes);
                    listItem.remove();
                };
                listItem.appendChild(deleteBtn);
                showtimesList.appendChild(listItem);

                dateInput.value = '';
                timeInput.value = '';
            }
        }
    </script>
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
            <a href="manage_bookings.php" class="tab-link">Bookings</a>
        </div>

        <div>
            <nav>
                <a href="index.php">Home</a>
                <a href="admin.php?logout=true">Logout</a>
            </nav>
        </div>
        
    </header>

    <main>
        <div class="admin-sections-container">
            <!-- Add New Movies Section -->
            <section class="admin-subsection">
                <h2 class="form-title">Add New Movie</h2>
                <form method="POST" class="admin-form" enctype="multipart/form-data">
                    <input type="text" name="title" placeholder="Movie Title" required>
                    <input type="text" name="genre" placeholder="Genre (e.g., Action)" required>
                    <input type="text" name="duration" placeholder="Duration (e.g., 2h 10m)" required>
                    <input type="text" name="synopsis" placeholder="Synopsis" required>
                    <input type="text" name="slots" placeholder="Slots" required>
                    Movie image: <input type="file" name="image" accept="image/*" required><br>

                    <h3>Showtimes</h3>
                    <input type="date" id="showtime_date">
                    <input type="time" id="showtime_time">
                    <button type="button" onclick="addShowtime()">Add Showtime</button>
                    <ul id="showtimes_list"></ul>
                    <input type="hidden" name="showtimes" id="showtimes_hidden">

                    <button type="submit" name="add_movie">Add Movie</button>
                </form>
            </section>
        </div>

        <!-- Currently Showing Movies Section -->
        <section class="admin-section">
            <h2>Currently Showing Movies</h2>
            <table class="admin-table">
                <tr>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($movies as $index => $movie): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="admin-movie-poster"></td>
                        <td><?= htmlspecialchars($movie['title']) ?></td>
                        <td><?= htmlspecialchars($movie['genre']) ?></td>
                        <td><?= htmlspecialchars($movie['duration']) ?></td>
                        <td>
                            <a href="admin.php?delete=<?= $index ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this movie?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <!-- Clear All Summaries Button -->
                <form method="POST">
                    <button type="submit" name="clear_summaries" class="clear-btn" onclick="return confirm('Are you sure you want to clear all summaries?');">
                        Clear All Summaries
                    </button>
                </form>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 CinemaBooking. Admin Panel.</p>
    </footer>
</body>
</html>
