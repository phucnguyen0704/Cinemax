<!DOCTYPE html>
<html lang="vi">
<?php
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

if (!in_array($page, $allowedPages)) {
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