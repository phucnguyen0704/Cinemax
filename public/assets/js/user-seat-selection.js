/**
 * JavaScript cho trang Chọn ghế (User)
 */

let seatsData = [];
let seatTypesData = [];
let hallData = null;
let currentHallId = null;
let currentShowId = null;
let selectedSeats = [];

// Load dữ liệu khi trang được tải
document.addEventListener('DOMContentLoaded', async function() {
    // Lấy hall_id và show_id từ URL
    const urlParams = new URLSearchParams(window.location.search);
    currentHallId = urlParams.get('hall_id');
    currentShowId = urlParams.get('show_id');
    
    if (!currentHallId) {
        showError('Không tìm thấy ID phòng chiếu');
        return;
    }
    
    try {
        await loadAllData();
        renderSeatLayout();
        populateLegend();
        updateSummary();
    } catch (error) {
        console.error('Lỗi khi load dữ liệu:', error);
        showError('Có lỗi xảy ra khi tải dữ liệu sơ đồ ghế');
    }
});

/**
 * Load tất cả dữ liệu cần thiết
 */
async function loadAllData() {
    try {
        [seatsData, seatTypesData, hallData] = await Promise.all([
            getSeatsByHall(currentHallId),
            getAllSeatTypes(),
            getHallById(currentHallId)
        ]);
        
        // Cập nhật header với thông tin phòng
        updateHeader();
    } catch (error) {
        throw new Error('Không thể tải dữ liệu: ' + error.message);
    }
}

/**
 * Cập nhật header với thông tin phòng
 */
function updateHeader() {
    if (hallData) {
        const hallNameEl = document.querySelector('.hall-name, .seat-selection-header h2');
        if (hallNameEl) {
            hallNameEl.textContent = hallData.Name || 'Phòng chiếu';
        }
        
        const cinemaNameEl = document.querySelector('.cinema-name');
        if (cinemaNameEl && hallData.CinemaName) {
            cinemaNameEl.textContent = hallData.CinemaName;
        }
    }
}

/**
 * Render sơ đồ ghế
 */
function renderSeatLayout() {
    const seatGrid = document.querySelector('.seat-grid');
    const seatsContainer = document.querySelector('#seatsContainer');
    const container = seatGrid || seatsContainer;
    if (!container) return;
    
    container.innerHTML = '';
    
    if (seatsData.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 40px; color: #888;">Chưa có ghế nào trong phòng này</div>';
        return;
    }
    
    // Nhóm ghế theo hàng
    const seatsByRow = {};
    seatsData.forEach(seat => {
        const rowName = seat.RowName;
        if (!seatsByRow[rowName]) {
            seatsByRow[rowName] = [];
        }
        seatsByRow[rowName].push(seat);
    });
    
    // Sắp xếp các hàng
    const sortedRows = Object.keys(seatsByRow).sort();
    
    // Render từng hàng
    sortedRows.forEach(rowName => {
        const rowSeats = seatsByRow[rowName].sort((a, b) => a.SeatNumber - b.SeatNumber);
        
        const rowDiv = document.createElement('div');
        rowDiv.className = 'seat-row';
        
        // Label hàng
        const rowLabel = document.createElement('span');
        rowLabel.className = 'row-label';
        rowLabel.textContent = rowName;
        rowDiv.appendChild(rowLabel);
        
        // Render ghế trong hàng
        rowSeats.forEach(seat => {
            const seatItem = document.createElement('div');
            seatItem.className = `seat type-${seat.SeatTypeID}`;
            seatItem.setAttribute('data-seat-id', seat.SeatID);
            seatItem.setAttribute('data-seat-name', `${rowName}${seat.SeatNumber}`);
            seatItem.setAttribute('data-price', calculatePrice(seat.PriceMultiplier));
            seatItem.textContent = seat.SeatNumber;
            seatItem.title = `${rowName}${seat.SeatNumber} - ${seat.TypeName}`;
            
            // Kiểm tra trạng thái ghế
            if (seat.isBooking == 1) {
                seatItem.classList.add('occupied', 'sold');
            }
            
            // Click handler
            seatItem.onclick = function() {
                toggleSeat(seatItem);
            };
            
            rowDiv.appendChild(seatItem);
        });
        
        container.appendChild(rowDiv);
    });
}

/**
 * Calculate price based on multiplier
 */
function calculatePrice(multiplier) {
    const basePrice = 100000; // Giá gốc
    return Math.round(basePrice * multiplier);
}

/**
 * Toggle seat selection
 */
function toggleSeat(element) {
    // Chặn click nếu ghế đã bán hoặc không khả dụng
    if (element.classList.contains('occupied') || 
        element.classList.contains('sold') || 
        element.classList.contains('held') || 
        element.classList.contains('reserved')) {
        return;
    }
    
    const seatId = element.getAttribute('data-seat-id');
    const seatName = element.getAttribute('data-seat-name');
    const price = parseFloat(element.getAttribute('data-price'));
    
    // Xử lý logic Chọn / Bỏ chọn
    if (element.classList.contains('selected')) {
        // Bỏ chọn
        element.classList.remove('selected');
        selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    } else {
        // Chọn mới
        const MAX_SEATS = 8;
        if (selectedSeats.length >= MAX_SEATS) {
            alert(`Bạn chỉ được chọn tối đa ${MAX_SEATS} ghế.`);
            return;
        }
        element.classList.add('selected');
        selectedSeats.push({ id: seatId, name: seatName, price: price });
    }
    
    // Cập nhật giao diện
    updateSummary();
}

