<?php
if (!isset($promotionService)) {
    die('PromotionService chưa được khởi tạo.');
}

$promotions = $promotionService->listPromotions();

// Chỉ hiện khuyến mãi còn hiệu lực hoặc sắp diễn ra
$promotions = array_values(array_filter($promotions, function ($promo) {
    return in_array(($promo['computed_status'] ?? ''), ['active', 'scheduled'], true);
}));

function formatPromotionDateVN($date)
{
    if (!$date) return 'Chưa cập nhật';
    return date('d/m/Y', strtotime($date));
}

function getPromotionBadgeText($promo)
{
    $type = $promo['discount_type'] ?? 'percent';
    $value = (float)($promo['discount_value'] ?? 0);

    if ($type === 'percent') {
        return 'Giảm ' . rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') . '%';
    }

    return 'Giảm ' . number_format($value, 0, ',', '.') . 'đ';
}

function getPromotionStatusText($status)
{
    return match ($status) {
        'active' => 'Đang áp dụng',
        'scheduled' => 'Sắp diễn ra',
        'expired' => 'Đã hết hạn',
        default => 'Không xác định',
    };
}
?>

<main class="section">
    <div class="container">
        <div class="section-header">
            <h2>Khuyến mãi &amp; Sự kiện</h2>
        </div>

        <div class="promo-grid">
            <?php if (empty($promotions)): ?>
                <p>Hiện chưa có khuyến mãi nào đang áp dụng hoặc sắp diễn ra.</p>
            <?php else: ?>
                <?php foreach ($promotions as $promo): ?>
                    <div class="promo-card">
                        <img
                            src="https://via.placeholder.com/400x200?text=Promotion"
                            alt="<?= htmlspecialchars($promo['code'] ?? 'PROMO_CODE') ?>"
                        >

                        <div class="promo-content">
                            <span class="promo-badge">
                                <?= htmlspecialchars(getPromotionBadgeText($promo)) ?>
                            </span>

                            <h3>Mã: <?= htmlspecialchars($promo['code'] ?? '') ?></h3>

                            <p>
                                Áp dụng từ
                                <?= htmlspecialchars(formatPromotionDateVN($promo['start_date'] ?? null)) ?>
                                đến
                                <?= htmlspecialchars(formatPromotionDateVN($promo['end_date'] ?? null)) ?>
                            </p>

                            <p>
                                <strong>Trạng thái:</strong>
                                <?= htmlspecialchars(getPromotionStatusText($promo['computed_status'] ?? '')) ?>
                            </p>

                            <?php if (!empty($promo['name'])): ?>
                                <p><?= htmlspecialchars($promo['name']) ?></p>
                            <?php endif; ?>

                            <?php if ((float)($promo['min_amount'] ?? 0) > 0): ?>
                                <p>
                                    Đơn tối thiểu:
                                    <?= number_format((float)$promo['min_amount'], 0, ',', '.') ?>đ
                                </p>
                            <?php endif; ?>

                            <a href="#" class="promo-link">Chi tiết</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>