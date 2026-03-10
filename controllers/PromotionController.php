<?php

class PromotionController
{
    private $promotionService;

    public function __construct($promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function create()
    {
        try {
            $data = [
                'code' => $_POST['code'] ?? '',
                'name' => $_POST['name'] ?? '',
                'discount_type' => $_POST['discount_type'] ?? 'percent',
                'discount_value' => $_POST['discount_value'] ?? 0,
                'start_date' => $_POST['start_date'] ?? '',
                'end_date' => $_POST['end_date'] ?? '',
                'min_amount' => $_POST['min_amount'] ?? 0,
            ];

            $this->promotionService->createPromotion($data);

            header("Location: index.php?page=promotions&success=" . urlencode("Thêm khuyến mãi thành công."));
            exit;
        } catch (Throwable $e) {
            header("Location: index.php?page=promotions&error=" . urlencode($e->getMessage()) . "&open_modal=add");
            exit;
        }
    }

    public function update($id)
    {
        try {
            $data = [
                'code' => $_POST['code'] ?? '',
                'name' => $_POST['name'] ?? '',
                'discount_type' => $_POST['discount_type'] ?? 'percent',
                'discount_value' => $_POST['discount_value'] ?? 0,
                'start_date' => $_POST['start_date'] ?? '',
                'end_date' => $_POST['end_date'] ?? '',
                'min_amount' => $_POST['min_amount'] ?? 0,
            ];

            $this->promotionService->updatePromotion((int)$id, $data);

            header("Location: index.php?page=promotions&success=" . urlencode("Cập nhật khuyến mãi thành công."));
            exit;
        } catch (Throwable $e) {
            header("Location: index.php?page=promotions&error=" . urlencode($e->getMessage()) . "&open_modal=edit&id=" . (int)$id);
            exit;
        }
    }

    public function delete($id)
    {
        try {
            $this->promotionService->deletePromotion((int)$id);

            header("Location: index.php?page=promotions&success=" . urlencode("Xóa khuyến mãi thành công."));
            exit;
        } catch (Throwable $e) {
            header("Location: index.php?page=promotions&error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}