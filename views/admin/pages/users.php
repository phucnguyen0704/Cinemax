<section class="users">
    <header class="admin-header">
        <h1>Quản lý người dùng</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="openModal('addUserModal')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm người dùng</span>
            </button>

            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">
        <!-- TABLE USERS -->
        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Vai trò</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#1</strong></td>
                            <td>Nguyễn Văn A</td>
                            <td>a@gmail.com</td>
                            <td>0123456789</td>
                            <td>
                                <span class="badge badge-warning">Admin</span>
                            </td>
                            <td>
                                <a href="#" class="btn-action">Sửa</a>
                                <button class="btn-action danger">Xóa</button>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>#2</strong></td>
                            <td>Trần Thị B</td>
                            <td>b@gmail.com</td>
                            <td>-</td>
                            <td>
                                <span class="badge badge-success">User</span>
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

    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm người dùng mới</h2>
                <button class="btn-close" onclick="closeModal('addUserModal')">&times;</button>
            </div>

            <form>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password">
                    </div>
                    <div class="form-group">
                        <label>Vai trò</label>
                        <select>
                            <option>User</option>
                            <option>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addUserModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>
</section>