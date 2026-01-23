<?php
$users = $adminController->getAllUsers();
$error = $_SESSION['error'] ?? null;
?>
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
        <?php if (isset($_GET['error']) || isset($_GET['add'])): ?>
            <div class="alert alert-success" id="autoAlert">
                <?php
                if (isset($error)) {
                    if ($_GET['error'] == 1) {
                        echo $error;
                    }
                }
                if ($_GET['add'] == 1) {
                    echo "Người dùng đã được thêm thành công!";
                }
                ?>
            </div>
        <?php endif; ?>
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
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($user['UserID']) ?></strong></td>
                                    <td><?= htmlspecialchars($user['FullName']) ?></td>
                                    <td><?= htmlspecialchars($user['Email']) ?></td>
                                    <td><?= htmlspecialchars($user['Phone'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge <?= ($user['Role'] ?? 'User') === 'Admin' ? 'badge-warning' : 'badge-success' ?>">
                                            <?= htmlspecialchars($user['Role'] ?? 'User') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn-action">Sửa</a>
                                        <button class="btn-action danger">Xóa</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:20px;">
                                    Chưa có người dùng nào trong hệ thống.
                                </td>
                            </tr>
                        <?php endif; ?>
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

            <form id="addUserForm" action="../admin/index.php?page=users&action=create" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text" name="fullName" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" required>
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