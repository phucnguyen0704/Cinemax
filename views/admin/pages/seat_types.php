<section class="seat_types">
    
    <header class="admin-header">
        <h1>Quản lý Loại ghế & Giá vé</h1>
        <button class="btn-add" onclick="openModal('addSeatTypeModal')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Thêm loại ghế</span>
        </button>
    </header>

    <div class="dashboard-content">
        <!-- Alert mẫu -->
        <!-- <div class="alert alert-success">Thành công!</div> -->
        <!-- <div class="alert alert-error">Có lỗi xảy ra</div> -->

        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên loại ghế</th>
                            <th>Phụ thu (VNĐ)</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1</td>
                            <td><strong>Ghế thường</strong></td>
                            <td style="color: var(--success-color); font-weight: bold;">
                                +0 ₫
                            </td>
                            <td>
                                <a href="seat_types.php?action=edit&id=1" class="btn-action">Sửa</a>
                                <button class="btn-action danger">Xóa</button>
                            </td>
                        </tr>

                        <tr>
                            <td>#2</td>
                            <td><strong>Ghế VIP</strong></td>
                            <td style="color: var(--success-color); font-weight: bold;">
                                +50.000 ₫
                            </td>
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

    <div id="addSeatTypeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm loại ghế</h2>
                <button class="btn-close" onclick="closeModal('addSeatTypeModal')">&times;</button>
            </div>
            <form action="../../Handle/seattypes_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên loại ghế</label>
                        <input type="text" name="name" placeholder="VD: Ghế đôi" required>
                    </div>
                    <div class="form-group">
                        <label>Phụ thu (VNĐ)</label>
                        <input type="number" name="price_surcharge" value="0" required step="1000" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addSeatTypeModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</section>
