<?php
require_once __DIR__ . '/../models/Hall.php';

class HallService
{
    private $hallModel;

    public function __construct($hallModel)
    {
        $this->hallModel = $hallModel;
    }

    public function getAllHalls($cinemaId = null)
    {
        return $this->hallModel->getAllHalls($cinemaId);
    }

    public function getHallById($hallId)
    {
        if (empty($hallId) || !is_numeric($hallId)) {
            throw new InvalidArgumentException("Hall ID không hợp lệ.");
        }

        return $this->hallModel->getHallById($hallId);
    }

    public function getHallsByCinema($cinemaId)
    {
        if (empty($cinemaId) || !is_numeric($cinemaId)) {
            throw new InvalidArgumentException("Cinema ID không hợp lệ.");
        }

        return $this->hallModel->getHallsByCinema($cinemaId);
    }

    public function createHall($cinemaId, $name, $statusId)
    {
        if (!is_numeric($cinemaId)) {
            throw new InvalidArgumentException("Cinema ID không hợp lệ.");
        }

        if ($name === null || trim($name) === '') {
            throw new InvalidArgumentException("Tên phòng chiếu không được để trống.");
        }

        if (mb_strlen($name) > 50) {
            throw new InvalidArgumentException("Tên phòng chiếu không được vượt quá 50 ký tự.");
        }

        if ($statusId === null || $statusId === '' || !is_numeric($statusId)) {
            throw new InvalidArgumentException("Status ID không hợp lệ.");
        }

        return $this->hallModel->createHall((int)$cinemaId, $name, (int)$statusId);
    }

    public function updateHall($hallId, $cinemaId, $name, $statusId)
    {
        if (!is_numeric($hallId)) {
            throw new InvalidArgumentException("Hall ID không hợp lệ.");
        }

        if (!is_numeric($cinemaId)) {
            throw new InvalidArgumentException("Cinema ID không hợp lệ.");
        }

        if ($name === null || trim($name) === '') {
            throw new InvalidArgumentException("Tên phòng chiếu không được để trống.");
        }

        if (mb_strlen($name) > 50) {
            throw new InvalidArgumentException("Tên phòng chiếu không được vượt quá 50 ký tự.");
        }

        if ($statusId === null || $statusId === '' || !is_numeric($statusId)) {
            throw new InvalidArgumentException("Status ID không hợp lệ.");
        }

        return $this->hallModel->updateHall((int)$hallId, (int)$cinemaId, $name, (int)$statusId);
    }

    public function deleteHall($hallId)
    {
        if (empty($hallId) || !is_numeric($hallId)) {
            throw new InvalidArgumentException("Hall ID không hợp lệ.");
        }

        return $this->hallModel->deleteHall($hallId);
    }

    public function getSeatCount($hallId)
    {
        if (empty($hallId) || !is_numeric($hallId)) {
            throw new InvalidArgumentException("Hall ID không hợp lệ.");
        }

        return $this->hallModel->getSeatCount($hallId);
    }
}

