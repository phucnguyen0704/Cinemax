/**
 * JavaScript cho trang quản lý Phòng chiếu
 */

let hallsData = [];
let cinemasData = [];
let statusesData = [];

// Load dữ liệu khi trang được tải
document.addEventListener('DOMContentLoaded', async function() {
    try {
        await loadAllData();
        renderHallsTable();
        populateCinemaFilter();
        populateCinemaSelect();
        populateStatusSelect();
        
        // Setup modal open handler để populate selects
        setupModalHandlers();
    } catch (error) {
        console.error('Lỗi khi load dữ liệu:', error);
        showAlert('Có lỗi xảy ra khi tải dữ liệu: ' + error.message, 'error');
    }
});

/**
 * Setup handlers cho modal
 */
function setupModalHandlers() {
    // Override openModal function để populate selects khi mở modal thêm mới
    const originalOpenModal = window.openModal;
    if (originalOpenModal) {
        window.openModal = function(modalId) {
            originalOpenModal(modalId);
            if (modalId === 'addScreenModal') {
                setTimeout(() => {
                    populateCinemaSelect();
                    populateStatusSelect();
                }, 100);
            }
        };
    }
}

/**
 * Load tất cả dữ liệu cần thiết
 */
async function loadAllData() {
    try {
        [hallsData, cinemasData, statusesData] = await Promise.all([
            getAllHalls(),
            getAllCinemas(),
            getAllHallStatuses()
        ]);
    } catch (error) {
        throw new Error('Không thể tải dữ liệu: ' + error.message);
    }
}

/**
 * Render bảng phòng chiếu
 */
