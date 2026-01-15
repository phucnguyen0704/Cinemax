<section class="movies">
    <main class="section">
        <div class="container">
            <div class="section-header">
                <h2>Tất cả phim</h2>
                <div class="filter-tabs">
                    <a href="movies.php?filter=all" class="active btn-filter">Tất cả</a>
                    <a href="movies.php?filter=now-showing" class="btn-filter">Đang chiếu</a>
                    <a href="movies.php?filter=coming-soon" class="btn-filter">Sắp chiếu</a>
                </div>
            </div>

            <div class="movie-grid">

                <!-- Movie card -->
                <div class="movie-card">
                    <div class="movie-poster">
                        <img src="" alt="Tên phim">

                        <div class="movie-overlay">
                            <a href="index.php?page=movie_detail" class="overlay-btn btn-detail">
                                Chi tiết
                            </a>
                        </div>
                    </div>

                    <div class="movie-info">
                        <h3>Tên phim</h3>
                        <div class="movie-meta">
                            <span class="duration">120 phút</span>
                        </div>

                        <a href="showtimes.php?movie_id=1" class="btn-book-ticket">
                            Đặt vé
                        </a>
                        <!--
                        <button class="btn-book-ticket" disabled
                                style="background: var(--bg-tertiary); cursor: not-allowed;">
                            Chưa mở bán
                        </button>
                        -->
                    </div>
                </div>
                <!-- End movie card -->

            </div>
        </div>
    </main>
</section>