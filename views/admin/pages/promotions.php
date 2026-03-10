<?php
$search = $_GET['q'] ?? '';
$promotions = $promotionService->listPromotions($search);

$successMessage = $_GET['success'] ?? '';
$errorMessage   = $_GET['error'] ?? '';
$openModal      = $_GET['open_modal'] ?? '';
$editId         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editPromotion  = null;

if ($openModal === 'edit' && $editId > 0) {
    try {
        $editPromotion = $promotionService->getPromotionDetail($editId);
    } catch (Throwable $e) {
        $errorMessage = $e->getMessage();
    }
}

function renderPromotionStatusLabel($status)
{
    $baseStyle = 'display:inline-flex;align-items:center;justify-content:center;padding:6px 12px;border-radius:999px;font-size:13px;font-weight:600;white-space:nowrap;min-width:135px;text-align:center;line-height:1.2;';

    if ($status === 'active') {
        return '<span style="' . $baseStyle . 'background:#16351f;color:#b7f7c8;border:1px solid #2d7a43;">Đang kích hoạt</span>';
    }

    if ($status === 'pending') {
        return '<span style="' . $baseStyle . 'background:#4a3512;color:#ffd58a;border:1px solid #b37b18;">Chưa kích hoạt</span>';
    }

    return '<span style="' . $baseStyle . 'background:#3a1616;color:#ffb3b3;border:1px solid #a94442;">Đã hết hạn</span>';
}
?>

