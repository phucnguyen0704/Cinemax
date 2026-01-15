<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="../../../public/assets/css/style.css">
    <link rel="stylesheet" href="../../../public/assets/css/seat-selection.css">
    <link rel="stylesheet" href="../../../public/assets/css/food-selection.css">
    <link rel="stylesheet" href="../../../public/assets/css/payment.css">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo"><span>Cinema </span></div>

                <div class="booking-steps">
                    <div class="step completed"><span class="step-number">1</span><span class="step-label">Suất chiếu</span></div>
                    <div class="step completed"><span class="step-number">2</span><span class="step-label">Ghế</span></div>
                    <div class="step completed"><span class="step-number">3</span><span class="step-label">Đồ ăn</span></div>
                    <div class="step active"><span class="step-number">4</span><span class="step-label">Thanh toán</span></div>
                </div>

                <a href="../../Handle/bookings_process.php?action=cancel&id=<?php echo $booking_id; ?>"
                    class="btn-back" onclick="return confirm('Hủy đơn hàng này?');">Hủy đơn</a>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <div class="container">
            <div class="booking-layout">

                <div class="payment-section">
                    <h2>Chọn phương thức thanh toán</h2>

                    <form id="paymentForm" action="../index.php?page=booking_success" method="POST">
                        <input type="hidden" name="action" value="confirm_payment">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">

                        <div class="method-list">
                            <label class="method-item active">
                                <input type="radio" name="payment_method" value="ATM" checked>
                                <img src="https://cdn-icons-png.flaticon.com/512/2534/2534204.png" class="method-icon">
                                <div class="method-info">
                                    <h4>Thẻ ATM nội địa / Internet Banking</h4>
                                    <p>Hỗ trợ tất cả các ngân hàng tại Việt Nam</p>
                                </div>
                                <div class="radio-custom"></div>
                            </label>

                            <label class="method-item">
                                <input type="radio" name="payment_method" value="MOMO">
                                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" class="method-icon">
                                <div class="method-info">
                                    <h4>Ví điện tử MoMo</h4>
                                    <p>Quét mã QR để thanh toán nhanh chóng</p>
                                </div>
                                <div class="radio-custom"></div>
                            </label>

                            <label class="method-item">
                                <input type="radio" name="payment_method" value="VISA">
                                <img src="https://cdn-icons-png.flaticon.com/512/349/349221.png" class="method-icon">
                                <div class="method-info">
                                    <h4>Thẻ Quốc tế (Visa / Master / JCB)</h4>
                                    <p>Thanh toán an toàn, bảo mật</p>
                                </div>
                                <div class="radio-custom"></div>
                            </label>
                        </div>

                        <div class="note-box">
                            ⚠️ <strong>Lưu ý:</strong> Sau khi bấm "Thanh toán", đơn hàng sẽ được chuyển sang trạng thái <strong>Chờ xác nhận</strong>. Vui lòng đợi nhân viên/hệ thống xác nhận trong giây lát.
                        </div>
                    </form>
                </div>

                <div class="booking-sidebar">
                    <div class="booking-summary">
                        <h3 style="border-bottom: 1px solid #444; padding-bottom: 15px; margin-bottom: 15px;">Thông tin đặt vé</h3>

                        <div class="summary-block">
                            <h4 style="color: #fff; margin-bottom: 10px; font-size: 18px;"></h4>
                            <div class="info-row"><span>Rạp:</span> <strong></strong></div>
                            <div class="info-row"><span>Suất:</span> <strong></strong></div>
                            <div class="info-row"><span>Phòng:</span> <strong>
                                </strong>
                            </div>
                            <div class="info-row"><span>Ghế:</span> <strong style="color: var(--primary-color);"></strong></div>
                        </div>

                        <div style="margin-top: 20px;">
                            <div class="info-row">
                                <span>Tổng vé:</span>
                                <span>500000 ₫</span>
                            </div>
                            <div class="info-row">
                                <span>Tổng đồ ăn:</span>
                                <span>50000 ₫</span>
                            </div>

                            <div style="border-top: 1px dashed #555; margin: 15px 0;"></div>

                            <div class="info-row" style="align-items: center;">
                                <span style="font-size: 16px; font-weight: bold; color: #fff;">Tổng thanh toán:</span>
                                <span class="final-total">550000 ₫</span>
                            </div>
                        </div>

                        <button type="button" onclick="submitPayment()" class="btn-continue">
                            THANH TOÁN NGAY
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function submitPayment() {
            if (confirm('Xác nhận thanh toán đơn hàng này?')) {
                document.getElementById('paymentForm').submit();
            }
        }

        // Hiệu ứng JS đổi class active khi click
        const methods = document.querySelectorAll('.method-item');
        methods.forEach(m => {
            m.addEventListener('click', () => {
                // Bỏ active ở tất cả
                methods.forEach(x => x.classList.remove('active'));
                // Thêm active vào cái được click
                m.classList.add('active');
                // Check radio button bên trong
                m.querySelector('input').checked = true;
            });
        });
    </script>
</body>

</html>