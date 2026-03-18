<?php
if (!isset($movieService)) {
    die('MovieService chưa được khởi tạo.');
}

$movieId = $_GET['id'] ?? null;

if (!$movieId || !is_numeric($movieId)) {
    echo '<div class="container" style="padding:40px 0;"><p>Phim không hợp lệ.</p></div>';
    return;
}

$movie = $movieService->getMovieDetailForUser((int)$movieId);

if (!$movie || !in_array((int)$movie['status'], [1, 2], true)) {
    echo '<div class="container" style="padding:40px 0;"><p>Không tìm thấy phim.</p></div>';
    return;
}

function movieStatusTextDetail($status)
{
    return match ((int)$status) {
        1 => 'Sắp chiếu',
        2 => 'Đang chiếu',
        3 => 'Ngừng chiếu',
        default => 'Không xác định',
    };
}

function formatDateVN($date)
{
    if (!$date) return 'Chưa cập nhật';
    return date('d/m/Y', strtotime($date));
}

function buildPosterUrlDetail($posterUrl)
{
    $posterUrl = trim((string)$posterUrl);

    if ($posterUrl === '') {
        return '/webb/Cinemax/assets/posters/no-image.png';
    }

    if (preg_match('/^https?:\/\//i', $posterUrl)) {
        return $posterUrl;
    }

    return '/webb/Cinemax/' . ltrim($posterUrl, '/');
}

function buildYoutubeEmbedUrl($url)
{
    $url = trim((string)$url);
    if ($url === '') return null;

    $parts = parse_url($url);
    if (!$parts || empty($parts['host'])) {
        return null;
    }

    $host = strtolower($parts['host']);
    $path = $parts['path'] ?? '';
    $query = $parts['query'] ?? '';

    $videoId = null;

    if (strpos($host, 'youtu.be') !== false) {
        $videoId = trim($path, '/');
    } elseif (
        strpos($host, 'youtube.com') !== false ||
        strpos($host, 'www.youtube.com') !== false ||
        strpos($host, 'm.youtube.com') !== false
    ) {
        parse_str($query, $queryParams);

        if (!empty($queryParams['v'])) {
            $videoId = $queryParams['v'];
        } elseif (preg_match('~^/embed/([^/?&]+)~', $path, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('~^/shorts/([^/?&]+)~', $path, $matches)) {
            $videoId = $matches[1];
        }
    }

    if (!$videoId) {
        return null;
    }

    $videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId);

    if ($videoId === '') {
        return null;
    }

    return 'https://www.youtube.com/embed/' . $videoId;
}

$poster = buildPosterUrlDetail($movie['poster_url'] ?? '');
$trailerEmbed = buildYoutubeEmbedUrl($movie['trailer_url'] ?? '');
$genreNames = !empty($movie['genres']) ? implode(', ', array_column($movie['genres'], 'name')) : 'Chưa cập nhật';
?>

<section class="movie_deail">
    <div class="movie-detail-header"
        style="background-image: url('<?= htmlspecialchars($poster) ?>'); background-size: cover; background-position: center;">
        <div class="container">
            <div class="detail-content">
                <img src="<?= htmlspecialchars($poster) ?>"
                    alt="<?= htmlspecialchars($movie['title'] ?? 'Movie title') ?>"
                    class="detail-poster"
                    onerror="this.src='/webb/Cinemax/assets/posters/no-image.png'">

                <div class="detail-info">
                    <h1><?= htmlspecialchars($movie['title'] ?? 'Tên phim') ?></h1>

                    <div class="meta-tags">
                        <span class="tag <?= (int)$movie['status'] === 2 ? 'tag-primary' : '' ?>">
                            <?= htmlspecialchars(movieStatusTextDetail($movie['status'] ?? 0)) ?>
                        </span>
                        <span class="tag"><?= (int)($movie['duration_min'] ?? 0) ?> phút</span>
                        <span class="tag">Khởi chiếu: <?= htmlspecialchars(formatDateVN($movie['release_date'] ?? null)) ?></span>
                    </div>

                    <p><strong>Đạo diễn:</strong> <?= htmlspecialchars($movie['director'] ?? 'Chưa cập nhật') ?></p>
                    <p><strong>Diễn viên:</strong> <?= htmlspecialchars($movie['actors'] ?? 'Chưa cập nhật') ?></p>
                    <p><strong>Thể loại:</strong> <?= htmlspecialchars($genreNames) ?></p>

                    <div style="margin-top: 20px;">
                        <h3>Nội dung phim</h3>
                        <p style="line-height: 1.6; color: #ccc;">
                            <?= nl2br(htmlspecialchars($movie['description'] ?? 'Chưa có mô tả cho phim này.')) ?>
                        </p>
                    </div>

                    <?php if ($trailerEmbed): ?>
                        <div class="trailer-box">
                            <h3>Trailer</h3>
                            <iframe class="trailer-frame"
                                src="<?= htmlspecialchars($trailerEmbed) ?>"
                                allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="booking-section" id="booking">
        <div class="container">
            <div class="section-header">
                <h2>Lịch chiếu</h2>
            </div>

            <?php if ((int)$movie['status'] === 2): ?>
                <div style="text-align: center; padding: 40px; border: 1px dashed #444; border-radius: 8px;">
                    <p style="color: #aaa;">Phần lịch chiếu sẽ nối tiếp với bảng showtimes/shows sau.</p>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; border: 1px dashed #444; border-radius: 8px;">
                    <p style="color: #aaa;">Phim sắp chiếu, hiện chưa mở lịch chiếu.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</section>