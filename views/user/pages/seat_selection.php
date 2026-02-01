<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chọn ghế</title>
    <link rel="stylesheet" href="../../../public/assets/css/style.css">
    <link rel="stylesheet" href="../../../public/assets/css/seat-selection.css">

    <style>
        .seat.type-1 {
            background-color: #7d7d7d;
            border-color: #999;
            color: #fff;
        }

        .seat.type-2 {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border: 1px solid #ffb300;
            color: #000;
            font-weight: bold;
        }

        .seat.sold {
            background: #b71c1c !important;
            border-color: #d32f2f !important;
            color: #ffcdd2 !important;
            cursor: not-allowed;
            opacity: 0.7;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(0, 0, 0, 0.2) 5px, rgba(0, 0, 0, 0.2) 10px);
        }

        .seat.held {
            background: #0288d1 !important;
            border-color: #0277bd !important;
            cursor: not-allowed;
        }

        .seat.selected {
            background: #46d369 !important;
            border-color: #46d369 !important;
            color: #fff !important;
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(70, 211, 105, 0.5);
            z-index: 10;
        }

        /* CSS RIÊNG CHO ADMIN (CHỈ XEM) */
        .admin-view-mode .seat {
            cursor: default !important;
            pointer-events: none;
        }

        .admin-view-mode .seat:hover {
            transform: none;
        }

        .admin-notice {
            background: #333;
            color: #ffc107;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px dashed #ffc107;
        }

        /* CSS ĐỒNG HỒ ĐẾM NGƯỢC */
        .countdown-box {
            background: #e50914;
            color: #fff;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(229, 9, 20, 0.3);
        }
    </style>

    <!-- <script>
        const showtimeId = <?php echo $showtime_id; ?>;
        const basePrice = <?php echo $base_price; ?>;
        const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
    </script> -->
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo"><span>Cinema</span></div>
                <a href="<?php echo $is_admin ? '../admin/showtimes.php' : 'index.php'; ?>" class="btn-back">Thoát</a>
            </div>
        </div>
    </nav>

    <main class="booking-container">
        <div class="container">
            <form id="bookingForm" action="../../user/pages/food_selection.php" method="POST">
                <input type="hidden" name="action" value="reserve_seats">
                <input type="hidden" name="showtime_id" value="<?php echo $showtime_id; ?>">
                <div id="hiddenInputs"></div>

                <div class="booking-layout">
                    <div class="screen-section">
                        <!-- 
                        <?php if ($is_admin): ?>
                            <div class="admin-notice">⚠️ Bạn đang xem ở chế độ <strong>ADMIN</strong> (Chỉ xem tình trạng ghế, không thể đặt vé).</div>
                        <?php endif; ?> -->

                        <div class="showtime-info">
                            <div class="info-item"><span class="label">Phim:</span><span class="value"></span></div>
                            <div class="info-item"><span class="label">Suất:</span><span class="value"></span></div>
                            <div class="info-item"><span class="label">Rạp:</span><span class="value"></span></div>
                        </div>

                        <div class="screen-wrapper">
                            <div class="screen"><span>MÀN HÌNH</span></div>

                            <div class="seats-container" id="seatsContainer">
                                <div class="seat-grid" style="display: flex; flex-direction: column; gap: 8px; align-items: center;">
                                    <div style="text-align: center; padding: 40px; color: #888;">
                                        <div class="loading">Đang tải sơ đồ ghế...</div>
                                    </div>
                                </div>
                            </div>

                            <div class="seat-legend legend" id="seatLegend">
                                <div style="text-align: center; padding: 10px; color: #888;">
                                    <div class="loading">Đang tải...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="booking-sidebar">
                        <div class="booking-summary">
                            <h3>Thông tin đặt vé</h3>
                            <div class="countdown-box">
                                Thời gian giữ ghế: <span id="countdown">10:00</span>
                            </div>
                            <div class="summary-section">
                                <h4>Ghế đã chọn</h4>
                                <div id="selectedSeats" class="selected-seats-list">
                                    <p class="empty-message">Chưa chọn ghế</p>
                                </div>
                            </div>
                            <div class="summary-section">
                                <div class="price-row"><span>Tạm tính</span><span id="totalPrice">0 ₫</span></div>
                            </div>


                            <button class="btn-continue" type="submit">
                                Tiếp tục chọn đồ ăn
                            </button>

                            <!-- <button type="button" class="btn-continue" disabled style="background: #333; cursor: not-allowed;">
                                    Admin không thể đặt vé
                                </button> -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="../../../public/assets/js/api.js"></script>
    <script src="../../../public/assets/js/user-seat-selection.js"></script>

    <script>
        function startCountdown(duration) {
            const display = document.getElementById('countdown');
            if (!display) return;

            let timer = duration,
                minutes, seconds;

            const interval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    display.textContent = "00:00";
                    alert("Đã hết thời gian giữ ghế! Trang sẽ tải lại để cập nhật trạng thái.");
                    window.location.reload();
                }
            }, 1000);
        }

        // Start countdown when page loads
        document.addEventListener('DOMContentLoaded', () => {
            startCountdown(600); // 10 phút = 600 giây
        });
    </script>
</body>

</html>