<section class="promotions">

    <header class="admin-header">
        <h1>Quản lý Khuyến mãi</h1>
        <div class="header-actions">
            <button class="btn-add" onclick="openModal('addPromoModal')">
                <i class="fas fa-plus"></i>
                <span>Thêm mã mới</span>
            </button>

            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
    </header>

    <?php if ($successMessage): ?>
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#123524;color:#b8ffd2;border:1px solid #1f7a4d;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#3a1616;color:#ffd0d0;border:1px solid #a33;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-content">
        <div class="dashboard-card">

            <form method="GET" action="index.php" class="filter-bar" style="margin-bottom:16px;">
                <input type="hidden" name="page" value="promotions">

                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Tìm mã hoặc tên khuyến mãi..."
                    style="padding:8px;border-radius:4px;border:1px solid #444;background:#222;color:#fff;min-width:260px;">

                <button type="submit" class="btn-primary">Tìm</button>
                <a href="index.php?page=promotions" class="btn-action">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="min-width:70px;">ID</th>
                            <th style="min-width:140px;">Mã Code</th>
                            <th style="min-width:180px;">Tên khuyến mãi</th>
                            <th style="min-width:110px;">Loại giảm</th>
                            <th style="min-width:140px;">Giá trị giảm</th>
                            <th style="min-width:150px;">Đơn tối thiểu</th>
                            <th style="min-width:120px;">Ngày bắt đầu</th>
                            <th style="min-width:120px;">Ngày kết thúc</th>
                            <th style="min-width:170px; text-align:center;">Trạng thái</th>
                            <th style="min-width:150px; text-align:center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($promotions)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Chưa có khuyến mãi nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($promotions as $p): ?>
                                <tr>
                                    <td>#<?= (int)$p['promotion_id'] ?></td>
                                    <td><strong><?= htmlspecialchars($p['code']) ?></strong></td>
                                    <td><?= htmlspecialchars($p['name'] ?? '') ?></td>
                                    <td><?= ($p['discount_type'] ?? 'percent') === 'fixed' ? 'VNĐ' : '%' ?></td>
                                    <td>
                                        <?php if (($p['discount_type'] ?? 'percent') === 'fixed'): ?>
                                            <?= number_format((float)$p['discount_value'], 2, ',', '.') ?> VNĐ
                                        <?php else: ?>
                                            <?= number_format((float)$p['discount_value'], 2, ',', '.') ?> %
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format((float)($p['min_amount'] ?? 0), 2, ',', '.') ?> VNĐ</td>
                                    <td><?= !empty($p['start_date']) ? date('d/m/Y', strtotime($p['start_date'])) : '' ?></td>
                                    <td><?= !empty($p['end_date']) ? date('d/m/Y', strtotime($p['end_date'])) : '' ?></td>
                                    <td style="text-align:center; min-width:170px;">
                                        <?= renderPromotionStatusLabel($p['computed_status'] ?? 'expired') ?>
                                    </td>
                                    <td style="white-space:nowrap; text-align:center; min-width:150px;">
                                        <a href="index.php?page=promotions&open_modal=edit&id=<?= (int)$p['promotion_id'] ?>" class="btn-action">
                                            Sửa
                                        </a>

                                        <form method="POST"
                                              action="index.php?page=promotions&action=delete&id=<?= (int)$p['promotion_id'] ?>"
                                              style="display:inline;">
                                            <button type="submit"
                                                    class="btn-action danger"
                                                    onclick="return confirm('Xóa khuyến mãi này?')">
                                                Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addPromoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm khuyến mãi mới</h2>
                <button type="button" class="btn-close" onclick="closeModal('addPromoModal')">&times;</button>
            </div>

            <form method="POST" action="index.php?page=promotions&action=create" onsubmit="return validatePromotionForm(this)">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mã Code <span style="color:#ff6b6b">*</span></label>
                        <input type="text" name="code" required maxlength="50" placeholder="VD: GIAM10">
                    </div>

                    <div class="form-group">
                        <label>Tên khuyến mãi <span style="color:#ff6b6b">*</span></label>
                        <input type="text" name="name" required maxlength="255" placeholder="VD: Giảm cuối tuần">
                    </div>

                    <div class="form-group">
                        <label>Đơn vị giảm giá <span style="color:#ff6b6b">*</span></label>
                        <select name="discount_type" required>
                            <option value="percent">%</option>
                            <option value="fixed">VNĐ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Giá trị giảm <span style="color:#ff6b6b">*</span></label>
                        <input type="number" name="discount_value" min="1" step="0.01" required placeholder="VD: 10 hoặc 5000.50">
                    </div>

                    <div class="form-group">
                        <label>Đơn tối thiểu (VNĐ)</label>
                        <input type="number" name="min_amount" min="0" step="0.01" value="0" placeholder="VD: 50000">
                    </div>

                    <div class="form-group">
                        <label>Ngày bắt đầu <span style="color:#ff6b6b">*</span></label>
                        <input
                            type="date"
                            name="start_date"
                            required
                            min="1900-01-01"
                            max="2100-12-31"
                            onclick="this.showPicker && this.showPicker()">
                    </div>

                    <div class="form-group">
                        <label>Ngày kết thúc <span style="color:#ff6b6b">*</span></label>
                        <input
                            type="date"
                            name="end_date"
                            required
                            min="1900-01-01"
                            max="2100-12-31"
                            onclick="this.showPicker && this.showPicker()">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addPromoModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($editPromotion): ?>
    <div id="editPromoModal" class="modal" style="display:<?= $openModal === 'edit' ? 'flex' : 'none' ?>;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Chỉnh sửa khuyến mãi</h2>
                <button type="button" class="btn-close" onclick="window.location.href='index.php?page=promotions'">&times;</button>
            </div>

            <form method="POST" action="index.php?page=promotions&action=update&id=<?= (int)$editPromotion['promotion_id'] ?>" onsubmit="return validatePromotionForm(this)">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mã Code <span style="color:#ff6b6b">*</span></label>
                        <input type="text" name="code" required maxlength="50" value="<?= htmlspecialchars($editPromotion['code'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Tên khuyến mãi <span style="color:#ff6b6b">*</span></label>
                        <input type="text" name="name" required maxlength="255" value="<?= htmlspecialchars($editPromotion['name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Đơn vị giảm giá <span style="color:#ff6b6b">*</span></label>
                        <select name="discount_type" required>
                            <option value="percent" <?= ($editPromotion['discount_type'] ?? 'percent') === 'percent' ? 'selected' : '' ?>>%</option>
                            <option value="fixed" <?= ($editPromotion['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>VNĐ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Giá trị giảm <span style="color:#ff6b6b">*</span></label>
                        <input type="number" name="discount_value" min="1" step="0.01" required value="<?= htmlspecialchars($editPromotion['discount_value'] ?? '1') ?>">
                    </div>

                    <div class="form-group">
                        <label>Đơn tối thiểu (VNĐ)</label>
                        <input type="number" name="min_amount" min="0" step="0.01" value="<?= htmlspecialchars($editPromotion['min_amount'] ?? '0') ?>">
                    </div>

                    <div class="form-group">
                        <label>Ngày bắt đầu <span style="color:#ff6b6b">*</span></label>
                        <input
                            type="date"
                            name="start_date"
                            required
                            min="1900-01-01"
                            max="2100-12-31"
                            value="<?= htmlspecialchars($editPromotion['start_date'] ?? '') ?>"
                            onclick="this.showPicker && this.showPicker()">
                    </div>

                    <div class="form-group">
                        <label>Ngày kết thúc <span style="color:#ff6b6b">*</span></label>
                        <input
                            type="date"
                            name="end_date"
                            required
                            min="1900-01-01"
                            max="2100-12-31"
                            value="<?= htmlspecialchars($editPromotion['end_date'] ?? '') ?>"
                            onclick="this.showPicker && this.showPicker()">
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="index.php?page=promotions" class="btn-action">Hủy</a>
                    <button type="submit" class="btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</section>

<script>
function validatePromotionForm(form) {
    const code = form.querySelector('[name="code"]')?.value.trim() || '';
    const name = form.querySelector('[name="name"]')?.value.trim() || '';
    const discountType = form.querySelector('[name="discount_type"]')?.value || '';
    const discountValue = form.querySelector('[name="discount_value"]')?.value.trim() || '';
    const minAmount = form.querySelector('[name="min_amount"]')?.value.trim() || '0';
    const startDate = form.querySelector('[name="start_date"]')?.value.trim() || '';
    const endDate = form.querySelector('[name="end_date"]')?.value.trim() || '';

    if (code === '') {
        alert('Vui lòng nhập mã code.');
        return false;
    }

    if (!/^[a-zA-Z0-9_-]+$/.test(code)) {
        alert('Mã code chỉ được chứa chữ, số, gạch dưới hoặc gạch ngang.');
        return false;
    }

    if (name === '') {
        alert('Vui lòng nhập tên khuyến mãi.');
        return false;
    }

    if (!['percent', 'fixed'].includes(discountType)) {
        alert('Vui lòng chọn đơn vị giảm giá.');
        return false;
    }

    if (discountValue === '' || isNaN(discountValue) || Number(discountValue) < 1) {
        alert('Giá trị giảm phải lớn hơn hoặc bằng 1.');
        return false;
    }

    if (discountType === 'percent' && Number(discountValue) > 100) {
        alert('Giảm theo % không được vượt quá 100.');
        return false;
    }

    if (minAmount === '' || isNaN(minAmount) || Number(minAmount) < 0) {
        alert('Đơn tối thiểu không hợp lệ.');
        return false;
    }

    if (startDate === '') {
        alert('Vui lòng chọn ngày bắt đầu.');
        return false;
    }

    if (endDate === '') {
        alert('Vui lòng chọn ngày kết thúc.');
        return false;
    }

    if (startDate > endDate) {
        alert('Ngày bắt đầu không được lớn hơn ngày kết thúc.');
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    <?php if ($openModal === 'add'): ?>
        openModal('addPromoModal');
    <?php endif; ?>

    <?php if ($openModal === 'edit' && $editPromotion): ?>
        openModal('editPromoModal');
    <?php endif; ?>
});
</script>