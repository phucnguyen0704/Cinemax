<!DOCTYPE html>
<html lang="vi">
<?php

require_once __DIR__ . '/../../config/dbConfig.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../services/UserService.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

$conn = getDBConnection();
$userModel = new User($conn);
$userService = new UserService($userModel);
$adminController = new AdminController($userService);
session_start();
// Danh sách page hợp lệ (tránh hack ?content=../../)
$allowedPages = [
    'users',
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
            $adminController->updateUser($_GET['id'] ?? 0);
            exit;

        case 'delete':
            $adminController->deleteUser($_GET['id'] ?? 0);
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