<!DOCTYPE html>
<html lang="vi">
<?php

require_once __DIR__ . '/../../config/dbConfig.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Role.php';
require_once __DIR__ . '/../../models/Role_permissions.php';
require_once __DIR__ . '/../../models/Permission.php';
require_once __DIR__ . '/../../services/UserService.php';
require_once __DIR__ . '/../../services/RoleService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../services/Role_permissionsService.php';
require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/Role_permissionsController.php';
session_start();

//Ket noi DB
$conn = getDBConnection();

//Khởi tạo models
$userModel = new User($conn);
$roleModel = new Role($conn);
$permissionModel = new Permission($conn);
$role_permissionModel = new Role_permissions($conn);

//Khởi tạo services
$userService = new UserService($userModel);
$roleService = new RoleService($roleModel);
$permissionService = new PermissionService($permissionModel, $role_permissionModel);
$role_permissionsService = new Role_permissionsService($role_permissionModel);

//Khởi tạo controllers
$adminController = new AdminController($userService, $roleService, $permissionService);
$role_permissionsController = new Role_permissionsController($role_permissionsService);

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

        case 'saveRolePermissions':
            $role_permissionsController->saveRolePermissions();
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