function renderHallsTable() {
    const tbody = document.querySelector('.halls .data-table tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (hallsData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Chưa có dữ liệu</td></tr>';
        return;
    }
    
    hallsData.forEach(hall => {
        const cinema = cinemasData.find(c => c.CinemaID == hall.CinemaID);
        const status = statusesData.find(s => s.StatusID == hall.StatusID);
        
        // Màu trạng thái
        let statusColor = '#666';
        let statusText = status ? status.StatusName : 'N/A';
        if (status) {
            if (status.StatusName.includes('Hoạt động') || status.StatusName.includes('hoạt động')) {
                statusColor = '#46d369';
            } else if (status.StatusName.includes('Bảo trì') || status.StatusName.includes('bảo trì')) {
                statusColor = '#ffa500';
            } else if (status.StatusName.includes('Tạm dừng') || status.StatusName.includes('tạm dừng')) {
                statusColor = '#e50914';
            }
        }
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${hall.HallID}</td>
            <td>${cinema ? cinema.Name : 'N/A'}</td>
            <td><strong>${hall.Name}</strong></td>
            <td><span style="color: ${statusColor}; font-weight: bold;">${statusText}</span></td>
            <td>${hall.SeatCount || 0} ghế</td>
            <td>
                <a href="index.php?page=seats&hall_id=${hall.HallID}" class="btn-action" style="color: #46d369; border-color: #46d369;" title="Cấu hình ghế">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <path d="M3 9h18"></path>
                        <path d="M9 21V9"></path>
                    </svg>
                    Cấu hình ghế
                </a>
                <a href="#" class="btn-action" onclick="editHall(${hall.HallID}); return false;" title="Sửa">Sửa</a>
                <button class="btn-action danger" onclick="deleteHallHandler(${hall.HallID})" title="Xóa">Xóa</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Populate filter dropdown
 */
function populateCinemaFilter() {
    const select = document.querySelector('.halls .filter-select[name="theater_id"]');
    if (!select) return;
    
    // Giữ option đầu tiên
    const firstOption = select.querySelector('option[value=""]');
    select.innerHTML = '';
    if (firstOption) {
        select.appendChild(firstOption);
    }
    
    cinemasData.forEach(cinema => {
        const option = document.createElement('option');
        option.value = cinema.CinemaID;
        option.textContent = cinema.Name;
        select.appendChild(option);
    });
}

/**
 * Populate cinema select trong modal
 */
function populateCinemaSelect() {
    const selects = document.querySelectorAll('#addScreenModal select[name="cinema_id"], #editHallModal select[name="cinema_id"]');
    if (selects.length === 0) return;
    
    selects.forEach(select => {
        if (!select) return;
        
        const currentValue = select.value; // Giữ giá trị hiện tại nếu có
        select.innerHTML = '<option value="">-- Chọn rạp --</option>';
        cinemasData.forEach(cinema => {
            const option = document.createElement('option');
            option.value = cinema.CinemaID;
            option.textContent = cinema.Name;
            select.appendChild(option);
        });
        
        // Khôi phục giá trị nếu có
        if (currentValue) {
            select.value = currentValue;
        }
    });
}

/**
 * Populate status select trong modal
 */
function populateStatusSelect() {
    const selects = document.querySelectorAll('#addScreenModal select[name="status_id"], #editHallModal select[name="status_id"]');
    if (selects.length === 0) return;
    
    selects.forEach(select => {
        if (!select) return;
        
        const currentValue = select.value; // Giữ giá trị hiện tại nếu có
        select.innerHTML = '<option value="">-- Chọn trạng thái --</option>';
        statusesData.forEach(status => {
            const option = document.createElement('option');
            option.value = status.StatusID;
            option.textContent = status.StatusName;
            select.appendChild(option);
        });
        
        // Khôi phục giá trị nếu có
        if (currentValue) {
            select.value = currentValue;
        }
    });
}

/**
 * Filter halls theo cinema
 */
async function filterHallsByCinema(cinemaId) {
    try {
        // Hiển thị loading
        const tbody = document.querySelector('.halls .data-table tbody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;"><div class="loading">Đang tải dữ liệu...</div></td></tr>';
        }
        
        if (cinemaId) {
            hallsData = await getAllHalls(cinemaId);
        } else {
            hallsData = await getAllHalls();
        }
        renderHallsTable();
        
        // Cập nhật filter select
        const filterSelect = document.getElementById('cinemaFilter');
        if (filterSelect) {
            filterSelect.value = cinemaId || '';
        }
    } catch (error) {
        showAlert('Lỗi khi lọc dữ liệu: ' + error.message, 'error');
        // Load lại tất cả nếu có lỗi
        try {
            hallsData = await getAllHalls();
            renderHallsTable();
        } catch (e) {
            console.error('Error reloading data:', e);
        }
    }
}

/**
 * Edit hall
 */
async function editHall(hallId) {
    try {
        const hall = await getHallById(hallId);
        if (!hall) {
            showAlert('Không tìm thấy phòng chiếu', 'error');
            return;
        }
        
        // Tạo hoặc mở modal edit
        openEditModal(hall);
    } catch (error) {
        showAlert('Lỗi khi lấy thông tin phòng chiếu: ' + error.message, 'error');
    }
}

/**
 * Delete hall (handler function)
 */
async function deleteHallHandler(hallId) {
    if (!confirm('CẢNH BÁO: Xóa phòng sẽ xóa luôn sơ đồ ghế của phòng này?')) {
        return;
    }
    
    try {
        // Sử dụng deleteHall từ api.js
        await deleteHall(hallId);
        showAlert('Xóa phòng chiếu thành công!', 'success');
        await loadAllData();
        renderHallsTable();
    } catch (error) {
        showAlert('Lỗi khi xóa phòng chiếu: ' + error.message, 'error');
    }
}

/**
 * Show alert
 */
function showAlert(message, type = 'success') {
    // Tạo alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Thêm vào dashboard-content
    const content = document.querySelector('.halls .dashboard-content');
    if (content) {
        content.insertBefore(alert, content.firstChild);
        
        // Tự động xóa sau 3 giây
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
}

/**
 * Open edit modal
 */
function openEditModal(hall) {
    // Tạo modal nếu chưa có
    let modal = document.getElementById('editHallModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'editHallModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Cập nhật phòng chiếu</h2>
                    <button class="btn-close" onclick="closeModal('editHallModal')">&times;</button>
                </div>
                <form id="editHallForm" onsubmit="handleUpdateHall(event); return false;">
                    <input type="hidden" name="hall_id" id="editHallId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Chọn Rạp</label>
                            <select name="cinema_id" id="editCinemaId" required></select>
                        </div>
                        <div class="form-group">
                            <label>Tên phòng</label>
                            <input type="text" name="name" id="editHallName" placeholder="VD: Phòng 1, Phòng IMAX" required>
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="status_id" id="editStatusId" required></select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-action" onclick="closeModal('editHallModal')">Hủy</button>
                        <button type="submit" class="btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Populate selects ngay sau khi modal được tạo
        populateCinemaSelect();
        populateStatusSelect();
        
        // Điền dữ liệu sau khi selects đã được populate
        setTimeout(() => {
            const editHallId = document.getElementById('editHallId');
            const editCinemaId = document.getElementById('editCinemaId');
            const editHallName = document.getElementById('editHallName');
            const editStatusId = document.getElementById('editStatusId');
            
            if (editHallId) editHallId.value = hall.HallID;
            if (editCinemaId) editCinemaId.value = hall.CinemaID;
            if (editHallName) editHallName.value = hall.Name;
            if (editStatusId) editStatusId.value = hall.StatusID;
        }, 100);
    } else {
        // Modal đã tồn tại, chỉ cần populate và điền dữ liệu
        populateCinemaSelect();
        populateStatusSelect();
        
        setTimeout(() => {
            const editHallId = document.getElementById('editHallId');
            const editCinemaId = document.getElementById('editCinemaId');
            const editHallName = document.getElementById('editHallName');
            const editStatusId = document.getElementById('editStatusId');
            
            if (editHallId) editHallId.value = hall.HallID;
            if (editCinemaId) editCinemaId.value = hall.CinemaID;
            if (editHallName) editHallName.value = hall.Name;
            if (editStatusId) editStatusId.value = hall.StatusID;
        }, 100);
    }
    
    // Mở modal
    modal.classList.add('active');
}

/**
 * Handle update hall
 */
async function handleUpdateHall(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const hallId = formData.get('hall_id');
    
    if (!hallId) {
        showAlert('Lỗi: Không tìm thấy ID phòng chiếu', 'error');
        return;
    }
    
    const cinemaId = formData.get('cinema_id');
    const name = formData.get('name');
    const statusId = formData.get('status_id');
    
    if (!cinemaId || !name || !statusId) {
        showAlert('Vui lòng điền đầy đủ thông tin', 'error');
        return;
    }
    
    const data = {
        cinema_id: cinemaId,
        name: name.trim(),
        status_id: statusId
    };
    
    // Validation
    if (data.name.length > 50) {
        showAlert('Tên phòng chiếu không được vượt quá 50 ký tự', 'error');
        return;
    }
    
    try {
        await updateHall(hallId, data);
        showAlert('Cập nhật phòng chiếu thành công!', 'success');
        closeModal('editHallModal');
        await loadAllData();
        renderHallsTable();
    } catch (error) {
        console.error('Update hall error:', error);
        showAlert('Lỗi khi cập nhật: ' + error.message, 'error');
    }
}

/**
 * Handle create hall
 */
async function handleCreateHall(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const form = event.target;
    if (!form) {
        console.error('Form not found');
        return;
    }
    
    const formData = new FormData(form);
    
    const cinemaId = formData.get('cinema_id');
    const name = formData.get('name');
    const statusId = formData.get('status_id');
    
    console.log('Creating hall with data:', { cinemaId, name, statusId });
    
    if (!cinemaId || !name || !statusId) {
        showAlert('Vui lòng điền đầy đủ thông tin (Rạp, Tên phòng, Trạng thái)', 'error');
        return;
    }
    
    const data = {
        cinema_id: cinemaId,
        name: name.trim(),
        status_id: statusId
    };
    
    // Validation
    if (data.name.length === 0) {
        showAlert('Tên phòng chiếu không được để trống', 'error');
        return;
    }
    
    if (data.name.length > 50) {
        showAlert('Tên phòng chiếu không được vượt quá 50 ký tự', 'error');
        return;
    }
    
    // Disable submit button để tránh double submit
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang thêm...';
    }
    
    try {
        const result = await createHall(data);
        console.log('Create hall result:', result);
        
        showAlert('Thêm phòng chiếu thành công!', 'success');
        closeModal('addScreenModal');
        form.reset();
        
        // Reset selects
        populateCinemaSelect();
        populateStatusSelect();
        
        // Reload data
        await loadAllData();
        renderHallsTable();
    } catch (error) {
        console.error('Create hall error:', error);
        showAlert('Lỗi khi thêm phòng chiếu: ' + (error.message || 'Có lỗi xảy ra'), 'error');
    } finally {
        // Re-enable submit button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Thêm';
        }
    }
}

// Setup form handlers
document.addEventListener('DOMContentLoaded', function() {
    // Setup add form - chỉ add listener một lần
    const addForm = document.querySelector('#addScreenModal form');
    if (addForm) {
        // Remove existing listeners nếu có
        const newForm = addForm.cloneNode(true);
        addForm.parentNode.replaceChild(newForm, addForm);
        // Add listener mới
        document.querySelector('#addScreenModal form').addEventListener('submit', handleCreateHall);
    }
    
    // Setup filter form
    const filterForm = document.querySelector('.halls .filter-bar');
    if (filterForm) {
        const select = filterForm.querySelector('select[name="theater_id"]');
        if (select) {
            select.addEventListener('change', function() {
                filterHallsByCinema(this.value || null);
            });
        }
    }
    
    // Populate selects khi mở modal thêm mới
    const addModal = document.getElementById('addScreenModal');
    if (addModal) {
        // Sử dụng MutationObserver hoặc event listener để detect khi modal mở
        // Hoặc đơn giản hơn: populate khi click nút mở modal
        const addButton = document.querySelector('.halls .btn-add');
        if (addButton) {
            addButton.addEventListener('click', function() {
                setTimeout(() => {
                    populateCinemaSelect();
                    populateStatusSelect();
                }, 100);
            });
        }
    }
});
