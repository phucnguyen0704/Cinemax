<?php
class Hall
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllHalls($cinemaId = null)
    {
        if ($cinemaId) {
            $sql = "SELECT 
                        h.*,
                        h.hall_id AS HallID,
                        h.name    AS Name,
                        c.name    AS CinemaName,
                    CASE 
                        WHEN h.status = 1 THEN 'Đang hoạt động'
                        WHEN h.status = 0 THEN 'Tạm dừng'
                        ELSE 'Bảo trì'
                    END as StatusName
                    FROM halls h 
                    INNER JOIN cinemas c ON h.cinema_id = c.cinema_id 
                    WHERE h.status = 1 AND h.cinema_id = ? 
                    ORDER BY h.hall_id DESC";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("SQL Error: " . $this->conn->error);
            }
            $stmt->bind_param("i", $cinemaId);
            if (!$stmt->execute()) {
                throw new Exception("SQL Execute Error: " . $stmt->error);
            }
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("SQL Get Result Error: " . $stmt->error);
            }
        } else {
            $sql = "SELECT 
                        h.*,
                        h.hall_id AS HallID,
                        h.name    AS Name,
                        c.name    AS CinemaName,
                    CASE 
                        WHEN h.status = 1 THEN 'Đang hoạt động'
                        WHEN h.status = 0 THEN 'Tạm dừng'
                        ELSE 'Bảo trì'
                    END as StatusName
                    FROM halls h 
                    INNER JOIN cinemas c ON h.cinema_id = c.cinema_id 
                    WHERE h.status = 1 
                    ORDER BY h.hall_id DESC";
            $result = $this->conn->query($sql);
            if (!$result) {
                throw new Exception("SQL Error: " . $this->conn->error);
            }
        }

        $halls = [];
        while ($row = $result->fetch_assoc()) {
            $halls[] = $row;
        }

        return $halls;
    }

    public function getHallById($hallId)
    {
        $sql = "SELECT 
                    h.*,
                    h.hall_id AS HallID,
                    h.name    AS Name,
                    c.name    AS CinemaName,
                CASE 
                    WHEN h.status = 1 THEN 'Đang hoạt động'
                    WHEN h.status = 0 THEN 'Tạm dừng'
                    ELSE 'Bảo trì'
                END as StatusName
                FROM halls h 
                INNER JOIN cinemas c ON h.cinema_id = c.cinema_id 
                WHERE h.hall_id = ? AND h.status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $hallId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    public function getHallsByCinema($cinemaId)
    {
        $sql = "SELECT h.*,
                CASE 
                    WHEN h.status = 1 THEN 'Đang hoạt động'
                    WHEN h.status = 0 THEN 'Tạm dừng'
                    ELSE 'Bảo trì'
                END as StatusName
                FROM halls h 
                WHERE h.cinema_id = ? AND h.status = 1 
                ORDER BY h.name";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $cinemaId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        $halls = [];
        while ($row = $result->fetch_assoc()) {
            $halls[] = $row;
        }

        return $halls;
    }

    public function createHall($cinemaId, $name, $statusId = 1)
    {
        $sql = "INSERT INTO halls (cinema_id, name, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("isi", $cinemaId, $name, $statusId);

        return $stmt->execute();
    }

    public function updateHall($hallId, $cinemaId, $name, $statusId = 1)
    {
        $sql = "UPDATE halls SET cinema_id = ?, name = ?, status = ? WHERE hall_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("isii", $cinemaId, $name, $statusId, $hallId);

        return $stmt->execute();
    }

    public function deleteHall($hallId)
    {
        $sql = "UPDATE halls SET status = 0 WHERE hall_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $hallId);

        return $stmt->execute();
    }

    public function getSeatCount($hallId)
    {
        $sql = "SELECT COUNT(*) as count FROM seats WHERE hall_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $hallId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }
}

