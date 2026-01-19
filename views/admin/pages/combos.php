<section class="combos">

    <header class="admin-header">
        <h1>Quản lý Đồ ăn & Combo</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="openModal('addFoodModal')">
                <span>+ Thêm món mới</span>
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
                            <th>Hình ảnh</th>
                            <th>Tên món / combo</th>
                            <th>Giá</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">Chưa có món nào.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div id="addFoodModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm món mới</h2>
                <button class="btn-close" onclick="closeModal('addFoodModal')">&times;</button>
            </div>

            <form id="addFoodForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên món / combo</label>
                        <input type="text" name="name" required class="custom-input">
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" rows="3" class="custom-input"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Giá (VNĐ)</label>
                        <input type="number" name="price" required step="1000" min="0" class="custom-input">
                    </div>

                    <div class="form-group">
                        <label>Hình ảnh</label>
                        <input type="file" name="image_file" accept="image/*" class="custom-input">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addFoodModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm món</button>
                </div>
            </form>
        </div>
    </div>

</section>