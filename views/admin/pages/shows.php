<?php
$shows = $showController->getAllShows();
$halls = $hallController->getAllHalls();
$cinemas = $cinemaController->getAllCinemas();
$movies = $movieService->listMoviesAdmin();

$error = $_GET['error'] ?? null;

?>
<!-- Script chuyển data sang js-->
<script>
    window.initialShows = <?= json_encode($shows) ?>;
    window.initialHalls = <?= json_encode($halls) ?>;
    window.initialCinemas = <?= json_encode($cinemas) ?>;
    window.initialMovies = <?= json_encode($movies) ?>;
</script>

<section class="schedule-page shows">


    <div id="theaterSelectView" class="theater-select">

        <h2>Chọn rạp chiếu</h2>

        <div class="theater-list">

            <?php foreach ($cinemas as $cinema): ?>

                <button
                    class="theater-btn"
                    onclick="selectTheater('<?= htmlspecialchars($cinema['CinemaID']) ?>')">

                    <?= htmlspecialchars($cinema['Name']) ?>

                </button>

            <?php endforeach; ?>

        </div>

    </div>



    <div id="calendarView" class="calendar-wrapper">

        <div class="schedule-toolbar">

            <div class="toolbar-left">
                <button onclick="backToTheater()" class="btn-back">
                    ← Chọn rạp
                </button>
                <span id="theaterName"></span>
            </div>

            <div class="toolbar-center">
                <button class="nav-btn" onclick="prev()">◀</button>
                <h2 id="currentDate"></h2>
                <button class="nav-btn" onclick="next()">▶</button>
            </div>

            <div class="toolbar-right">
                <button class="view-btn active" onclick="setView('day',event)">Day</button>
                <button class="view-btn" onclick="setView('week',event)">Week</button>
            </div>

        </div>

        <div class="schedule-legend">

            <div class="legend-item">
                <span class="legend-color draft"></span>
                Chưa mở bán
            </div>

            <div class="legend-item">
                <span class="legend-color active"></span>
                Đang mở bán
            </div>

            <div class="legend-item">
                <span class="legend-color finished"></span>
                Đã kết thúc
            </div>

        </div>
        <?php if ($error || isset($_GET['add']) || isset($_GET['update']) || isset($_GET['delete'])): ?>
            <div class="alert <?= $error ? 'alert-error' : 'alert-success' ?>" id="autoAlert">
                <?php
                if ($error) {
                    echo htmlspecialchars($error);
                }

                if (isset($_GET['add']) && $_GET['add'] == 1) {
                    echo "Suất chiếu đã được thêm thành công!";
                } elseif (isset($_GET['update']) && $_GET['update'] == 1) {
                    echo "Suất chiếu đã được cập nhật thành công!";
                } elseif (isset($_GET['delete']) && $_GET['delete'] == 1) {
                    echo "Suất chiếu đã được xóa thành công!";
                }
                ?>
            </div>
        <?php endif; ?>


        <div id="calendar"></div>

    </div>


    <form method="POST" action="../admin/index.php?page=shows&action=create">
        <div id="popup" class="popup">

            <h3 id="popupTitle">Thêm suất chiếu</h3>

            <input type="hidden" id="showId">
            <input type="hidden" name="show_date" id="showDateField">

            <label>Phim</label>
            <select id="movieSelect" name="movie_id">
                <option value="">Chọn phim</option>

                <?php foreach ($movies as $movie): ?>
                    <option
                        value="<?= $movie['movie_id'] ?>"
                        data-duration="<?= $movie['duration_min'] ?>">
                        <?= htmlspecialchars($movie['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Phòng</label>
            <input type="hidden" name="hall_id" id="hallIdField">
            <input id="roomField" readonly>

            <label>Giờ bắt đầu</label>
            <input type="time" id="startTimeField" name="start_time" step="60">

            <label>Giờ kết thúc</label>
            <input type="time" id="endTimeField" name="end_time" readonly step="60">

            <label>Giá vé cơ bản</label>
            <input type="number" id="priceField" name="base_price">

            <div id="statusWrapper" style="display:none">

                <label>Trạng thái</label>

                <select id="statusField">
                    <option value="0">Chưa mở bán</option>
                    <option value="1">Đang mở bán</option>
                    <option value="-1">Đã kết thúc</option>
                </select>

            </div>

            <div class="popup-actions">

                <button type="button" onclick="closePopup()">Huỷ</button>

                <button id="btnSave" class="btn-save" type="submit">Lưu</button>

                <button type="button" id="btnUpdate" class="btn-save" onclick="updateShow()" style="display:none">
                    Cập nhật
                </button>

            </div>

        </div>
    </form>

</section>