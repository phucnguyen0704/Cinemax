<section class="schedule-page shows">


    <div id="theaterSelectView" class="theater-select">

        <h2>Chọn rạp chiếu</h2>

        <div class="theater-list">

            <button class="theater-btn" onclick="selectTheater('CGV Vincom')">
                CGV Vincom
            </button>

            <button class="theater-btn" onclick="selectTheater('Lotte Cinema')">
                Lotte Cinema
            </button>

            <button class="theater-btn" onclick="selectTheater('Galaxy Cinema')">
                Galaxy Cinema
            </button>

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


        <div id="calendar"></div>

    </div>



    <div id="popup" class="popup">

        <h3>Thêm suất chiếu</h3>

        <label>Phim</label>
        <select id="movieSelect">
            <option>Avengers</option>
            <option>Batman</option>
            <option>Avatar</option>
        </select>

        <label>Phòng</label>
        <input id="roomField">

        <label>Giờ bắt đầu</label>
        <input type="time" id="timeField">

        <div class="popup-actions">
            <button onclick="closePopup()">Huỷ</button>
            <button class="btn-save" onclick="saveShow()">Lưu</button>
        </div>

    </div>


</section>