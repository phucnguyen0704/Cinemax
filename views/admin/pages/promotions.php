<?php
$search = $_GET['q'] ?? '';
$promotions = $promotionService->listPromotions($search);

$successMessage = $_GET['success'] ?? '';
$errorMessage   = $_GET['error'] ?? '';
$openModal      = $_GET['open_modal'] ?? '';
$editId         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editPromotion  = null;

/**
 * Fallback để tránh fatal error nếu chưa load permissionConfig.php
 */
if (!function_exists('hasPermission')) {
    function hasPermission($permission)
    {
        return true;
    }
}

/**
 * Session flash cho form create/edit
 * Cần action create/update set các session này trước khi redirect về page promotions
 */
$addErrors = $_SESSION['promotion_add_errors'] ?? [];
$addOld    = $_SESSION['promotion_add_old'] ?? [];

$editErrors = $_SESSION['promotion_edit_errors'] ?? [];
$editOld    = $_SESSION['promotion_edit_old'] ?? [];
$editOldId  = isset($_SESSION['promotion_edit_id']) ? (int)$_SESSION['promotion_edit_id'] : 0;

unset(
    $_SESSION['promotion_add_errors'],
    $_SESSION['promotion_add_old'],
    $_SESSION['promotion_edit_errors'],
    $_SESSION['promotion_edit_old'],
    $_SESSION['promotion_edit_id']
);

if (($openModal === 'edit' && $editId > 0) || $editOldId > 0) {
    $targetEditId = $editOldId > 0 ? $editOldId : $editId;

    try {
        $editPromotion = $promotionService->getPromotionDetail($targetEditId);

        // nếu có old edit thì đè lên dữ liệu DB để giữ lại input cũ
        if (!empty($editOld)) {
            $editPromotion = array_merge($editPromotion, $editOld);
        }
    } catch (Throwable $e) {
        $errorMessage = $e->getMessage();
    }
}

function renderPromotionStatusLabel($status)
{
    $baseStyle = 'display:inline-flex;align-items:center;justify-content:center;padding:6px 12px;border-radius:999px;font-size:13px;font-weight:600;white-space:nowrap;min-width:135px;text-align:center;line-height:1.2;';

    if ($status === 'active') {
        return '<span style="' . $baseStyle . 'background:#16351f;color:#b7f7c8;border:1px solid #2d7a43;">Đang áp dụng</span>';
    }

    if ($status === 'scheduled') {
        return '<span style="' . $baseStyle . 'background:#4a3512;color:#ffd58a;border:1px solid #b37b18;">Sắp diễn ra</span>';
    }

    return '<span style="' . $baseStyle . 'background:#3a1616;color:#ffb3b3;border:1px solid #a94442;">Đã hết hạn</span>';
}

function fieldError($errors, $field)
{
    if (!empty($errors[$field])) {
        return '<div class="field-error">' . htmlspecialchars($errors[$field]) . '</div>';
    }
    return '';
}

function fieldClass($errors, $field)
{
    return !empty($errors[$field]) ? 'input-error' : '';
}

function oldValue($old, $field, $default = '')
{
    return htmlspecialchars((string)($old[$field] ?? $default));
}

function selectedValue($old, $field, $value, $default = '')
{
    $current = (string)($old[$field] ?? $default);
    return $current === (string)$value ? 'selected' : '';
}
?>

<style>
    .field-error {
        margin-top: 6px;
        color: #ff8f8f;
        font-size: 13px;
        line-height: 1.4;
    }

    .input-error {
        border: 1px solid #ff5f5f !important;
        box-shadow: 0 0 0 1px rgba(255, 95, 95, 0.15);
    }

    .form-general-error {
        margin-bottom: 16px;
        padding: 12px 14px;
        border-radius: 8px;
        background: #3a1616;
        color: #ffd0d0;
        border: 1px solid #a33;
    }

    .btn-action.disabled {
        pointer-events: none;
        opacity: 0.55;
        cursor: not-allowed;
    }
</style>

