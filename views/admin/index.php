<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../config/dbConfig.php';
require_once __DIR__ . '/../../config/permissionConfig.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Role.php';
require_once __DIR__ . '/../../models/Permission.php';
require_once __DIR__ . '/../../models/Role_permissions.php';
require_once __DIR__ . '/../../models/FoodCombo.php';
require_once __DIR__ . '/../../models/Movie.php';
require_once __DIR__ . '/../../models/Genre.php';
require_once __DIR__ . '/../../models/Promotion.php';
// promotion model: chỉ load nếu file tồn tại
$promotionModelFile = __DIR__ . '/../../models/Promotion.php';
if (file_exists($promotionModelFile)) {
    require_once $promotionModelFile;
}

// =========================
// SERVICES
// =========================
require_once __DIR__ . '/../../services/RoleService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../services/Role_permissionsService.php';
require_once __DIR__ . '/../../services/UserService.php';
require_once __DIR__ . '/../../services/FoodComboService.php';
require_once __DIR__ . '/../../services/AuthMiddleware.php';
require_once __DIR__ . '/../../services/MovieService.php';
require_once __DIR__ . '/../../services/GenreService.php';

// promotion service: chỉ load nếu file tồn tại
$promotionServiceFile = __DIR__ . '/../../services/PromotionService.php';
if (file_exists($promotionServiceFile)) {
    require_once $promotionServiceFile;
}

// =========================
// CONTROLLERS
// =========================
require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/Role_permissionsController.php';
require_once __DIR__ . '/../../controllers/FoodComboController.php';
require_once __DIR__ . '/../../controllers/MovieController.php';
require_once __DIR__ . '/../../controllers/GenreController.php';
require_once __DIR__ . '/../../controllers/PromotionController.php';

// promotion controller: chỉ load nếu file tồn tại
$promotionControllerFile = __DIR__ . '/../../controllers/PromotionController.php';
if (file_exists($promotionControllerFile)) {
    require_once $promotionControllerFile;
}

// =========================
// AUTH
// =========================
$authUser = AuthMiddleware::requireAdmin();

// =========================
// DB CONNECTION
// =========================
$conn = getDBConnection();

// =========================
// INIT MODELS
// =========================
$userModel = new User($conn);
$roleModel = new Role($conn);
$permissionModel = new Permission($conn);
$rolePermissionModel = new Role_permissions($conn);
$foodComboModel = new FoodCombo($conn);
$movieModel = new Movie($conn);
$genreModel = new Genre($conn);

// promotion model init an toàn
$promotionModel = null;
if (class_exists('Promotion')) {
    $promotionModel = new Promotion($conn);
}

// =========================
// INIT SERVICES
// =========================
$userService = new UserService($userModel);
$roleService = new RoleService($roleModel);
$permissionService = new PermissionService($permissionModel, $rolePermissionModel);
$rolePermissionsService = new Role_permissionsService($rolePermissionModel);
$foodComboService = new FoodComboService($foodComboModel);
$movieService = new MovieService($movieModel, $genreModel);
$genreService = new GenreService($genreModel);

// promotion service init an toàn
$promotionService = null;
if ($promotionModel !== null && class_exists('PromotionService')) {
    $promotionService = new PromotionService($promotionModel);
}

// =========================
// INIT CONTROLLERS
// =========================
$adminController = new AdminController($userService, $roleService, $permissionService);
$rolePermissionsController = new Role_permissionsController($rolePermissionsService);
$foodComboController = new FoodComboController($foodComboService);
$movieController = new MovieController($movieService);
$genreController = new GenreController($genreService);


// promotion controller init an toàn
$promotionController = null;
if ($promotionService !== null && class_exists('PromotionController')) {
    $promotionController = new PromotionController($promotionService);
}

// =========================
// ALLOWED PAGES
// =========================
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
if (!in_array($page, $allowedPages, true)) {
    $page = '404';
}

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// =========================
// USERS
// =========================
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

// =========================
// ROLES
// =========================
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

// =========================
// PERMISSIONS
// =========================
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
            $rolePermissionsController->saveRolePermissions();
            exit;
    }
}

// =========================
// COMBOS
// =========================
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

// =========================
// MOVIES
// dùng POST cho create/update/delete
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'movies' && $action) {
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

// =========================
// GENRES
// dùng POST cho create/delete
// =========================
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'promotions' && $action) {
    switch ($action) {
        case 'create':
            $promotionController->create();
            exit;

        case 'update':
            $promotionController->update($_GET['id'] ?? 0);
            exit;

        case 'delete':
            $promotionController->delete($_GET['id'] ?? 0);
            exit;
    }
}// =========================
// PROMOTIONS
// chỉ chạy nếu module promotion tồn tại đầy đủ
// =========================
// if ($promotionController !== null && $page === 'promotions' && $action) {
//     switch ($action) {
//         case 'create':
//             $promotionController->create();
//             exit;

//         case 'update':
//             $promotionController->update($_POST['promotion_id'] ?? 0);
//             exit;

//         case 'delete':
//             $promotionController->delete($_GET['id'] ?? 0);
//             exit;
//     }
// }

// =========================
// VIEW FILE
// =========================
$contentPath = __DIR__ . "/pages/$page.php";
if (!file_exists($contentPath)) {
    $contentPath = __DIR__ . "/pages/404.php";
}
?>
<!DOCTYPE html>
<html lang="vi">
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
