<?php
$billId = isset($_GET['bill_id']) ? (int)$_GET['bill_id'] : 0;

$billData = null;
$tickets  = [];
$combos   = [];

if ($billId > 0) {
    $sqlBill = "SELECT b.*, u.full_name, u.email
                FROM bills b
                JOIN users u ON u.user_id = b.user_id
                WHERE b.bill_id = ?";
    $stmtBill = $conn->prepare($sqlBill);
    $stmtBill->bind_param('i', $billId);
    $stmtBill->execute();
    $billData = $stmtBill->get_result()->fetch_assoc();

    if ($billData) {
        $tickets = $ticketService->getTicketsByBillId($billId);

        $sqlCombos = "SELECT bc.quantity, bc.price, co.name
                      FROM bill_combos bc
                      JOIN combos co ON co.combo_id = bc.combo_id
                      WHERE bc.bill_id = ?";
        $stmtCombos = $conn->prepare($sqlCombos);
        $stmtCombos->bind_param('i', $billId);
        $stmtCombos->execute();
        $resultCombos = $stmtCombos->get_result();
        while ($row = $resultCombos->fetch_assoc()) $combos[] = $row;
    }
}

$isPaid      = $billData && $billData['status'] === 'paid';
$isPending   = $billData && $billData['status'] === 'pending';
$isCancelled = $billData && $billData['status'] === 'cancelled';
?>

<section class="booking_success">
    <main class="section">
        <div class="container result-container">

            <?php if (!$billData): ?>
                <div style="text-align:center;padding:60px;color:#e50914;">
                    <h1>Không tìm thấy đơn hàng.</h1>
                    <a href="index.php" class="btn-action btn-home">Về trang chủ</a>
                </div>

            <?php elseif ($isCancelled): ?>
                <div style="text-align:center;color:#e50914;">
                    <h1>Đơn hàng đã bị hủy</h1>
                    <p>Vui lòng đặt lại vé mới.</p>
                    <a href="index.php" class="btn-action btn-home" style="margin-top:20px;display:inline-block;">Quay về trang chủ</a>
                </div>

            <?php elseif ($isPending): ?>
                <!-- GIỮ NGUYÊN -->
                <div class="pending-card">
                    <div class="pending-icon">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h1>Đơn hàng đang chờ xác nhận</h1>
                    <p>Cảm ơn bạn đã đặt vé. Hệ thống đang xử lý giao dịch của bạn.<br>
                        Vui lòng đợi nhân viên xác nhận thanh toán để nhận vé điện tử.</p>
                    <div class="order-ref">Mã đơn: #<?php echo $billId; ?></div>
                    <p style="font-size:14px;color:#888;">
                        ⚠️ Vé chưa có hiệu lực cho đến khi trạng thái là "Thanh toán thành công".
                    </p>
                    <div class="btn-group">
                        <a href="index.php" class="btn-action btn-home">Về trang chủ</a>
                        <a href="index.php?page=booking_success&bill_id=<?php echo $billId; ?>" class="btn-action btn-reload">🔄 Kiểm tra lại</a>
                    </div>
                </div>

            <?php elseif ($isPaid): ?>

                <div class="success-layout">

                    <!-- HEADER -->
                    <div class="success-header">
                        <h1>🎉 Đặt vé thành công</h1>
                        <p>
                            Mã đơn: <strong>#<?php echo $billId; ?></strong> —
                            <span class="price"><?php echo number_format($billData['final_amount'], 0, ',', '.'); ?> ₫</span>
                        </p>
                    </div>

                    <!-- TICKETS -->
                    <div class="ticket-wrapper">
                        <?php foreach ($tickets as $ticket):
                            $seatLabel = $ticket['row_name'] . $ticket['seat_number'];
                            $qrData = "CINEMAX-BILL{$billId}-TICKET{$ticket['ticket_id']}-SEAT{$seatLabel}";
                            $qrUrl  = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($qrData);
                        ?>

                            <div class="ticket-card">
                                <div class="ticket-header">
                                    🎬 Vé xem phim #<?php echo $ticket['ticket_id']; ?>
                                </div>

                                <div class="ticket-body">
                                    <h2 class="movie-title"><?php echo $ticket['movie_title']; ?></h2>

                                    <p><b>Rạp:</b> <?php echo $ticket['cinema_name']; ?></p>
                                    <p><b>Phòng:</b> <?php echo $ticket['hall_name']; ?></p>
                                    <p><b>Suất:</b>
                                        <?php echo date('d/m/Y', strtotime($ticket['show_date'])) . ' ' . substr($ticket['start_time'], 0, 5); ?>
                                    </p>
                                    <p><b>Ghế:</b> <span class="seat"><?php echo $seatLabel; ?></span></p>

                                    <div class="price">
                                        <?php echo number_format($ticket['price'], 0, ',', '.'); ?> ₫
                                    </div>

                                    <div class="qr">
                                        <img src="<?php echo $qrUrl; ?>">
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>

                    <!-- BUTTON -->
                    <div class="btn-bottom">
                        <a href="index.php" class="btn-action overlay-btn btn-detail">Về trang chủ</a>
                    </div>

                </div>

            <?php endif; ?>

        </div>
    </main>
</section>