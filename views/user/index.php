<!DOCTYPE html>
<html lang="vi">
<?php
require_once __DIR__ . '/../../services/AuthMiddleware.php';
require_once __DIR__ . '/../../services/MovieService.php';
require_once __DIR__ . '/../../services/PromotionService.php';



require_once __DIR__ . '/../../models/Movie.php';
require_once __DIR__ . '/../../models/Genre.php';
require_once __DIR__ . '/../../models/Promotion.php';

require_once __DIR__ . '/../../config/dbConfig.php';

// Lấy thông tin user từ JWT (null nếu chưa đăng nhập - user trang chủ không bắt buộc login)
$authUser = AuthMiddleware::getAuthUser();

$conn = getDbConnection(); // hoặc getDBConnection() nếu file dbConfig của anh đang dùng tên này
$movieModel = new Movie($conn);
$genreModel = new Genre($conn);
$promotionModel = new Promotion($conn);

$promotionService = new PromotionService($promotionModel);
$movieService = new MovieService($movieModel, $genreModel);

// Danh sách page hợp lệ (tránh hack ?content=../../)
$allowedPages = [
    'home',
    'movie_detail',
    'theaters',
    'movies',
    'promotions',
    'showtimes',
    'seat_selection',
    'booking_success',
    '404'
];

$page = $_GET['page'] ?? 'home';

if (!in_array($page, $allowedPages, true)) {
    $page = '404';
}

$contentPath = __DIR__ . "/pages/$page.php";
?>

<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>

<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main>
        <?php include $contentPath; ?>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>

</html>