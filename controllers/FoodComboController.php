<?php

require_once __DIR__ . '/../services/FoodComboService.php';

class FoodComboController
{
    private FoodComboService $comboService;

    public function __construct(FoodComboService $comboService)
    {
        $this->comboService = $comboService;
    }

    public function getAllCombos()
    {
        try {
            return $this->comboService->getAllCombos();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return [];
        }
    }

    public function getComboById($comboId)
    {
        try {
            return $this->comboService->getComboById($comboId);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return null;
        }
    }

    public function createCombo()
    {
        try {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;

            if (!$name || $price === '') {
                throw new InvalidArgumentException("Vui lòng nhập đầy đủ Tên combo và Giá.");
            }

            $resultId = $this->comboService->createCombo($name, $description, $price);

            if ($resultId) {
                $_SESSION['success'] = "Thêm combo thành công!";
                header('Location: ../../views/admin/index.php?page=combos&add=1');
            } else {
                throw new Exception("Không thể thêm combo.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=combos&error=1');
        }
    }

    public function updateCombo($id)
    {
        try {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;

            if (!$id) {
                throw new InvalidArgumentException("Không tìm thấy ID combo cần cập nhật.");
            }

            if (!$name || $price === '') {
                throw new InvalidArgumentException("Vui lòng nhập đầy đủ Tên combo và Giá.");
            }

            $result = $this->comboService->updateCombo($id, $name, $description, $price);

            if ($result) {
                $_SESSION['success'] = "Cập nhật combo thành công!";
                header('Location: ../../views/admin/index.php?page=combos&update=1');
            } else {
                throw new Exception("Không thể cập nhật combo.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=combos&error=1');
        }
    }

    public function deleteCombo($id)
    {
        try {
            if (!$id) {
                throw new InvalidArgumentException("Không tìm thấy ID combo cần xóa.");
            }

            $result = $this->comboService->deleteCombo($id);

            if ($result) {
                $_SESSION['success'] = "Xóa combo thành công!";
                header('Location: ../../views/admin/index.php?page=combos&delete=1');
            } else {
                throw new Exception("Không thể xóa combo.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=combos&error=1');
        }
    }
}

