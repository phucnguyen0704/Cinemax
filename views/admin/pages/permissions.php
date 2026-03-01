<?php
$permisstions = $adminController->getAllPermissions();
$error = $_GET['error'] ?? null;
?>
<section class="permissions">
    <header class="admin-header">
        <h1>Quản lý quyền</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="openModal('addPermissionModal')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm quyền</span>
            </button>

            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">

        <!-- ALERT -->
        <?php if ($error || isset($_GET['add'])): ?>
            <div class="alert alert-success" id="autoAlert">
                <?php
                if ($error) {
                    echo htmlspecialchars($error);
                }

                if (isset($_GET['add']) && $_GET['add'] == 1) {
                    echo "Quyền đã được thêm thành công!";
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="data-table permission-matrix">
                    <thead>
                        <tr>
                            <th>Chức năng</th>
                            <th>Admin</th>
                            <th>Manager</th>
                            <th>Staff</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($permisstions)): ?>
                            <?php foreach ($permisstions as $permission): ?>
                                <tr>
                                    <td><?= htmlspecialchars($permission['permission_code']) ?></td>
                                    <td>
                                        <input type="checkbox" name="permission_admin_<?= htmlspecialchars($permission['permission_id']) ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="permission_manager_<?= htmlspecialchars($permission['permission_id']) ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="permission_staff_<?= htmlspecialchars($permission['permission_id']) ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Không có quyền nào được tìm thấy.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px; text-align:right;">
                <button class="btn-primary">Lưu phân quyền</button>
            </div>
        </div>

    </div>

    <!-- ADD PERMISSION MODAL -->
    <div id="addPermissionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm quyền mới</h2>
                <button class="btn-close" onclick="closeModal('addPermissionModal')">&times;</button>
            </div>

            <form id="addPermissionForm" action="../admin/index.php?page=permissions&action=create" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên quyền</label>
                        <input type="text" name="permission_code"
                            placeholder="VD: Xem người dùng" required>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" placeholder="Mô tả quyền (tùy chọn)"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action"
                        onclick="closeModal('addPermissionModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm quyền</button>
                </div>
            </form>
        </div>
    </div>
</section>