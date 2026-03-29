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
$action = $_GET['action'] ?? null;

if (!in_array($page, $allowedPages, true)) {
    $page = '404';
}

if ($page === 'payment' && $action) {
    switch ($action) {
        case 'confirm_payment':
            $ticketIds = isset($_POST['ticket_ids']) && is_array($_POST['ticket_ids'])
                ? array_values(array_filter(array_map('intval', $_POST['ticket_ids'])))
                : [];

            $grandTotal  = isset($_POST['grand_total']) ? (float)$_POST['grand_total'] : 0;
            $showtimeId  = isset($_POST['showtime_id']) ? (int)$_POST['showtime_id'] : 0;
            $userId      = $authUser['user_id'] ?? 0;

            if (empty($ticketIds) || $userId <= 0) {
                header('Location: index.php?page=payment&error=invalid');
                exit;
            }

            try {
                $conn->begin_transaction();

                // 1. Tạo bill
                $sqlBill = "INSERT INTO bills (user_id, total_tickets, total_amount, final_amount, status)
                            VALUES (?, ?, ?, ?, 'pending')";
                $stmtBill = $conn->prepare($sqlBill);
                $totalTickets = count($ticketIds);
                $stmtBill->bind_param('iidd', $userId, $totalTickets, $grandTotal, $grandTotal);
                $stmtBill->execute();
                $billId = $conn->insert_id;

                // 2. Gắn bill_id + set status = 'booked' cho từng ticket
                $sqlUpdate = "UPDATE tickets SET bill_id = ?, status = 'booked' WHERE ticket_id = ? AND status = 'available'";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                foreach ($ticketIds as $tid) {
                    $stmtUpdate->bind_param('ii', $billId, $tid);
                    $stmtUpdate->execute();
                }

                $conn->commit();

                header('Location: index.php?page=booking_success&bill_id=' . $billId);
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                header('Location: index.php?page=payment&error=' . urlencode($e->getMessage()));
                exit;
            }
    }
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