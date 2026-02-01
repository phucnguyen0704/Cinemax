<?php
class Cinema
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllCinemas()
    {
        $sql = "SELECT c.*, l.Name as LocationName, cs.StatusName as CinemaStatusName,
                (SELECT COUNT(*) FROM halls h WHERE h.CinemaID = c.CinemaID AND h.Status = 1) as HallCount
                FROM cinemas c 
                INNER JOIN locations l ON c.LocationID = l.LocationID 
                INNER JOIN cinema_status cs ON c.StatusID = cs.StatusID 
                WHERE c.Status = 1 
                ORDER BY c.CinemaID DESC";
        
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        $cinemas = [];
        while ($row = $result->fetch_assoc()) {
            $cinemas[] = $row;
        }

        return $cinemas;
    }

    public function getCinemaById($cinemaId)
    {
        $sql = "SELECT c.*, l.Name as LocationName, cs.StatusName as CinemaStatusName,
                (SELECT COUNT(*) FROM halls h WHERE h.CinemaID = c.CinemaID AND h.Status = 1) as HallCount
                FROM cinemas c 
                INNER JOIN locations l ON c.LocationID = l.LocationID 
                INNER JOIN cinema_status cs ON c.StatusID = cs.StatusID 
                WHERE c.CinemaID = ? AND c.Status = 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $cinemaId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }

        return $result->fetch_assoc();
    }

    public function createCinema($name, $address, $locationId, $statusId)
    {
        $sql = "INSERT INTO cinemas (Name, Address, LocationID, StatusID, Status) VALUES (?, ?, ?, ?, 1)";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("ssii", $name, $address, $locationId, $statusId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        return $this->conn->insert_id;
    }

    public function updateCinema($cinemaId, $name, $address, $locationId, $statusId)
    {
        $sql = "UPDATE cinemas SET Name = ?, Address = ?, LocationID = ?, StatusID = ? WHERE CinemaID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("ssiii", $name, $address, $locationId, $statusId, $cinemaId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    public function deleteCinema($cinemaId)
    {
        // Soft delete: chỉ đổi Status = 0
        $sql = "UPDATE cinemas SET Status = 0 WHERE CinemaID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $cinemaId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    public function getAllLocations()
    {
        $sql = "SELECT LocationID, Name FROM locations WHERE Status = 1 ORDER BY Name";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        $locations = [];
        while ($row = $result->fetch_assoc()) {
            $locations[] = $row;
        }

        return $locations;
    }

    public function getAllCinemaStatuses()
    {
        $sql = "SELECT StatusID, StatusName FROM cinema_status WHERE Status = 1 ORDER BY StatusName";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        $statuses = [];
        while ($row = $result->fetch_assoc()) {
            $statuses[] = $row;
        }

        return $statuses;
    }
}
