<?php
$movies = json_decode(file_get_contents('movies.json'), true);

foreach ($movies as $movie) {
    echo '<div class="movie-card">
            <img src="' . htmlspecialchars($movie['image']) . '" alt="' . htmlspecialchars($movie['title']) . ' Poster">
            <h3>' . htmlspecialchars($movie['title']) . '</h3>
            <p>' . htmlspecialchars($movie['genre']) . ' | ' . htmlspecialchars($movie['duration']) . '</p>
            <a href="booking.php?title=' . urlencode($movie['title']) . '">
                <button>Book Now</button>
            </a>
          </div>';
}
?>