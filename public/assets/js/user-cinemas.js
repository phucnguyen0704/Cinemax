/**
 * JavaScript cho trang Rạp chiếu (User)
 */

let cinemasData = [];

// Load dữ liệu khi trang được tải
document.addEventListener('DOMContentLoaded', async function() {
    try {
        await loadCinemas();
        renderCinemas();
    } catch (error) {
        console.error('Lỗi khi load dữ liệu:', error);
        showError('Có lỗi xảy ra khi tải dữ liệu rạp chiếu');
    }
});

/**
 * Load danh sách rạp chiếu
 */
async function loadCinemas() {
    try {
        cinemasData = await getAllCinemas();
    } catch (error) {
        throw new Error('Không thể tải dữ liệu: ' + error.message);
    }
}

/**
 * Render danh sách rạp chiếu
 */
function renderCinemas() {
    const theaterList = document.querySelector('.theater-list');
    if (!theaterList) return;
    
    theaterList.innerHTML = '';
    
    if (cinemasData.length === 0) {
        theaterList.innerHTML = '<div style="text-align: center; padding: 40px; color: #888;">Chưa có rạp chiếu nào</div>';
        return;
    }
    
    cinemasData.forEach(cinema => {
        const theaterCard = document.createElement('div');
        theaterCard.className = 'theater-card';
        theaterCard.innerHTML = `
            <h3>${cinema.Name}</h3>
            <p><strong>Địa chỉ:</strong> ${cinema.Address}</p>
            <p><strong>Thành phố:</strong> ${cinema.LocationName || 'N/A'}</p>
            <p><strong>Số phòng:</strong> ${cinema.HallCount || 0}</p>
            <a href="index.php?page=showtimes&cinema_id=${cinema.CinemaID}" class="btn-primary">
                Xem lịch chiếu
            </a>
        `;
        theaterList.appendChild(theaterCard);
    });
}

/**
 * Show error message
 */
function showError(message) {
    const theaterList = document.querySelector('.theater-list');
    if (theaterList) {
        theaterList.innerHTML = `<div style="text-align: center; padding: 40px; color: #e50914;">${message}</div>`;
    }
}
