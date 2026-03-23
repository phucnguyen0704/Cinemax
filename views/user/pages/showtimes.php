<?php
$movie = null;
$movieId = $_GET['movie_id'] ?? ($_GET['id'] ?? null);

if ($movieId !== null && ctype_digit((string)$movieId)) {
    $movie = $movieService->getMovieDetailForUser((int)$movieId);
}

function buildPosterUrl($posterUrl)
{
    $posterUrl = trim((string)$posterUrl);

    if ($posterUrl === '') {
        return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='300' height='450' viewBox='0 0 300 450'><rect width='300' height='450' fill='%23111111'/><rect x='15' y='15' width='270' height='420' rx='12' fill='%231b1b1b' stroke='%23333333'/><text x='50%25' y='48%25' fill='%23999999' font-size='22' font-family='Arial, sans-serif' text-anchor='middle'>No Image</text><text x='50%25' y='55%25' fill='%23666666' font-size='14' font-family='Arial, sans-serif' text-anchor='middle'>Cinemax</text></svg>";
    }

    if (preg_match('/^https?:\/\//i', $posterUrl) || strpos($posterUrl, 'data:image/') === 0) {
        return $posterUrl;
    }

    if (strpos($posterUrl, '/Cinemax/') === 0) {
        return $posterUrl;
    }

    $posterUrl = ltrim($posterUrl, '/');
    if (strpos($posterUrl, 'public/') === 0) {
        return '/Cinemax/' . $posterUrl;
    }
    if (strpos($posterUrl, 'assets/') === 0) {
        return '/Cinemax/public/' . $posterUrl;
    }
    if (strpos($posterUrl, 'uploads/') === 0) {
        return '/Cinemax/public/assets/' . $posterUrl;
    }

    return '/Cinemax/public/assets/uploads/movies/' . $posterUrl;
}

$posterUrl = buildPosterUrl($movie['poster_url'] ?? '');

$duration = isset($movie['duration_min']) ? (string)$movie['duration_min'] : '--';
$director = !empty($movie['director']) ? (string)$movie['director'] : 'Đang cập nhật';
$movieTitle = !empty($movie['title']) ? (string)$movie['title'] : 'Lịch chiếu';
?>


<section class="showtimes">


    <div class="movie-header-bg">
        <div class="container">
            <div class="movie-header-content">
                <img id="showtimesPoster" src="<?= htmlspecialchars($posterUrl) ?>" class="header-poster">
                <div class="movie-header-info">
                    <h1 id="showtimesMovieTitle"><?= htmlspecialchars($movieTitle) ?></h1>
                    <p>Thời lượng: <span id="showtimesDuration"><?= htmlspecialchars($duration) ?></span> phút</p>
                    <p>Đạo diễn: <span id="showtimesDirector"><?= htmlspecialchars($director) ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <main class="section">
        <div class="container" id="showtimesContent">
            <div style="text-align: center; padding: 40px; color: #888;">
                <div class="loading">Đang tải lịch chiếu...</div>
            </div>
        </div>
    </main>

</section>

<script src="/Cinemax/public/assets/js/api.js"></script>
<script src="/Cinemax/public/assets/js/user-showtimes.js"></script>