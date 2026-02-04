<?php
class SeatType
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllSeatTypes()
    {
        $sql = "SELECT * FROM seat_type WHERE Status = 1 ORDER BY SeatTypeID";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        $seatTypes = [];
        while ($row = $result->fetch_assoc()) {
            $seatTypes[] = $row;
        }

        return $seatTypes;
    }

    public function getSeatTypeById($seatTypeId)
    {
        $sql = "SELECT * FROM seat_type WHERE SeatTypeID = ? AND Status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $seatTypeId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    public function getSeatTypeByName($typeName)
    {
        $sql = "SELECT * FROM seat_type WHERE TypeName = ? AND Status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("s", $typeName);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    public function createSeatType($typeName, $priceMultiplier)
    {
        $sql = "INSERT INTO seat_type (TypeName, PriceMultiplier) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("sd", $typeName, $priceMultiplier);

        return $stmt->execute();
    }

    public function updateSeatType($seatTypeId, $typeName, $priceMultiplier)
    {
        $sql = "UPDATE seat_type SET TypeName = ?, PriceMultiplier = ? WHERE SeatTypeID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("sdi", $typeName, $priceMultiplier, $seatTypeId);

        return $stmt->execute();
    }

    public function deleteSeatType($seatTypeId)
    {
        // Kiểm tra xem có ghế nào đang sử dụng loại ghế này không
        $checkSql = "SELECT COUNT(*) as count FROM seats WHERE SeatTypeID = ? AND Status = 1";
        $checkStmt = $this->conn->prepare($checkSql);
        if (!$checkStmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $checkStmt->bind_param("i", $seatTypeId);
        if (!$checkStmt->execute()) {
            throw new Exception("SQL Execute Error: " . $checkStmt->error);
        }
        $result = $checkStmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $checkStmt->error);
        }
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Không thể xóa loại ghế này vì đang có ghế đang sử dụng.");
        }

        $sql = "UPDATE seat_type SET Status = 0 WHERE SeatTypeID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $seatTypeId);

        return $stmt->execute();
    }
}

