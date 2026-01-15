<nav class="navbar">
    <div class="container">
        <div class="nav-content">
            <a href="index.php" class="logo" style="text-decoration: none;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8a4 4 0 0 1 0 8v4a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-4a4 4 0 0 1 0-8"></path>
                    <path d="M10 8V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v4"></path>
                    <path d="M6 8v-2a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"></path>
                    <path d="M18 8v-2a2 2 0 0 0-2-2h0a2 2 0 0 0-2 2v2"></path>
                    <line x1="12" y1="14" x2="12" y2="22"></line>
                    <line x1="8" y1="15" x2="9" y2="22"></line>
                    <line x1="16" y1="15" x2="15" y2="22"></line>
                </svg>
                <span>Cinemax</span>
            </a>

            <div class="nav-links">
                <a href="index.php">Trang chủ</a>
                <a href="index.php?page=movies">Phim</a>
                <a href="index.php?page=theaters">Rạp chiếu</a>
                <a href="index.php?page=promotions">Khuyến mãi</a>
            </div>

            <div class="nav-actions">
                <form action="index.php?page=search_results" method="GET" class="search-box">
                    <input type="text" placeholder="Tìm phim..." id="searchInput" name="query" required>
                </form>

                <a href="../login.php" class="btn-login">Đăng nhập</a>
                <a href="../register.php" class="btn-primary">Đăng ký</a>
            </div>
        </div>
    </div>
</nav>