/**
 * Update summary (danh sách ghế đã chọn & tổng tiền)
 */
function updateSummary() {
    const container = document.getElementById('selectedSeats');
    const hiddenInputs = document.getElementById('hiddenInputs');
    const btnContinue = document.getElementById('btnContinue');
    const totalPriceEl = document.getElementById('totalPrice');
    
    if (!container) return;
    
    // Xóa nội dung cũ
    if (container) container.innerHTML = '';
    if (hiddenInputs) hiddenInputs.innerHTML = '';
    
    // Nếu chưa chọn ghế nào
    if (selectedSeats.length === 0) {
        if (container) {
            container.innerHTML = '<p class="empty-message">Chưa chọn ghế</p>';
        }
        if (btnContinue) {
            btnContinue.disabled = true;
            btnContinue.style.opacity = '0.5';
            btnContinue.style.cursor = 'not-allowed';
        }
        if (totalPriceEl) {
            totalPriceEl.textContent = '0 ₫';
        }
        return;
    }
    
    // Vẽ lại danh sách ghế đã chọn
    let total = 0;
    selectedSeats.forEach(seat => {
        // Hiển thị thẻ tag
        const tag = document.createElement('div');
        tag.className = 'seat-tag';
        tag.style.cssText = "background: #333; padding: 5px 10px; border-radius: 15px; font-size: 13px; display: flex; align-items: center; gap: 5px; border: 1px solid #555; color: #fff; margin-bottom: 5px;";
        tag.innerHTML = `${seat.name} <span style="cursor:pointer; color:#ff4444; font-weight:bold; margin-left:5px;" onclick="removeSeat('${seat.id}')">×</span>`;
        container.appendChild(tag);
        
        // Tạo input ẩn để gửi Form
        if (hiddenInputs) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'seat_ids[]';
            input.value = seat.id;
            hiddenInputs.appendChild(input);
        }
        
        total += seat.price;
    });
    
    // Cập nhật tổng tiền
    if (totalPriceEl) {
        totalPriceEl.textContent = total.toLocaleString('vi-VN') + ' ₫';
    }
    
    // Mở khóa nút tiếp tục
    if (btnContinue) {
        btnContinue.disabled = false;
        btnContinue.style.opacity = '1';
        btnContinue.style.cursor = 'pointer';
    }
}

/**
 * Remove seat from selection
 */
function removeSeat(seatId) {
    // Tìm ghế trên màn hình và bỏ class selected
    const seatEl = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
    if (seatEl) {
        seatEl.classList.remove('selected');
    }
    // Xóa khỏi mảng dữ liệu
    selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    updateSummary();
}

/**
 * Populate legend
 */
function populateLegend() {
    const legend = document.querySelector('.legend, .seat-legend');
    if (!legend) return;
    
    // Lưu phần hướng dẫn nếu có
    const existingItems = Array.from(legend.querySelectorAll('.legend-item'));
    let instructionText = '👉 Click để chọn ghế';
    existingItems.forEach(item => {
        if (item.textContent.includes('Click')) {
            instructionText = item.textContent;
        }
    });
    
    legend.innerHTML = '';
    
    // Thêm legend cho từng loại ghế
    seatTypesData.forEach(seatType => {
        const legendItem = document.createElement('div');
        legendItem.className = 'legend-item';
        const dotClass = legend.classList.contains('seat-legend') ? 'seat-demo' : 'seat-legend-dot';
        legendItem.innerHTML = `
            <div class="${dotClass} type-${seatType.SeatTypeID}"></div>
            <span>${seatType.TypeName}</span>
        `;
        legend.appendChild(legendItem);
    });
    
    // Thêm các trạng thái
    const statuses = [
        { class: 'selected', text: 'Đang chọn' },
        { class: 'sold', text: 'Đã bán' },
        { class: 'held', text: 'Đang giữ' }
    ];
    
    statuses.forEach(status => {
        const legendItem = document.createElement('div');
        legendItem.className = 'legend-item';
        const dotClass = legend.classList.contains('seat-legend') ? 'seat-demo' : 'seat-legend-dot';
        legendItem.innerHTML = `
            <div class="${dotClass} ${status.class}"></div>
            <span>${status.text}</span>
        `;
        legend.appendChild(legendItem);
    });
    
    // Thêm phần hướng dẫn
    const instructionItem = document.createElement('div');
    instructionItem.className = 'legend-item';
    instructionItem.style.cssText = 'margin-left: 15px; border-left: 1px solid #444; padding-left: 15px;';
    instructionItem.textContent = instructionText;
    legend.appendChild(instructionItem);
}

/**
 * Show error message
 */
function showError(message) {
    const container = document.querySelector('.seat-selection-container, .booking-container');
    if (container) {
        container.innerHTML = `<div style="text-align: center; padding: 40px; color: #e50914;">${message}</div>`;
    }
}
