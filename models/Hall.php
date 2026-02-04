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
            $sql = "SELECT h.*, c.Name as CinemaName, hs.StatusName as StatusName 
                    FROM halls h 
                    INNER JOIN cinemas c ON h.CinemaID = c.CinemaID 
                    INNER JOIN hall_status hs ON h.StatusID = hs.StatusID 
                    WHERE h.Status = 1 AND h.CinemaID = ? 
                    ORDER BY h.HallID DESC";
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
            $sql = "SELECT h.*, c.Name as CinemaName, hs.StatusName as StatusName 
                    FROM halls h 
                    INNER JOIN cinemas c ON h.CinemaID = c.CinemaID 
                    INNER JOIN hall_status hs ON h.StatusID = hs.StatusID 
                    WHERE h.Status = 1 
                    ORDER BY h.HallID DESC";
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
        $sql = "SELECT h.*, c.Name as CinemaName, hs.StatusName as StatusName 
                FROM halls h 
                INNER JOIN cinemas c ON h.CinemaID = c.CinemaID 
                INNER JOIN hall_status hs ON h.StatusID = hs.StatusID 
                WHERE h.HallID = ? AND h.Status = 1";
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
        $sql = "SELECT h.*, hs.StatusName as StatusName 
                FROM halls h 
                INNER JOIN hall_status hs ON h.StatusID = hs.StatusID 
                WHERE h.CinemaID = ? AND h.Status = 1 
                ORDER BY h.Name";
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

    public function createHall($cinemaId, $name, $statusId)
    {
        $sql = "INSERT INTO halls (CinemaID, Name, StatusID) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("isi", $cinemaId, $name, $statusId);

        return $stmt->execute();
    }

    public function updateHall($hallId, $cinemaId, $name, $statusId)
    {
        $sql = "UPDATE halls SET CinemaID = ?, Name = ?, StatusID = ? WHERE HallID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("isii", $cinemaId, $name, $statusId, $hallId);

        return $stmt->execute();
    }

    public function deleteHall($hallId)
    {
        $sql = "UPDATE halls SET Status = 0 WHERE HallID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $hallId);

        return $stmt->execute();
    }

    public function getSeatCount($hallId)
    {
        $sql = "SELECT COUNT(*) as count FROM seats WHERE HallID = ? AND Status = 1";
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

