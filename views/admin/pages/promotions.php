<section class="promotions">

    <header class="admin-header">
        <h1>Quản lý Khuyến mãi</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="openModal('addPromoModal')">
                <i class="fas fa-plus"></i>
                <span>Thêm mã mới</span>
            </button>

            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">

        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã Code</th>
                            <th>Giá trị giảm</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center">Chưa có khuyến mãi nào.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div id="addPromoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm khuyến mãi mới</h2>
                <button class="btn-close" onclick="closeModal('addPromoModal')">&times;</button>
            </div>

            <form id="addPromoForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mã Code</label>
                        <input type="text" name="code" required>
                    </div>

                    <div class="form-group">
                        <label>Giảm % (VD: 10 cho 10%)</label>
                        <input type="number" name="discount_percent" value="0" min="0" max="100">
                    </div>

                    <div class="form-group">
                        <label>Giảm tiền (VNĐ)</label>
                        <input type="number" name="discount_value" value="0" min="0" step="1000">
                    </div>

                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="start_date" required>
                    </div>

                    <div class="form-group">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="end_date" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addPromoModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

</section>