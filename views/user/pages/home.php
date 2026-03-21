<?php
$allPromotions = isset($promotionService) ? $promotionService->listPromotions() : [];

// Chỉ lấy khuyến mãi đang áp dụng
$activePromotions = array_values(array_filter($allPromotions, function ($promo) {
    return ($promo['computed_status'] ?? '') === 'active';
}));

if (!isset($movieService)) {
    die('MovieService chưa được khởi tạo.');
}

$nowShowingMovies = $movieService->listMoviesForUser('now-showing');
$comingSoonMovies = $movieService->listMoviesForUser('coming-soon');

function buildPosterUrlHome($posterUrl)
{
    $posterUrl = trim((string)$posterUrl);

    if ($posterUrl === '') {
        return '/Cinemax/public/assets/uploads/movies/no-image.png';
    }

    if (preg_match('/^https?:\/\//i', $posterUrl)) {
        return $posterUrl;
    }

    $posterUrl = ltrim($posterUrl, '/');

    if (strpos($posterUrl, 'public/') === 0) {
        $posterUrl = substr($posterUrl, 7);
    }

    return '/Cinemax/public/' . $posterUrl;
}

function formatPromotionDateVNHome($date)
{
    if (!$date) return 'Chưa cập nhật';
    return date('d/m/Y', strtotime($date));
}

function getPromotionBadgeTextHome($promo)
{
    $type = $promo['discount_type'] ?? 'percent';
    $value = (float)($promo['discount_value'] ?? 0);

    if ($type === 'percent') {
        return 'Giảm ' . rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') . '%';
    }

    return 'Giảm ' . number_format($value, 0, ',', '.') . 'đ';
}
?>

<section class="home hero">
    <div class="hero-slider">
        <div class="hero-slide active"
            style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)),
             url('https://images.pexels.com/photos/7991579/pexels-photo-7991579.jpeg?auto=compress&cs=tinysrgb&w=1920') center/cover;">
            <div class="container">
                <div class="hero-content" style="padding: 60px;">
                    <h1>Trải nghiệm điện ảnh đỉnh cao</h1>
                    <p>Đặt vé online nhanh chóng - Nhận ưu đãi hấp dẫn</p>
                    <div class="hero-buttons">
                        <a href="#now-showing" class="btn-hero-primary">Đặt vé ngay</a>
                        <a href="index.php?page=movies" class="btn-hero-secondary">Xem tất cả phim</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>
</section>

<section class="quick-booking"></section>

<section id="now-showing" class="section">
    <div class="container">
        <div class="section-header">
            <h2>Phim đang chiếu</h2>
            <a href="index.php?page=movies&filter=now-showing" class="view-all">
                Xem tất cả
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </a>
        </div>

        <div class="movie-grid" id="nowShowingMovies">
            <?php if (empty($nowShowingMovies)): ?>
                <p>Hiện chưa có phim đang chiếu.</p>
            <?php else: ?>
                <?php foreach (array_slice($nowShowingMovies, 0, 8) as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <img
                                src="<?= htmlspecialchars(buildPosterUrlHome($movie['poster_url'] ?? '')) ?>"
                                alt="<?= htmlspecialchars($movie['title'] ?? 'Movie title') ?>"
                                onerror="this.src='/Cinemax/public/assets/uploads/movies/no-image.png'">

                            <div class="movie-overlay">
                                <a href="index.php?page=movie_detail&id=<?= (int)$movie['movie_id'] ?>" class="overlay-btn btn-detail">
                                    Chi tiết
                                </a>
                                <a href="index.php?page=movie_detail&id=<?= (int)$movie['movie_id'] ?>#booking" class="overlay-btn btn-buy-overlay">
                                    Đặt vé
                                </a>
                            </div>
                        </div>

                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['title'] ?? 'Tên phim') ?></h3>
                            <div class="movie-meta">
                                <span class="duration"><?= (int)($movie['duration_min'] ?? 0) ?> phút</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Phim sắp chiếu</h2>
            <a href="index.php?page=movies&filter=coming-soon" class="view-all">
                Xem tất cả
            </a>
        </div>

        <div class="movie-grid" id="comingSoonMovies">
            <?php if (empty($comingSoonMovies)): ?>
                <p>Hiện chưa có phim sắp chiếu.</p>
            <?php else: ?>
                <?php foreach (array_slice($comingSoonMovies, 0, 8) as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <img
                                src="<?= htmlspecialchars(buildPosterUrlHome($movie['poster_url'] ?? '')) ?>"
                                alt="<?= htmlspecialchars($movie['title'] ?? 'Movie title') ?>"
                                onerror="this.src='/Cinemax/public/assets/uploads/movies/no-image.png'">

                            <div class="movie-overlay">
                                <a href="index.php?page=movie_detail&id=<?= (int)$movie['movie_id'] ?>" class="overlay-btn btn-detail">
                                    Chi tiết
                                </a>
                            </div>
                        </div>

                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['title'] ?? 'Tên phim') ?></h3>
                            <div class="movie-meta">
                                <span class="duration"><?= (int)($movie['duration_min'] ?? 0) ?> phút</span>
                            </div>
                            <button class="btn-book-ticket" disabled
                                style="background: var(--bg-tertiary); cursor: not-allowed; opacity: 0.7;">
                                Chưa mở bán
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Khuyến mãi hot</h2>
            <a href="index.php?page=promotions" class="view-all">Xem tất cả</a>
        </div>

        <div class="promo-grid">
            <?php if (empty($activePromotions)): ?>
                <p>Hiện chưa có khuyến mãi hot.</p>
            <?php else: ?>
                <?php foreach (array_slice($activePromotions, 0, 4) as $promo): ?>
                    <div class="promo-card">
                        <img
                            src="https://via.placeholder.com/400x200?text=Promotion"
                            alt="<?= htmlspecialchars($promo['code'] ?? 'Promotion') ?>">

                        <div class="promo-content">
                            <span class="promo-badge">
                                <?= htmlspecialchars(getPromotionBadgeTextHome($promo)) ?>
                            </span>

                            <h3>Mã: <?= htmlspecialchars($promo['code'] ?? '') ?></h3>

                            <p>
                                HSD: <?= htmlspecialchars(formatPromotionDateVNHome($promo['end_date'] ?? null)) ?>
                            </p>

                            <?php if (!empty($promo['name'])): ?>
                                <p><?= htmlspecialchars($promo['name']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>