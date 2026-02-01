<?php

require_once __DIR__ . '/../services/HallStatusService.php';
require_once __DIR__ . '/../config/dbConfig.php';

class HallStatusController
{
    private HallStatusService $hallStatusService;

    public function __construct(HallStatusService $hallStatusService)
    {
        $this->hallStatusService = $hallStatusService;
    }

    public function getAllStatuses()
    {
        try {
            return $this->hallStatusService->getAllStatuses();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return [];
        }
    }

    public function getStatusById($statusId)
    {
        try {
            return $this->hallStatusService->getStatusById($statusId);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return null;
        }
    }

    public function createStatus()
    {
        try {
            $statusName = $_POST['status_name'] ?? '';

            if (empty($statusName)) {
                throw new InvalidArgumentException("Vui lòng nhập tên trạng thái.");
            }

            $result = $this->hallStatusService->createStatus($statusName);

            if ($result) {
                $_SESSION['success'] = "Thêm trạng thái phòng thành công!";
                header('Location: ../../views/admin/index.php?page=hall_status&add=1');
            } else {
                throw new Exception("Không thể thêm trạng thái phòng.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=hall_status&error=1');
        }
    }

    public function updateStatus($id)
    {
        try {
            $statusName = $_POST['status_name'] ?? '';

            if (empty($statusName)) {
                throw new InvalidArgumentException("Vui lòng nhập tên trạng thái.");
            }

            $result = $this->hallStatusService->updateStatus($id, $statusName);

            if ($result) {
                $_SESSION['success'] = "Cập nhật trạng thái phòng thành công!";
                header('Location: ../../views/admin/index.php?page=hall_status&update=1');
            } else {
                throw new Exception("Không thể cập nhật trạng thái phòng.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=hall_status&error=1');
        }
    }

    public function deleteStatus($id)
    {
        try {
            $result = $this->hallStatusService->deleteStatus($id);

            if ($result) {
                $_SESSION['success'] = "Xóa trạng thái phòng thành công!";
                header('Location: ../../views/admin/index.php?page=hall_status&delete=1');
            } else {
                throw new Exception("Không thể xóa trạng thái phòng.");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=hall_status&error=1');
        }
    }
}

