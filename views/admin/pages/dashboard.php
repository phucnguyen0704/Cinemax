<section class="dashboard">
    <header class="admin-header">
        <h1>Dashboard</h1>
        <div class="header-actions">
            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">

        <!-- STATS -->
        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(229, 9, 20, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Doanh thu hôm nay</span>
                    <span class="stat-value" style="color: #e50914;">0 ₫</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(70, 211, 105, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Vé đã bán</span>
                    <span class="stat-value" style="color: #46d369;">0</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 165, 0, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Người dùng mới</span>
                    <span class="stat-value" style="color: #ffa500;">0</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect>
                        <polyline points="17 2 12 7 7 2"></polyline>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Phim đang chiếu</span>
                    <span class="stat-value" style="color: #3b82f6;">0</span>
                </div>
            </div>

        </div>

        <!-- TABLE + TOP MOVIES -->
        <div class="dashboard-grid-2" style="margin-top: 30px;">

            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Đơn hàng mới nhất</h3>
                    <a href="bookings.php" class="card-action">Xem tất cả →</a>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#0001</td>
                                <td>Khách hàng</td>
                                <td style="color: var(--primary-color); font-weight: bold;">0 ₫</td>
                                <td>Đã thanh toán</td>
                                <td style="color: #888; font-size: 13px;">00:00 01/01</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Top phim bán chạy</h3>
                </div>

                <div class="top-movies-list">
                    <div class="top-movie-item">
                        <img src="../../assets/images/no-poster.jpg" class="tm-poster">
                        <div class="tm-info">
                            <h4>Tên phim</h4>
                            <div class="tm-stats">
                                <span class="tm-highlight">0</span> vé đã bán
                                <br>
                                Doanh thu: <span style="color: #fff;">0 ₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>