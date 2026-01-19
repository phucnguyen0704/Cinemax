<section class="theaters">

    <header class="admin-header">
        <h1>Quản lý rạp chiếu</h1>
        <div class="header-actions">

            <button class="btn-add" onclick="openModal('addTheaterModal')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm rạp mới</span>
            </button>

            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">

        <!-- TABLE LIST -->
        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên rạp</th>
                        <th>Địa chỉ</th>
                        <th>Thành phố</th>
                        <th>Số phòng</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody id="theatersTableBody">

                    <tr>
                        <td><strong>#1</strong></td>
                        <td>CGV Vincom</td>
                        <td>72 Lê Thánh Tôn</td>
                        <td>TP.HCM</td>
                        <td>8</td>
                        <td>
                            <a href="#" class="btn-action">Sửa</a>
                            <button class="btn-action danger">Xóa</button>
                            <a href="#" class="btn-action">Quản lý phòng</a>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>#2</strong></td>
                        <td>Lotte Cinema</td>
                        <td>20 Cộng Hòa</td>
                        <td>TP.HCM</td>
                        <td>6</td>
                        <td>
                            <a href="#" class="btn-action">Sửa</a>
                            <button class="btn-action danger">Xóa</button>
                            <a href="#" class="btn-action">Quản lý phòng</a>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL ADD THEATER -->
    <div id="addTheaterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm rạp mới</h2>
                <button class="btn-close" onclick="closeModal('addTheaterModal')">&times;</button>
            </div>

            <form>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên rạp</label>
                        <input type="text">
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <input type="text">
                    </div>
                    <div class="form-group">
                        <label>Thành phố</label>
                        <input type="text">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addTheaterModal')">
                        Hủy
                    </button>
                    <button type="submit" class="btn-primary">
                        Thêm rạp
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>
