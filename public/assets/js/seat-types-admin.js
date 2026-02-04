/**
 * JavaScript cho trang quản lý Loại ghế
 */

let seatTypesData = [];

// Load dữ liệu khi trang được tải
document.addEventListener('DOMContentLoaded', async function() {
    try {
        await loadSeatTypes();
        renderSeatTypesTable();
    } catch (error) {
        console.error('Lỗi khi load dữ liệu:', error);
        showAlert('Có lỗi xảy ra khi tải dữ liệu: ' + error.message, 'error');
    }
});

/**
 * Load dữ liệu loại ghế
 */
async function loadSeatTypes() {
    try {
        seatTypesData = await getAllSeatTypes();
    } catch (error) {
        throw new Error('Không thể tải dữ liệu: ' + error.message);
    }
}

/**
 * Render bảng loại ghế
 */
function renderSeatTypesTable() {
    const tbody = document.querySelector('.seat_types .data-table tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (seatTypesData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">Chưa có dữ liệu</td></tr>';
        return;
    }
    
    seatTypesData.forEach(seatType => {
        // Tính phụ thu từ PriceMultiplier
        // Giả sử giá gốc là 100,000 VNĐ
        const basePrice = 100000;
        const priceSurcharge = Math.round((seatType.PriceMultiplier - 1) * basePrice);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${seatType.SeatTypeID}</td>
            <td><strong>${seatType.TypeName}</strong></td>
            <td style="color: var(--success-color); font-weight: bold;">
                +${priceSurcharge.toLocaleString('vi-VN')} ₫
            </td>
            <td>
                <a href="#" class="btn-action" onclick="editSeatType(${seatType.SeatTypeID}); return false;">Sửa</a>
                <button class="btn-action danger" onclick="deleteSeatType(${seatType.SeatTypeID})">Xóa</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Edit seat type
 */
async function editSeatType(id) {
    try {
        const seatType = await getSeatTypeById(id);
        if (!seatType) {
            showAlert('Không tìm thấy loại ghế', 'error');
            return;
        }
        
        // Tính phụ thu từ PriceMultiplier
        const basePrice = 100000;
        const priceSurcharge = Math.round((seatType.PriceMultiplier - 1) * basePrice);
        
        openEditModal(seatType, priceSurcharge);
    } catch (error) {
        showAlert('Lỗi khi lấy thông tin loại ghế: ' + error.message, 'error');
    }
}

/**
 * Delete seat type
 */
async function deleteSeatType(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa loại ghế này?')) {
        return;
    }
    
    try {
        await deleteSeatType(id);
        showAlert('Xóa loại ghế thành công!', 'success');
        await loadSeatTypes();
        renderSeatTypesTable();
    } catch (error) {
        showAlert('Lỗi khi xóa loại ghế: ' + error.message, 'error');
    }
}

/**
 * Open edit modal
 */
function openEditModal(seatType, priceSurcharge) {
    // Tạo modal nếu chưa có
    let modal = document.getElementById('editSeatTypeModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'editSeatTypeModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Cập nhật loại ghế</h2>
                    <button class="btn-close" onclick="closeModal('editSeatTypeModal')">&times;</button>
                </div>
                <form id="editSeatTypeForm" onsubmit="handleUpdateSeatType(event); return false;">
                    <input type="hidden" name="seat_type_id" id="editSeatTypeId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tên loại ghế</label>
                            <input type="text" name="type_name" id="editTypeName" placeholder="VD: VIP, Couple" required>
                        </div>
                        <div class="form-group">
                            <label>Phụ thu (VNĐ)</label>
                            <input type="number" name="price_surcharge" id="editPriceSurcharge" value="0" required step="1000" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-action" onclick="closeModal('editSeatTypeModal')">Hủy</button>
                        <button type="submit" class="btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Điền dữ liệu
    document.getElementById('editSeatTypeId').value = seatType.SeatTypeID;
    document.getElementById('editTypeName').value = seatType.TypeName;
    document.getElementById('editPriceSurcharge').value = priceSurcharge;
    
    // Mở modal
    modal.classList.add('active');
}

/**
 * Handle update seat type
 */
async function handleUpdateSeatType(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const seatTypeId = formData.get('seat_type_id');
    
    const data = {
        type_name: formData.get('type_name'),
        price_surcharge: formData.get('price_surcharge')
    };
    
    try {
        await updateSeatType(seatTypeId, data);
        showAlert('Cập nhật loại ghế thành công!', 'success');
        closeModal('editSeatTypeModal');
        await loadSeatTypes();
        renderSeatTypesTable();
    } catch (error) {
        showAlert('Lỗi khi cập nhật: ' + error.message, 'error');
    }
}

/**
 * Handle create seat type
 */
async function handleCreateSeatType(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    const data = {
        type_name: formData.get('type_name'),
        price_surcharge: formData.get('price_surcharge')
    };
    
    try {
        await createSeatType(data);
        showAlert('Thêm loại ghế thành công!', 'success');
        closeModal('addSeatTypeModal');
        form.reset();
        await loadSeatTypes();
        renderSeatTypesTable();
    } catch (error) {
        showAlert('Lỗi khi thêm loại ghế: ' + error.message, 'error');
    }
}

/**
 * Show alert
 */
function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    const content = document.querySelector('.seat_types .dashboard-content');
    if (content) {
        content.insertBefore(alert, content.firstChild);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
}

// Override form submit cho modal thêm mới
document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.querySelector('#addSeatTypeModal form');
    if (addForm) {
        addForm.addEventListener('submit', handleCreateSeatType);
    }
});
