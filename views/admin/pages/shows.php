<section class="shows">

    <header class="admin-header">
        <h1>Quản lý Suất chiếu</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="openModal('addShowtimeModal')">
                <span>+ Thêm suất chiếu</span>
            </button>
            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin Cinema</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">

        <!-- Alert mẫu -->
        <!-- <div class="alert alert-success">Thao tác thành công!</div> -->
        <!-- <div class="alert alert-error">Có lỗi xảy ra</div> -->

        <div class="filter-container">
            <form action="" method="GET" class="filter-form">

                <div class="form-group-filter">
                    <label>Ngày chiếu</label>
                    <input type="date" name="filter_date" class="dark-input">
                </div>

                <div class="form-group-filter">
                    <label>Phim</label>
                    <select name="filter_movie" class="dark-input">
                        <option value="">-- Tất cả phim --</option>
                        <option value="1">Avengers: Endgame</option>
                        <option value="2">The Nun</option>
                        <option value="3">Fast & Furious</option>
                    </select>
                </div>

                <div class="form-group-filter">
                    <label>Rạp chiếu</label>
                    <select name="filter_theater" class="dark-input">
                        <option value="">-- Tất cả rạp --</option>
                        <option value="1">CGV Vincom</option>
                        <option value="2">Lotte Cinema</option>
                    </select>
                </div>

                <button type="submit" class="btn-filter-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Lọc
                </button>

            </form>
        </div>

        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Rạp</th>
                            <th>Phòng</th>
                            <th>Thời gian</th>
                            <th>Giá vé</th>
                            <th>Ghế trống</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td><strong>Avengers: Endgame</strong></td>
                            <td>CGV Vincom</td>
                            <td>Phòng 1</td>
                            <td>
                                <div style="font-weight: bold;">20/01/2026</div>
                                <div style="font-size: 13px; color: var(--primary-color);">19:30</div>
                            </td>
                            <td>90.000 ₫</td>
                            <td>
                                <span style="color: #46d369; font-weight: bold;">
                                    120 / 150
                                </span>
                            </td>
                            <td>
                                <form action="../../Handle/showtimes_process.php"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Xóa suất chiếu này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="showtime_id" value="1">
                                    <button class="btn-action danger">Xóa</button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>The Nun</strong></td>
                            <td>Lotte Cinema</td>
                            <td>Phòng 2</td>
                            <td>
                                <div style="font-weight: bold;">21/01/2026</div>
                                <div style="font-size: 13px; color: var(--primary-color);">21:00</div>
                            </td>
                            <td>100.000 ₫</td>
                            <td>
                                <span style="color: #ff9800; font-weight: bold;">
                                    30 / 120
                                </span>
                            </td>
                            <td>
                                <form action="../../Handle/showtimes_process.php"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Xóa suất chiếu này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="showtime_id" value="2">
                                    <button class="btn-action danger">Xóa</button>
                                </form>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal thêm suất chiếu -->
    <div id="addShowtimeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm suất chiếu mới</h2>
                <button class="btn-close" onclick="closeModal('addShowtimeModal')">&times;</button>
            </div>

            <form action="../../Handle/showtimes_process.php" method="POST">
                <input type="hidden" name="action" value="add">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Chọn Rạp</label>
                        <select class="dark-input" required>
                            <option value="">-- Chọn rạp --</option>
                            <option value="1">CGV Vincom</option>
                            <option value="2">Lotte Cinema</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Phòng chiếu</label>
                        <select name="screen_id" class="dark-input" required>
                            <option value="1">Phòng 1</option>
                            <option value="2">Phòng 2</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Phim</label>
                        <select name="movie_id" class="dark-input" required>
                            <option value="1">Avengers: Endgame</option>
                            <option value="2">The Nun</option>
                            <option value="3">Fast & Furious</option>
                        </select>
                    </div>

                    <div class="form-group"
                         style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label>Ngày chiếu</label>
                            <input type="date" name="show_date" class="dark-input" required>
                        </div>
                        <div>
                            <label>Giờ chiếu</label>
                            <input type="time" name="show_time" class="dark-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Giá vé cơ bản (VNĐ)</label>
                        <input type="number"
                               name="price"
                               class="dark-input"
                               value="90000"
                               min="0"
                               step="1000"
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn-action"
                            onclick="closeModal('addShowtimeModal')">
                        Hủy
                    </button>
                    <button type="submit" class="btn-primary">
                        Thêm suất chiếu
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>
