<section class="seats">

    <header class="admin-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="screens.php" class="btn-action">← Quay lại</a>
            <h1>Sơ đồ ghế: Phòng Chiếu 1</h1>
        </div>
        <div style="color: #888;">Rạp Cinestar Quận 1</div>

        <form action="../../Handle/seats_process.php" method="POST"
            onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ xóa sạch sơ đồ hiện tại để làm lại!');">
            <input type="hidden" name="action" value="reset">
            <input type="hidden" name="screen_id" value="1">
            <button class="btn-action danger">Xóa sơ đồ & Làm lại</button>
        </form>
    </header>

    <div class="dashboard-content">

        <!-- Alert mẫu -->
        <!-- <div class="alert alert-success">Thành công!</div> -->
        <!-- <div class="alert alert-error">Có lỗi xảy ra</div> -->

        <div class="legend">
            <div class="legend-item">
                <div class="dot type-1"></div>
                Ghế Thường
            </div>
            <div class="legend-item">
                <div class="dot type-2"></div>
                Ghế VIP
            </div>
            <div class="legend-item" style="margin-left: 15px; border-left: 1px solid #444; padding-left: 15px;">
                👉 Click ghế để đổi loại | ❌ Click dấu X để xóa
            </div>
        </div>

        <div class="editor-area">
            <div class="screen-line"></div>
            <div style="margin-bottom: 30px; font-size: 12px; color: #666; letter-spacing: 2px;">
                MÀN HÌNH
            </div>

            <div class="seat-grid">

                <div class="seat-row">
                    <div class="row-label">A</div>

                    <div style="position: relative;">
                        <a href="#" class="seat-item type-1" title="Loại: Ghế Thường">
                            A1
                            <form action="../../Handle/seats_process.php" method="POST" style="display:contents;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="seat_id" value="1">
                                <input type="hidden" name="screen_id" value="1">
                                <button type="submit" class="btn-x"
                                    title="Xóa"
                                    onclick="event.stopPropagation(); return confirm('Xóa ghế này?');">
                                    ×
                                </button>
                            </form>
                        </a>
                    </div>

                    <div style="position: relative;">
                        <a href="#" class="seat-item type-1" title="Loại: Ghế Thường">A2</a>
                    </div>

                    <div style="position: relative;">
                        <a href="#" class="seat-item type-1" title="Loại: Ghế Thường">A3</a>
                    </div>
                </div>

                <div class="seat-row">
                    <div class="row-label">B</div>

                    <div style="position: relative;">
                        <a href="#" class="seat-item type-2" title="Loại: Ghế VIP">B1</a>
                    </div>

                    <div style="position: relative;">
                        <a href="#" class="seat-item type-2" title="Loại: Ghế VIP">B2</a>
                    </div>

                    <div style="position: relative;">
                        <a href="#" class="seat-item type-2" title="Loại: Ghế VIP">B3</a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>