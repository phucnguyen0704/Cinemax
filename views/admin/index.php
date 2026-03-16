<!DOCTYPE html>
<html lang="vi">
<?php
require_once __DIR__ . '/../../config/dbConfig.php';
require_once __DIR__ . '/../../config/permissionConfig.php';
//Các file model
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Role.php';
require_once __DIR__ . '/../../models/Permission.php';
require_once __DIR__ . '/../../models/Role_permissions.php';
require_once __DIR__ . '/../../models/FoodCombo.php';
require_once __DIR__ . '/../../models/Movie.php';
require_once __DIR__ . '/../../models/Genre.php';
require_once __DIR__ . '/../../models/Show.php';

//Các file service
require_once __DIR__ . '/../../services/RoleService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../services/Role_permissionsService.php';
require_once __DIR__ . '/../../services/UserService.php';
require_once __DIR__ . '/../../services/FoodComboService.php';
require_once __DIR__ . '/../../services/AuthMiddleware.php';
require_once __DIR__ . '/../../services/MovieService.php';
require_once __DIR__ . '/../../services/GenreService.php';
require_once __DIR__ . '/../../services/ShowService.php';

//Các file controller
require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/Role_permissionsController.php';
require_once __DIR__ . '/../../controllers/FoodComboController.php';
require_once __DIR__ . '/../../controllers/MovieController.php';
require_once __DIR__ . '/../../controllers/GenreController.php';
require_once __DIR__ . '/../../controllers/ShowController.php';
session_start();

// Kiểm tra đăng nhập và quyền admin bằng JWT
$authUser = AuthMiddleware::requireAdmin();

//Ket noi DB
$conn = getDBConnection();

//Khởi tạo models
$userModel = new User($conn);
$roleModel = new Role($conn);
$permissionModel = new Permission($conn);
$role_permissionModel = new Role_permissions($conn);
$foodComboModel = new FoodCombo($conn);
$movieModel = new Movie($conn);
$genreModel = new Genre($conn);
$showModel = new Show($conn);


//Khởi tạo services
$userService = new UserService($userModel);
$roleService = new RoleService($roleModel);
$permissionService = new PermissionService($permissionModel, $role_permissionModel);
$role_permissionsService = new Role_permissionsService($role_permissionModel);
$foodComboService = new FoodComboService($foodComboModel);
$movieService = new MovieService($movieModel, $genreModel);
$genreService = new GenreService($genreModel);
$showService = new ShowService($showModel);


//Khởi tạo controllers
$adminController = new AdminController($userService, $roleService, $permissionService);
$role_permissionsController = new Role_permissionsController($role_permissionsService);
$genreController = new GenreController($genreService);
$foodComboController = new FoodComboController($foodComboService);
$movieController = new MovieController($movieService);
$showController = new ShowController($showService);
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
$id = $_GET['id'] ?? null;

if ($page === 'movies' && $action) {
    switch ($action) {
        case 'create':
            $movieController->create();
            exit;

        case 'update':
            $movieId = $id ?? ($_POST['movie_id'] ?? 0);
            $movieController->update($movieId);
            exit;

        case 'delete':
            $movieController->delete($id ?? 0);
            exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'genres' && $action) {
    switch ($action) {
        case 'create':
            $genreController->create();
            exit;

        case 'delete':
            $genreController->delete($_GET['id'] ?? 0);
            exit;
    }
}

if ($page === 'shows' && $action) {
    switch ($action) {
        case 'create':
            $showController->createShow();
            exit;

        case 'update':
            $showId = $id ?? ($_POST['show_id'] ?? 0);
            $showController->updateShow($showId);
            exit;

        case 'delete':
            $showController->deleteShow($_GET['id'] ?? 0);
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

        <!--
                    <?php echo "<pre>";
        print_r($_SESSION);
        print_r($authUser);
        echo "</pre>"; ?>
        -->