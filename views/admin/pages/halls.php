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
            <form method="GET" class="filter-bar" id="filterForm">
                <select name="theater_id" class="filter-select" id="cinemaFilter">
                    <option value="">-- Tất cả các rạp --</option>
                </select>
                <button type="button" class="btn-action" onclick="filterHallsByCinema(null)">Xóa lọc</button>
            </form>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Rạp</th>
                            <th>Tên Phòng</th>
                            <th>Trạng thái</th>
                            <th>Sức chứa</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">
                                <div class="loading">Đang tải dữ liệu...</div>
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
            <form id="addHallForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn Rạp</label>
                        <select name="cinema_id" id="addCinemaId" required>
                            <option value="">-- Chọn rạp --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tên phòng</label>
                        <input type="text" name="name" placeholder="VD: Phòng 1, Phòng IMAX" required>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status_id" id="addStatusId" required>
                            <option value="">-- Chọn trạng thái --</option>
                        </select>
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

<script src="../../public/assets/js/api.js"></script>
<script src="../../public/assets/js/halls-admin.js"></script>