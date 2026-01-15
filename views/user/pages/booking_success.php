<section class="booking_success">
    <main class="section">
        <div class="container result-container">
            <div class="pending-card">
                <div class="pending-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h1>Đơn hàng đang chờ xác nhận</h1>
                <p>
                    Cảm ơn bạn đã đặt vé. Hệ thống đang xử lý giao dịch của bạn.<br>
                    Vui lòng đợi nhân viên xác nhận thanh toán để nhận vé điện tử.
                </p>

                <div class="order-ref">Mã đơn: #</div>

                <p style="font-size: 14px; color: #888;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 5px;">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Lưu ý: Vé chưa có hiệu lực cho đến khi trạng thái là "Thanh toán thành công".
                </p>

                <div class="btn-group">
                    <a href="index.php" class="btn-action btn-home">Về trang chủ</a>
                    <button class="btn-action btn-reload">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 4v6h-6"></path>
                            <path d="M1 20v-6h6"></path>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                        Kiểm tra lại
                    </button>
                </div>
            </div>

            <div class="ticket-wrapper" style="display: none;">
                <div style="margin-bottom: 30px;">
                    <div style="color: #46d369; margin-bottom: 10px;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <h1 style="font-size: 32px;">Đặt vé thành công!</h1>
                    <p style="color: #aaa;">Cảm ơn bạn đã sử dụng dịch vụ của CinemaHub</p>
                </div>

                <div class="ticket-card" id="ticketCapture">
                    <div class="ticket-header">
                        <h2 style="margin:0; font-size: 24px; text-transform: uppercase;">Vé Xem Phim</h2>
                        <div style="font-size: 13px; margin-top: 5px;">Mã đơn: #</div>
                    </div>

                    <div class="ticket-body">
                        <h3 style="color: #e50914; font-size: 22px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">

                        </h3>

                        <div class="tk-row"><span>Rạp chiếu</span> <span class="tk-val"></span></div>
                        <div class="tk-row"><span>Phòng chiếu</span> <span class="tk-val"></span></div>
                        <div class="tk-row"><span>Suất chiếu</span> <span class="tk-val"></span></div>
                        <div class="tk-row">
                            <span>Ghế ngồi</span>
                            <span class="tk-val" style="font-size: 16px;">

                            </span>
                        </div>

                        <?php if (!empty($booking['foods'])): ?>
                            <div class="tk-row">
                                <span>Combo</span>
                                <span class="tk-val" style="font-weight: normal;">
                                    <?php foreach ($booking['foods'] as $f) echo $f['Name'] . " (x" . $f['Quantity'] . ")<br>"; ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="tk-row" style="border: none; margin-top: 15px;">
                            <span style="font-weight: bold; color: #555;">Tổng thanh toán</span>
                            <span class="tk-val" style="font-size: 20px; color: #e50914;">
                                550000 ₫
                            </span>
                        </div>

                        <div class="qr-area">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=BOOKING--VERIFIED" alt="QR Code" style="width: 140px;">
                            <p style="font-size: 11px; color: #888; margin-top: 8px;">Đưa mã này cho nhân viên để vào rạp</p>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="index.php" class="btn-action btn-home">Về trang chủ</a>
                    <button id="btnSaveImage" class="btn-action btn-save">📸 Lưu ảnh vé</button>
                </div>
            </div>

            <div style="text-align: center; color: #e50914; display: none;">
                <h1>Đơn hàng đã bị hủy</h1>
                <p>Vui lòng đặt lại vé mới.</p>
                <a href="index.php" class="btn-action btn-home" style="margin-top: 20px; display: inline-block;">Quay về trang chủ</a>
            </div>

        </div>
    </main>
</section>