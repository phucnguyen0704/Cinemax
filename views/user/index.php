<!DOCTYPE html>
<html lang="vi">
<?php
//Ket noi db
require_once __DIR__ . '/../../config/dbConfig.php';

//Cac file models
require_once __DIR__ . '/../../models/Movie.php';
require_once __DIR__ . '/../../models/Genre.php';
require_once __DIR__ . '/../../models/Promotion.php';
require_once __DIR__ . '/../../models/Show.php';
require_once __DIR__ . '/../../models/Ticket.php';



//Cac file service
require_once __DIR__ . '/../../services/AuthMiddleware.php';
require_once __DIR__ . '/../../services/MovieService.php';
require_once __DIR__ . '/../../services/PromotionService.php';
require_once __DIR__ . '/../../services/ShowService.php';
require_once __DIR__ . '/../../services/TicketService.php';

//Cac file controller
require_once __DIR__ . '/../../controllers/ShowController.php';

session_start();


// Lấy thông tin user từ JWT (null nếu chưa đăng nhập - user trang chủ không bắt buộc login)
$authUser = AuthMiddleware::getAuthUser();

//Ket noi db
$conn = getDbConnection();

//Khoi tao models
$movieModel = new Movie($conn);
$genreModel = new Genre($conn);
$promotionModel = new Promotion($conn);
$showModel = new Show($conn);
$ticketModel = new Ticket($conn);

//Khoi tao services
$promotionService = new PromotionService($promotionModel);
$movieService = new MovieService($movieModel, $genreModel);
$ticketService = new TicketService($ticketModel);
$showService = new ShowService($showModel, $ticketService);



//Khoi tao controller
$showController = new ShowController($showService);




// Danh sách page hợp lệ (tránh hack ?content=../../)
$allowedPages = [
    'home',
    'movie_detail',
    'theaters',
    'movies',
    'promotions',
    'showtimes',
    'seat_selection',
    'food_selection',
    'payment',
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