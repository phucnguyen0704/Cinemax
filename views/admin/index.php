<!DOCTYPE html>
<html lang="vi">
<?php

require_once __DIR__ . '/../../config/dbConfig.php';
require_once __DIR__ . '/../../services/AuthMiddleware.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Role.php';
require_once __DIR__ . '/../../models/Permission.php';
require_once __DIR__ . '/../../models/FoodCombo.php';
require_once __DIR__ . '/../../services/UserService.php';
require_once __DIR__ . '/../../services/FoodComboService.php';
require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/FoodComboController.php';
session_start();

// Kiểm tra đăng nhập và quyền admin bằng JWT
$authUser = AuthMiddleware::requireAdmin();

$conn = getDBConnection();
$userModel = new User($conn);
$roleModel = new Role($conn);
$permissionModel = new Permission($conn);
$foodComboModel = new FoodCombo($conn);

$userService = new UserService($userModel);
$roleService = new RoleService($roleModel);
$permissionService = new PermissionService($permissionModel);
$foodComboService = new FoodComboService($foodComboModel);

$adminController = new AdminController($userService, $roleService, $permissionService);
$foodComboController = new FoodComboController($foodComboService);

// Danh sách page hợp lệ (tránh hack ?content=../../)
$allowedPages = [
    'users',
    'roles',
    'permissions',
    'dashboard',
    'movies',
    'genres',
    'cinemas',
    'halls',
    'seat_types',
    'seats',
    'shows',
    'bookings',
    'combos',
    'promotions',
    '404'
];

$page = $_GET['page'] ?? 'dashboard';

if (!in_array($page, $allowedPages)) {
    $page = '404';
}
$action = $_GET['action'] ?? null;

if ($page === 'users' && $action) {
    switch ($action) {
        case 'create':
            $adminController->createUser();
            exit;

        case 'update':
            $adminController->updateUser($_POST['user_id'] ?? 0);
            exit;

        case 'delete':
            $adminController->deleteUser($_GET['id'] ?? 0);
            exit;
    }
}

if ($page === 'roles' && $action) {
    switch ($action) {
        case 'create':
            $adminController->createRole();
            exit;

        case 'update':
            $adminController->updateRole($_POST['role_id'] ?? 0);
            exit;

        case 'delete':
            $adminController->deleteRole($_GET['id'] ?? 0);
            exit;
    }
}

if ($page === 'permissions' && $action) {
    switch ($action) {
        case 'create':
            $adminController->createPermission();
            exit;

        case 'update':
            $adminController->updatePermission($_POST['permission_id'] ?? 0);
            exit;

        case 'delete':
            $adminController->deletePermission($_GET['id'] ?? 0);
            exit;
    }
}

if ($page === 'combos' && $action) {
    switch ($action) {
        case 'create':
            $foodComboController->createCombo();
            exit;

        case 'update':
            $foodComboController->updateCombo($_POST['combo_id'] ?? 0);
            exit;

        case 'delete':
            $foodComboController->deleteCombo($_GET['id'] ?? 0);
            exit;
    }
}

$contentPath = __DIR__ . "/pages/$page.php";
?>

<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="admin-main">
            <?php include $contentPath; ?>
        </main>
    </div>
</body>

</html>