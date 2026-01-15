<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chọn đồ ăn</title>
    <link rel="stylesheet" href="../../../public/assets/css/style.css">
    <link rel="stylesheet" href="../../../public/assets/css/seat-selection.css">
    <link rel="stylesheet" href="../../../public/assets/css/food-selection.css">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo"><span>Cinema</span></div>

                <div class="booking-steps">
                    <div class="step completed">
                        <span class="step-number">1</span>
                        <span class="step-label">Chọn suất</span>
                    </div>
                    <div class="step completed">
                        <span class="step-number">2</span>
                        <span class="step-label">Chọn ghế</span>
                    </div>
                    <div class="step active">
                        <span class="step-number">3</span>
                        <span class="step-label">Đồ ăn</span>
                    </div>
                    <div class="step">
                        <span class="step-number">4</span>
                        <span class="step-label">Thanh toán</span>
                    </div>
                </div>

                <a href="#" class="btn-back">
                    Hủy đơn
                </a>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <div class="container">
            <div class="booking-layout">

                <!-- FOOD SECTION -->
                <div class="food-section">
                    <div class="section-header">
                        <h2>Combo Bắp Nước & Ưu đãi</h2>
                        <p>Thêm đồ ăn để trải nghiệm điện ảnh trọn vẹn hơn</p>
                    </div>

                    <div class="food-grid">

                        <!-- FOOD CARD -->
                        <div class="food-card">
                            <div class="food-img">
                                <img src="#"
                                    alt="Food image">
                            </div>
                            <div class="food-info">
                                <h3>Tên món</h3>
                                <p class="desc">Mô tả món ăn</p>
                                <div class="price">0 ₫</div>

                                <div class="qty-control"
                                    data-id=""
                                    data-price=""
                                    data-name="">
                                    <button type="button" class="btn-qty minus">-</button>
                                    <span class="qty-val">0</span>
                                    <button type="button" class="btn-qty plus">+</button>
                                </div>
                            </div>
                        </div>
                        <!-- /FOOD CARD -->

                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="booking-sidebar">
                    <div class="booking-summary">
                        <h3>Thông tin đặt vé</h3>

                        <div class="summary-block">
                            <h4 class="movie-title">Tên phim</h4>
                            <div class="info-row"><span>Rạp:</span> <strong>Tên rạp</strong></div>
                            <div class="info-row"><span>Suất:</span> <strong>00:00 01/01</strong></div>
                            <div class="info-row"><span>Phòng:</span> <strong>Phòng chiếu</strong></div>
                            <div class="info-row">
                                <span>Ghế:</span>
                                <strong style="color: var(--primary-color);">A1, A2</strong>
                            </div>
                        </div>

                        <div class="summary-block"
                            id="selectedFoodContainer"
                            style="display:none; border-top: 1px solid var(--border-color); padding-top: 15px; margin-top: 15px;">
                            <h4 style="font-size: 14px; color: var(--text-secondary); margin-bottom: 10px;">Đồ ăn</h4>
                            <div id="selectedFoodList"></div>
                        </div>

                        <div class="summary-section"
                            style="border-top: 1px solid var(--border-color); margin-top: 20px; padding-top: 20px;">
                            <div class="price-row">
                                <span>Tiền vé:</span>
                                <span>0 ₫</span>
                            </div>
                            <div class="price-row">
                                <span>Tiền đồ ăn:</span>
                                <span id="foodTotalDisplay">0 ₫</span>
                            </div>
                            <div class="price-row total">
                                <span>Tổng cộng</span>
                                <span id="grandTotalDisplay">0 ₫</span>
                            </div>
                        </div>

                        <form id="finalForm">
                            <div id="foodInputs"></div>

                            <button type="submit" class="btn-continue">
                                Xác nhận & Thanh toán
                                <svg width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>


    <script>
        const seatTotal = 3;
        btn_continue = document.querySelector('.btn-continue');

        btn_continue.addEventListener('click', function(event) {
            event.preventDefault();
            window.location.href = "../../user/pages/payment.php";
        });
    </script>
    <script src="../../../public/assets/js/food-selection.js"></script>
</body>

</html>