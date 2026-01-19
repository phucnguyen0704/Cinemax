<section class="bookings">

    <header class="admin-header">
        <h1>Quản lý đơn hàng</h1>
        <div class="header-actions">
            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin Cinema</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">

        <!-- Thống kê -->
        <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 255, 255, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Hiển thị</span>
                    <span class="stat-value">120</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(70, 211, 105, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Đã thanh toán</span>
                    <span class="stat-value">85</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 165, 0, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Chờ thanh toán</span>
                    <span class="stat-value">25</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Đã hủy</span>
                    <span class="stat-value">10</span>
                </div>
            </div>

        </div>

        <!-- Bảng đơn hàng -->
        <div class="dashboard-card">

            <div class="card-header">
                <form class="search-form" style="width: 300px;">
                    <input type="text" placeholder="Nhập mã đơn, tên, email...">
                </form>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Vé</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td><strong>#BK001</strong></td>
                            <td>
                                <div style="font-weight:bold;">Nguyễn Văn A</div>
                                <small style="color:#888;">a@gmail.com</small>
                            </td>
                            <td style="color: var(--primary-color); font-weight: bold;">
                                180.000 ₫
                            </td>
                            <td>2 vé</td>
                            <td>
                                <span class="status-badge status-Paid">
                                    Đã thanh toán
                                </span>
                            </td>
                            <td>19:30 18/01/2026</td>
                            <td>
                                <span style="color:#555;font-size:12px;">-</span>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>#BK002</strong></td>
                            <td>
                                <div style="font-weight:bold;">Trần Thị B</div>
                                <small style="color:#888;">b@gmail.com</small>
                            </td>
                            <td style="color: var(--primary-color); font-weight: bold;">
                                270.000 ₫
                            </td>
                            <td>3 vé</td>
                            <td>
                                <span class="status-badge status-Pending">
                                    Chờ thanh toán
                                </span>
                            </td>
                            <td>20:15 18/01/2026</td>
                            <td>
                                <button class="action-btn btn-approve" title="Xác nhận thanh toán">✔</button>
                                <button class="action-btn btn-cancel" title="Hủy đơn">✖</button>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>#BK003</strong></td>
                            <td>
                                <div style="font-weight:bold;">Lê Văn C</div>
                                <small style="color:#888;">c@gmail.com</small>
                            </td>
                            <td style="color: var(--primary-color); font-weight: bold;">
                                90.000 ₫
                            </td>
                            <td>1 vé</td>
                            <td>
                                <span class="status-badge status-Cancelled">
                                    Đã hủy
                                </span>
                            </td>
                            <td>21:00 17/01/2026</td>
                            <td>
                                <span style="color:#555;font-size:12px;">-</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>