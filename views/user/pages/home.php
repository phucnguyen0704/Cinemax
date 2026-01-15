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
            <a href="movies.php?filter=now-showing" class="view-all">
                Xem tất cả
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </a>
        </div>

        <div class="movie-grid" id="nowShowingMovies">
            <div class="movie-card">
                <div class="movie-poster">
                    <img src="https://via.placeholder.com/400x600" alt="Movie title">

                    <div class="movie-overlay">
                        <a href="index.php?page=movie_detail" class="overlay-btn btn-detail">Chi tiết</a>
                        <a href="index.php?page=showtimes" class="overlay-btn btn-buy-overlay">Đặt vé</a>
                    </div>
                </div>

                <div class="movie-info">
                    <h3>Tên phim</h3>
                    <div class="movie-meta">
                        <span class="duration">120 phút</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Phim sắp chiếu</h2>
            <a href="movies.php?filter=coming-soon" class="view-all">Xem tất cả</a>
        </div>

        <div class="movie-grid" id="comingSoonMovies">
            <div class="movie-card">
                <div class="movie-poster">
                    <img src="https://via.placeholder.com/400x600" alt="Movie title">

                    <div class="movie-overlay">
                        <a href="index.php?page=movie_detail" class="overlay-btn btn-detail">Chi tiết</a>
                    </div>
                </div>

                <div class="movie-info">
                    <h3>Tên phim</h3>
                    <div class="movie-meta">
                        <span class="duration">110 phút</span>
                    </div>
                    <button class="btn-book-ticket" disabled
                        style="background: var(--bg-tertiary); cursor: not-allowed; opacity: 0.7;">
                        Chưa mở bán
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Khuyến mãi hot</h2>
            <a href="promotions.php" class="view-all">Xem tất cả</a>
        </div>

        <div class="promo-grid">
            <div class="promo-card">
                <img src="https://via.placeholder.com/400x200?text=Promotion" alt="Promotion">
                <div class="promo-content">
                    <span class="promo-badge">Giảm 20%</span>
                    <h3>Mã: SALE20</h3>
                    <p>HSD: 31/12/2026</p>
                </div>
            </div>
        </div>
    </div>
</section>