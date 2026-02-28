<?php
require_once __DIR__ . '/../models/HallStatus.php';

class HallStatusService
{
    private $hallStatusModel;

    public function __construct($hallStatusModel)
    {
        $this->hallStatusModel = $hallStatusModel;
    }

    public function getAllStatuses()
    {
        return $this->hallStatusModel->getAllStatuses();
    }

    public function getStatusById($statusId)
    {
        if (empty($statusId) || !is_numeric($statusId)) {
            throw new InvalidArgumentException("Status ID không hợp lệ.");
        }

        return $this->hallStatusModel->getStatusById($statusId);
    }

    public function getStatusByName($statusName)
    {
        if (empty($statusName) || trim($statusName) === '') {
            throw new InvalidArgumentException("Tên trạng thái không được để trống.");
        }

        return $this->hallStatusModel->getStatusByName($statusName);
    }

    public function createStatus($statusName)
    {
        if (empty($statusName) || trim($statusName) === '') {
            throw new InvalidArgumentException("Tên trạng thái không được để trống.");
        }

        if (strlen($statusName) > 50) {
            throw new InvalidArgumentException("Tên trạng thái không được vượt quá 50 ký tự.");
        }

        // Kiểm tra xem trạng thái đã tồn tại chưa
        $existingStatus = $this->hallStatusModel->getStatusByName($statusName);
        if ($existingStatus) {
            throw new InvalidArgumentException("Trạng thái này đã tồn tại.");
        }

        return $this->hallStatusModel->createStatus($statusName);
    }

    public function updateStatus($statusId, $statusName)
    {
        if (empty($statusId) || !is_numeric($statusId)) {
            throw new InvalidArgumentException("Status ID không hợp lệ.");
        }

        if (empty($statusName) || trim($statusName) === '') {
            throw new InvalidArgumentException("Tên trạng thái không được để trống.");
        }

        if (strlen($statusName) > 50) {
            throw new InvalidArgumentException("Tên trạng thái không được vượt quá 50 ký tự.");
        }

        // Kiểm tra xem trạng thái đã tồn tại chưa (trừ chính nó)
        $existingStatus = $this->hallStatusModel->getStatusByName($statusName);
        $existingStatusId = $existingStatus['StatusID'] ?? $existingStatus['status_id'] ?? null;
        if ($existingStatus && $existingStatusId != $statusId) {
            throw new InvalidArgumentException("Trạng thái này đã tồn tại.");
        }

        return $this->hallStatusModel->updateStatus($statusId, $statusName);
    }

    public function deleteStatus($statusId)
    {
        if (empty($statusId) || !is_numeric($statusId)) {
            throw new InvalidArgumentException("Status ID không hợp lệ.");
        }

        return $this->hallStatusModel->deleteStatus($statusId);
    }
}

