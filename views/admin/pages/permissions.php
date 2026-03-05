<?php
$permissions = $adminController->getAllPermissions();
$roles = $adminController->getAllRoles();
$rolePermissions = $role_permissionsController->getAllRolePermissions();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
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
        </div>
    </header>

    <div class="dashboard-content">

        <!-- ALERT -->
        <?php if ($error || isset($_GET['add']) || isset($_GET['update']) || isset($_GET['delete']) || isset($_GET['save'])): ?>
            <div class="alert <?= $error ? 'alert-error' : 'alert-success' ?>" id="autoAlert">
                <?php
                if ($error) {
                    echo htmlspecialchars($error);
                }

                if (isset($_GET['add']) && $_GET['add'] == 1) {
                    echo "Quyền đã được thêm thành công!";
                } elseif (isset($_GET['update']) && $_GET['update'] == 1) {
                    echo "Quyền đã được cập nhật thành công!";
                } elseif (isset($_GET['delete']) && $_GET['delete'] == 1) {
                    echo "Quyền đã được xóa thành công!";
                } elseif (isset($_GET['save']) && $_GET['save'] == 1) {
                    echo "Phân quyền đã được lưu thành công!";
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-card">
            <div class="table-responsive">
                <form method="POST" action="../admin/index.php?page=permissions&action=saveRolePermissions">
                    <table class="data-table permission-matrix">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Chức năng</th>

                                <?php foreach ($roles as $role): ?>
                                    <th style="text-align: center;"><?= htmlspecialchars($role['role_name']) ?></th>
                                <?php endforeach; ?>

                                <th style="text-align: center;">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($permissions as $permission): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($permission['permission_id']) ?></strong></td>
                                    <td><?= htmlspecialchars($permission['permission_code']) ?></td>

                                    <?php foreach ($roles as $role): ?>
                                        <td>
                                            <input type="checkbox"
                                                name="role_permissions[<?= $role['role_id'] ?>][]"
                                                value="<?= $permission['permission_id'] ?>"
                                                <?= isset($rolePermissions[$role['role_id']]) &&
                                                    in_array($permission['permission_id'], $rolePermissions[$role['role_id']])
                                                    ? 'checked' : '' ?>>
                                        </td>
                                    <?php endforeach; ?>

                                    <td>
                                        <button class="btn-action" type="button"
                                            onclick="openUpdatePermissionModal(this)"
                                            data-permission-id="<?= $permission['permission_id'] ?>"
                                            data-permission-code="<?= htmlspecialchars($permission['permission_code']) ?>"
                                            data-permission-description="<?= htmlspecialchars($permission['description']) ?>">
                                            <i class="fas fa-edit" style="color: #007BFF;"></i>
                                        </button>

                                        <button type="button" class="btn-action" onclick="confirmDelete(<?= htmlspecialchars($permission['permission_id']) ?>)">
                                            <i class="fas fa-times" style="color: #E50914;"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top:20px; text-align:right;">
                        <button class="btn-primary" type="submit">Lưu phân quyền</button>
                    </div>
                </form>

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
                </div>
            </form>
        </div>
    </div>

    <!-- UPDATE PERMISSION MODAL -->
    <div id="updatePermissionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Cập nhật quyền</h2>
                <button class="btn-close" onclick="closeModal('updatePermissionModal')">&times;</button>
            </div>

            <form id="updatePermissionForm" action="../admin/index.php?page=permissions&action=update" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" id="update_permission_id" name="permission_id">
                        <label>Tên quyền</label>
                        <input type="text" id="update_permission_code" name="permission_code"
                            placeholder="VD: Xem người dùng" required>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea id="update_permission_description" name="description" placeholder="Mô tả quyền (tùy chọn)"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-action"
                            onclick="closeModal('updatePermissionModal')">Hủy</button>
                        <button type="submit" class="btn-primary">Cập nhật quyền</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>