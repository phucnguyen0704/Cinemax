/**
 * JavaScript cho trang Lịch chiếu (User)
 */

let cinemasData = [];
let hallsData = [];
let showsData = [];
let currentCinemaId = null;
let currentMovieId = null;

// Load dữ liệu khi trang được tải
document.addEventListener('DOMContentLoaded', async function() {
    // Lấy cinema_id và movie_id từ URL
    const urlParams = new URLSearchParams(window.location.search);
    currentCinemaId = urlParams.get('cinema_id');
    currentMovieId = urlParams.get('movie_id');
    
    try {
        await loadAllData();
        renderShowtimes();
    } catch (error) {
        console.error('Lỗi khi load dữ liệu:', error);
        showError('Có lỗi xảy ra khi tải dữ liệu lịch chiếu');
    }
});

/**
 * Load tất cả dữ liệu cần thiết
 */
async function loadAllData() {
    try {
        // Load cinemas và halls
        const [allCinemas, halls] = await Promise.all([
            getAllCinemas(),
            currentCinemaId ? getAllHalls(currentCinemaId) : Promise.resolve([])
        ]);
        // Chỉ giữ rạp đang hoạt động cho user
        cinemasData = allCinemas.filter(cinema => String(cinema.Status) === '1');
        hallsData = halls;
        
        // TODO: Load shows từ API (cần tạo API cho shows)
        // showsData = await getShowsByCinemaAndMovie(currentCinemaId, currentMovieId);
        
        // Tạm thời dùng dữ liệu mẫu
        showsData = [];
    } catch (error) {
        throw new Error('Không thể tải dữ liệu: ' + error.message);
    }
}

/**
 * Render lịch chiếu
 */
function renderShowtimes() {
    const container = document.querySelector('.showtimes .container');
    if (!container) return;
    
    // Nếu có cinema_id, hiển thị theo rạp
    if (currentCinemaId) {
        const cinema = cinemasData.find(c => c.CinemaID == currentCinemaId);
        if (cinema) {
            renderShowtimesByCinema(cinema, hallsData);
        }
    } else {
        // Hiển thị tất cả rạp
        renderAllCinemas();
    }
}

/**
 * Render lịch chiếu theo rạp
 */
function renderShowtimesByCinema(cinema, halls) {
    const container = document.querySelector('.showtimes .container');
    if (!container) return;
    
    let html = `
        <div class="section-header">
            <h2>Lịch chiếu - ${cinema.Name}</h2>
            <p style="color: #888; margin-top: 5px;">${cinema.Address}</p>
        </div>
        
        <div class="date-section">
            <h3 class="date-header">📅 Ngày: ${formatDate(new Date())}</h3>
    `;
    
    if (halls.length === 0) {
        html += '<div style="text-align: center; padding: 40px; color: #888;">Chưa có phòng chiếu</div>';
    } else {
        halls.forEach(hall => {
            html += `
                <div class="theater-block">
                    <div class="theater-name">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                        ${hall.Name} (${hall.SeatCount || 0} ghế)
                    </div>
                    <div class="time-list" id="timeList-${hall.HallID}">
                        <div style="text-align: center; padding: 20px; color: #888;">Chưa có suất chiếu</div>
                    </div>
                </div>
            `;
        });
    }
    
    html += '</div>';
    container.innerHTML = html;
    
    // TODO: Load và render shows cho từng phòng
    // halls.forEach(hall => {
    //     loadShowsForHall(hall.HallID);
    // });
}

/**
 * Render tất cả rạp
 */
function renderAllCinemas() {
    const container = document.querySelector('.showtimes .container');
    if (!container) return;
    
    let html = '<div class="section-header"><h2>Chọn rạp chiếu</h2></div>';
    html += '<div class="theater-list">';
    
    if (cinemasData.length === 0) {
        html += '<div style="text-align: center; padding: 40px; color: #888;">Chưa có rạp chiếu nào</div>';
    } else {
        cinemasData.forEach(cinema => {
            html += `
                <div class="theater-card">
                    <h3>${cinema.Name}</h3>
                    <p><strong>Địa chỉ:</strong> ${cinema.Address}</p>
                    <p><strong>Số phòng:</strong> ${cinema.HallCount || 0}</p>
                    <a href="index.php?page=showtimes&cinema_id=${cinema.CinemaID}" class="btn-primary">
                        Xem lịch chiếu
                    </a>
                </div>
            `;
        });
    }
    
    html += '</div>';
    container.innerHTML = html;
}

/**
 * Format date
 */
function formatDate(date) {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

/**
 * Show error message
 */
function showError(message) {
    const container = document.querySelector('.showtimes .container');
    if (container) {
        container.innerHTML = `<div style="text-align: center; padding: 40px; color: #e50914;">${message}</div>`;
    }
}
