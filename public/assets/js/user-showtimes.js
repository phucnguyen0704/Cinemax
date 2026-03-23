/**
 * JavaScript cho trang Lịch chiếu (User)
 */

let cinemasData = [];
let hallsData = [];
let showsData = [];
let currentCinemaId = null;
let currentMovieId = null;
const defaultPosterSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='300' height='450' viewBox='0 0 300 450'><rect width='300' height='450' fill='%23111111'/><rect x='15' y='15' width='270' height='420' rx='12' fill='%231b1b1b' stroke='%23333333'/><text x='50%25' y='48%25' fill='%23999999' font-size='22' font-family='Arial, sans-serif' text-anchor='middle'>No Image</text><text x='50%25' y='55%25' fill='%23666666' font-size='14' font-family='Arial, sans-serif' text-anchor='middle'>Cinemax</text></svg>";

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
        const [allCinemas, halls, userShows] = await Promise.all([
            getAllCinemas(),
            currentCinemaId ? getAllHalls(currentCinemaId) : Promise.resolve([]),
            getUserShows({
                cinema_id: currentCinemaId || "",
                movie_id: currentMovieId || ""
            })
        ]);
        // Chỉ giữ rạp đang hoạt động cho user
        cinemasData = allCinemas.filter(cinema => String(cinema.Status) === '1');
        hallsData = halls.filter(hall => String(hall.status ?? hall.Status ?? 0) === '1');
        showsData = userShows || [];
        updateMovieHeader();
    } catch (error) {
        throw new Error('Không thể tải dữ liệu: ' + error.message);
    }
}

function normalizePosterUrl(rawUrl) {
    const posterUrl = String(rawUrl || '').trim();
    if (!posterUrl) return defaultPosterSvg;
    if (/^https?:\/\//i.test(posterUrl) || posterUrl.startsWith('data:image/')) return posterUrl;
    if (posterUrl.startsWith('/Cinemax/')) return posterUrl;
    const clean = posterUrl.replace(/^\/+/, '');
    if (clean.startsWith('public/')) return `/Cinemax/${clean}`;
    if (clean.startsWith('assets/')) return `/Cinemax/public/${clean}`;
    if (clean.startsWith('uploads/')) return `/Cinemax/public/assets/${clean}`;
    return `/Cinemax/public/assets/uploads/movies/${clean}`;
}

function updateMovieHeader() {
    const titleEl = document.getElementById('showtimesMovieTitle');
    const posterEl = document.getElementById('showtimesPoster');
    const durationEl = document.getElementById('showtimesDuration');
    const directorEl = document.getElementById('showtimesDirector');
    if (!titleEl || !posterEl || !durationEl || !directorEl) return;

    let targetShow = null;
    if (currentMovieId) {
        targetShow = showsData.find((s) => String(s.movie_id ?? s.MovieID ?? '') === String(currentMovieId)) || null;
    }
    if (!targetShow && showsData.length > 0) {
        targetShow = showsData[0];
    }

    if (!targetShow) {
        return;
    }

    const movieTitle = targetShow.movie_title ?? targetShow.MovieTitle ?? titleEl.textContent ?? 'Lịch chiếu';
    const duration = targetShow.movie_duration_min ?? targetShow.duration_min ?? '--';
    const director = targetShow.movie_director ?? targetShow.director ?? 'Đang cập nhật';
    const posterUrl = normalizePosterUrl(targetShow.movie_poster_url ?? targetShow.poster_url ?? '');

    titleEl.textContent = movieTitle;
    durationEl.textContent = String(duration);
    directorEl.textContent = String(director || 'Đang cập nhật');
    posterEl.src = posterUrl;
    posterEl.onerror = () => {
        posterEl.onerror = null;
        posterEl.src = defaultPosterSvg;
    };
}

/**
 * Render lịch chiếu
 */
function renderShowtimes() {
    const container = document.getElementById('showtimesContent');
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
    const container = document.getElementById('showtimesContent');
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
            const hallId = hall.HallID ?? hall.hall_id;
            const hallName = hall.Name ?? hall.name ?? 'Phòng';
            const hallShows = showsData.filter(show => {
                const showHallId = show.hall_id ?? show.HallID ?? show.hallId;
                return String(showHallId) === String(hallId);
            });
            html += `
                <div class="theater-block">
                    <div class="theater-name">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                        ${hallName} (${hall.SeatCount || hall.seat_count || 0} ghế)
                    </div>
                    <div class="time-list" id="timeList-${hallId}">
                        ${
                          hallShows.length
                            ? hallShows.map(show => `
                                <a href="index.php?page=seat_selection&show_id=${show.show_id ?? show.ShowID ?? ''}&hall_id=${show.hall_id ?? show.HallID ?? ''}" class="time-btn">
                                    ${String(show.start_time ?? show.StartTime ?? '').substring(0, 5)}<br>
                                    <small>${Number(show.base_price ?? show.BasePrice ?? 0).toLocaleString('vi-VN')} ₫</small>
                                </a>
                              `).join('')
                            : '<div style="text-align: center; padding: 20px; color: #888;">Chưa có suất chiếu</div>'
                        }
                    </div>
                </div>
            `;
        });
    }
    
    html += '</div>';
    container.innerHTML = html;
    
}

/**
 * Render tất cả rạp
 */
function renderAllCinemas() {
    const container = document.getElementById('showtimesContent');
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
    const container = document.getElementById('showtimesContent');
    if (container) {
        container.innerHTML = `<div style="text-align: center; padding: 40px; color: #e50914;">${message}</div>`;
    }
}