<section class="promotions">

    <header class="admin-header">
        <h1>Quản lý Khuyến mãi</h1>

        <div class="header-actions">
            <?php if (hasPermission('promotions_create')): ?>
                <button class="btn-add" onclick="openModal('addPromoModal')">
                    <i class="fas fa-plus"></i>
                    <span>Thêm mã mới</span>
                </button>
            <?php endif; ?>
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
                            <th style="min-width:180px; text-align:center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($promotions)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Chưa có khuyến mãi nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($promotions as $p): ?>
                                <?php $computedStatus = $p['computed_status'] ?? 'expired'; ?>
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
                                        <?= renderPromotionStatusLabel($computedStatus) ?>
                                    </td>
                                    <td style="white-space:nowrap; text-align:center; min-width:180px;">
                                        <?php if (hasPermission('promotions_update')): ?>
                                            <?php if ($computedStatus === 'expired'): ?>
                                                <span class="btn-action disabled" title="Khuyến mãi đã hết hạn, không được chỉnh sửa">
                                                    Sửa
                                                </span>
                                            <?php else: ?>
                                                <a href="index.php?page=promotions&open_modal=edit&id=<?= (int)$p['promotion_id'] ?>" class="btn-action">
                                                    Sửa
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (hasPermission('promotions_delete')): ?>
                                            <form method="POST"
                                                action="index.php?page=promotions&action=delete&id=<?= (int)$p['promotion_id'] ?>"
                                                style="display:inline;">
                                                <button type="submit"
                                                    class="btn-action danger"
                                                    onclick="return confirm('Xóa khuyến mãi này?')">
                                                    Xóa
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (hasPermission('promotions_create')): ?>
        <div id="addPromoModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Thêm khuyến mãi mới</h2>
                    <button type="button" class="btn-close" onclick="closeModal('addPromoModal')">&times;</button>
                </div>

                <form method="POST" action="index.php?page=promotions&action=create" onsubmit="return validatePromotionForm(this)">
                    <div class="modal-body">
                        <?php if (!empty($addErrors['general'])): ?>
                            <div class="form-general-error"><?= htmlspecialchars($addErrors['general']) ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Mã Code <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="text"
                                name="code"
                                required
                                maxlength="50"
                                placeholder="VD: GIAM10"
                                value="<?= oldValue($addOld, 'code') ?>"
                                class="<?= fieldClass($addErrors, 'code') ?>">
                            <?= fieldError($addErrors, 'code') ?>
                        </div>

                        <div class="form-group">
                            <label>Tên khuyến mãi <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="text"
                                name="name"
                                required
                                maxlength="255"
                                placeholder="VD: Giảm cuối tuần"
                                value="<?= oldValue($addOld, 'name') ?>"
                                class="<?= fieldClass($addErrors, 'name') ?>">
                            <?= fieldError($addErrors, 'name') ?>
                        </div>

                        <div class="form-group">
                            <label>Đơn vị giảm giá <span style="color:#ff6b6b">*</span></label>
                            <select
                                name="discount_type"
                                required
                                class="<?= fieldClass($addErrors, 'discount_type') ?>">
                                <option value="percent" <?= selectedValue($addOld, 'discount_type', 'percent', 'percent') ?>>%</option>
                                <option value="fixed" <?= selectedValue($addOld, 'discount_type', 'fixed') ?>>VNĐ</option>
                            </select>
                            <?= fieldError($addErrors, 'discount_type') ?>
                        </div>

                        <div class="form-group">
                            <label>Giá trị giảm <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="number"
                                name="discount_value"
                                min="1"
                                step="0.01"
                                required
                                placeholder="VD: 10 hoặc 5000.50"
                                value="<?= oldValue($addOld, 'discount_value') ?>"
                                class="<?= fieldClass($addErrors, 'discount_value') ?>">
                            <?= fieldError($addErrors, 'discount_value') ?>
                        </div>

                        <div class="form-group">
                            <label>Đơn tối thiểu (VNĐ)</label>
                            <input
                                type="number"
                                name="min_amount"
                                min="0"
                                step="0.01"
                                value="<?= oldValue($addOld, 'min_amount', '0') ?>"
                                placeholder="VD: 50000"
                                class="<?= fieldClass($addErrors, 'min_amount') ?>">
                            <?= fieldError($addErrors, 'min_amount') ?>
                        </div>

                        <div class="form-group">
                            <label>Ngày bắt đầu <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="date"
                                name="start_date"
                                required
                                min="1900-01-01"
                                max="2100-12-31"
                                value="<?= oldValue($addOld, 'start_date') ?>"
                                class="<?= fieldClass($addErrors, 'start_date') ?>"
                                onclick="this.showPicker && this.showPicker()">
                            <?= fieldError($addErrors, 'start_date') ?>
                        </div>

                        <div class="form-group">
                            <label>Ngày kết thúc <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="date"
                                name="end_date"
                                required
                                min="1900-01-01"
                                max="2100-12-31"
                                value="<?= oldValue($addOld, 'end_date') ?>"
                                class="<?= fieldClass($addErrors, 'end_date') ?>"
                                onclick="this.showPicker && this.showPicker()">
                            <?= fieldError($addErrors, 'end_date') ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-action" onclick="closeModal('addPromoModal')">Hủy</button>
                        <button type="submit" class="btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if (($editPromotion || !empty($editErrors)) && hasPermission('promotions_update')): ?>
        <div id="editPromoModal" class="modal" style="display:<?= ($openModal === 'edit' || $editOldId > 0) ? 'flex' : 'none' ?>;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Chỉnh sửa khuyến mãi</h2>
                    <button type="button" class="btn-close" onclick="window.location.href='index.php?page=promotions'">&times;</button>
                </div>

                <form method="POST" action="index.php?page=promotions&action=update&id=<?= (int)($editPromotion['promotion_id'] ?? $editOldId) ?>" onsubmit="return validatePromotionForm(this)">
                    <div class="modal-body">
                        <?php if (!empty($editErrors['general'])): ?>
                            <div class="form-general-error"><?= htmlspecialchars($editErrors['general']) ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Mã Code <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="text"
                                name="code"
                                required
                                maxlength="50"
                                value="<?= htmlspecialchars($editPromotion['code'] ?? '') ?>"
                                class="<?= fieldClass($editErrors, 'code') ?>">
                            <?= fieldError($editErrors, 'code') ?>
                        </div>

                        <div class="form-group">
                            <label>Tên khuyến mãi <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="text"
                                name="name"
                                required
                                maxlength="255"
                                value="<?= htmlspecialchars($editPromotion['name'] ?? '') ?>"
                                class="<?= fieldClass($editErrors, 'name') ?>">
                            <?= fieldError($editErrors, 'name') ?>
                        </div>

                        <div class="form-group">
                            <label>Đơn vị giảm giá <span style="color:#ff6b6b">*</span></label>
                            <select
                                name="discount_type"
                                required
                                class="<?= fieldClass($editErrors, 'discount_type') ?>">
                                <option value="percent" <?= (($editPromotion['discount_type'] ?? 'percent') === 'percent') ? 'selected' : '' ?>>%</option>
                                <option value="fixed" <?= (($editPromotion['discount_type'] ?? '') === 'fixed') ? 'selected' : '' ?>>VNĐ</option>
                            </select>
                            <?= fieldError($editErrors, 'discount_type') ?>
                        </div>

                        <div class="form-group">
                            <label>Giá trị giảm <span style="color:#ff6b6b">*</span></label>
                            <input
                                type="number"
                                name="discount_value"
                                min="1"
                                step="0.01"
                                required
                                value="<?= htmlspecialchars($editPromotion['discount_value'] ?? '1') ?>"
                                class="<?= fieldClass($editErrors, 'discount_value') ?>">
                            <?= fieldError($editErrors, 'discount_value') ?>
                        </div>

                        <div class="form-group">
                            <label>Đơn tối thiểu (VNĐ)</label>
                            <input
                                type="number"
                                name="min_amount"
                                min="0"
                                step="0.01"
                                value="<?= htmlspecialchars($editPromotion['min_amount'] ?? '0') ?>"
                                class="<?= fieldClass($editErrors, 'min_amount') ?>">
                            <?= fieldError($editErrors, 'min_amount') ?>
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
                                class="<?= fieldClass($editErrors, 'start_date') ?>"
                                onclick="this.showPicker && this.showPicker()">
                            <?= fieldError($editErrors, 'start_date') ?>
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
                                class="<?= fieldClass($editErrors, 'end_date') ?>"
                                onclick="this.showPicker && this.showPicker()">
                            <?= fieldError($editErrors, 'end_date') ?>
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
    function clearInlineErrors(form) {
        form.querySelectorAll('.field-error.client-error').forEach(el => el.remove());
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    }

    function appendFieldError(input, message) {
        if (!input) return;
        input.classList.add('input-error');

        const error = document.createElement('div');
        error.className = 'field-error client-error';
        error.textContent = message;
        input.insertAdjacentElement('afterend', error);
    }

    function validatePromotionForm(form) {
        clearInlineErrors(form);

        const code = form.querySelector('[name="code"]');
        const name = form.querySelector('[name="name"]');
        const discountType = form.querySelector('[name="discount_type"]');
        const discountValue = form.querySelector('[name="discount_value"]');
        const minAmount = form.querySelector('[name="min_amount"]');
        const startDate = form.querySelector('[name="start_date"]');
        const endDate = form.querySelector('[name="end_date"]');

        let isValid = true;
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const codeVal = code?.value.trim() || '';
        const nameVal = name?.value.trim() || '';
        const discountTypeVal = discountType?.value || '';
        const discountValueVal = discountValue?.value.trim() || '';
        const minAmountVal = minAmount?.value.trim() || '0';
        const startDateVal = startDate?.value.trim() || '';
        const endDateVal = endDate?.value.trim() || '';

        if (codeVal === '') {
            appendFieldError(code, 'Vui lòng nhập mã code.');
            isValid = false;
        } else if (!/^[a-zA-Z0-9_-]+$/.test(codeVal)) {
            appendFieldError(code, 'Mã code chỉ được chứa chữ, số, gạch dưới hoặc gạch ngang.');
            isValid = false;
        }

        if (nameVal === '') {
            appendFieldError(name, 'Vui lòng nhập tên khuyến mãi.');
            isValid = false;
        }

        if (!['percent', 'fixed'].includes(discountTypeVal)) {
            appendFieldError(discountType, 'Vui lòng chọn đơn vị giảm giá.');
            isValid = false;
        }

        if (discountValueVal === '' || isNaN(discountValueVal) || Number(discountValueVal) < 1) {
            appendFieldError(discountValue, 'Giá trị giảm phải lớn hơn hoặc bằng 1.');
            isValid = false;
        } else if (discountTypeVal === 'percent' && Number(discountValueVal) > 100) {
            appendFieldError(discountValue, 'Giảm theo % không được vượt quá 100.');
            isValid = false;
        }

        if (minAmountVal === '' || isNaN(minAmountVal) || Number(minAmountVal) < 0) {
            appendFieldError(minAmount, 'Đơn tối thiểu không hợp lệ.');
            isValid = false;
        }

        if (startDateVal === '') {
            appendFieldError(startDate, 'Vui lòng chọn ngày bắt đầu.');
            isValid = false;
        }

        if (endDateVal === '') {
            appendFieldError(endDate, 'Vui lòng chọn ngày kết thúc.');
            isValid = false;
        }

        if (startDateVal !== '') {
            const start = new Date(startDateVal + 'T00:00:00');
            if (start < today) {
                appendFieldError(startDate, 'Ngày bắt đầu không được nhỏ hơn ngày hiện tại.');
                isValid = false;
            }
        }

        if (startDateVal !== '' && endDateVal !== '') {
            const start = new Date(startDateVal + 'T00:00:00');
            const end = new Date(endDateVal + 'T00:00:00');

            if (end < start) {
                appendFieldError(endDate, 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu.');
                isValid = false;
            }
        }

        return isValid;
    }

    document.addEventListener('DOMContentLoaded', function() {
        <?php if (($openModal === 'add' || !empty($addErrors)) && hasPermission('promotions_create')): ?>
            openModal('addPromoModal');
        <?php endif; ?>

        <?php if ((($openModal === 'edit' && $editPromotion) || $editOldId > 0 || !empty($editErrors)) && hasPermission('promotions_update')): ?>
            openModal('editPromoModal');
        <?php endif; ?>
    });
</script>