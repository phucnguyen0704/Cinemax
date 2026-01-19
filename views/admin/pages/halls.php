<section class="halls">

    <header class="admin-header">
        <h1>Quản lý Phòng chiếu</h1>
        <button class="btn-add" onclick="openModal('addScreenModal')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Thêm phòng mới</span>
        </button>
    </header>

    <div class="dashboard-content">
        <!-- Alert mẫu -->
        <!-- <div class="alert alert-success">Thao tác thành công!</div> -->
        <!-- <div class="alert alert-error">Có lỗi xảy ra</div> -->

        <div class="dashboard-card">
            <form method="GET" action="screens.php" class="filter-bar">
                <select name="theater_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả các rạp --</option>
                    <option value="1">Rạp CGV Vincom</option>
                    <option value="2">Rạp Galaxy Nguyễn Du</option>
                </select>
                <a href="screens.php" class="btn-action">Xóa lọc</a>
            </form>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Rạp</th>
                            <th>Tên Phòng</th>
                            <th>Sức chứa</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1</td>
                            <td>Rạp CGV Vincom</td>
                            <td><strong>Phòng 1</strong></td>
                            <td>120 ghế</td>
                            <td>
                                <a href="index.php?page=seats" class="btn-action" style="color: #46d369; border-color: #46d369;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <path d="M3 9h18"></path>
                                        <path d="M9 21V9"></path>
                                    </svg>
                                    Cấu hình ghế
                                </a>

                                <a href="screens.php?action=edit&id=1" class="btn-action">Sửa</a>

                                <form action="../../Handle/screens_process.php" method="POST" style="display:inline;"
                                    onsubmit="return confirm('CẢNH BÁO: Xóa phòng sẽ xóa luôn sơ đồ ghế của phòng này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="screen_id" value="1">
                                    <button class="btn-action danger">Xóa</button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>#2</td>
                            <td>Rạp Galaxy Nguyễn Du</td>
                            <td><strong>IMAX</strong></td>
                            <td>200 ghế</td>
                            <td>
                                <a href="#" class="btn-action">Sửa</a>
                                <button class="btn-action danger">Xóa</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addScreenModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm phòng chiếu</h2>
                <button class="btn-close" onclick="closeModal('addScreenModal')">&times;</button>
            </div>
            <form action="../../Handle/screens_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn Rạp</label>
                        <select name="theater_id" required>
                            <option value="">-- Chọn rạp --</option>
                            <option value="1">Rạp CGV Vincom</option>
                            <option value="2">Rạp Galaxy Nguyễn Du</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tên phòng</label>
                        <input type="text" name="name" placeholder="VD: Phòng 1, Phòng IMAX" required>
                    </div>
                    <div class="form-group">
                        <label>Sức chứa</label>
                        <input type="number" name="capacity" value="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addScreenModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</section>