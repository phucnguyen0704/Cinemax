<?php
class Seat
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getSeatsByHall($hallId)
    {
        $sql = "SELECT s.*, st.TypeName, st.PriceMultiplier 
                FROM seats s 
                INNER JOIN seat_type st ON s.SeatTypeID = st.SeatTypeID 
                WHERE s.HallID = ? AND s.Status = 1 
                ORDER BY s.RowName, s.SeatNumber";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $hallId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }

        $seats = [];
        while ($row = $result->fetch_assoc()) {
            $seats[] = $row;
        }

        return $seats;
    }

    public function getSeatById($seatId)
    {
        $sql = "SELECT s.*, st.TypeName, st.PriceMultiplier 
                FROM seats s 
                INNER JOIN seat_type st ON s.SeatTypeID = st.SeatTypeID 
                WHERE s.SeatID = ? AND s.Status = 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $seatId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }

        return $result->fetch_assoc();
    }

    public function createSeat($hallId, $seatTypeId, $rowName, $seatNumber)
    {
        $sql = "INSERT INTO seats (HallID, SeatTypeID, RowName, SeatNumber, Status) VALUES (?, ?, ?, ?, 1)";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("iisi", $hallId, $seatTypeId, $rowName, $seatNumber);
        if (!$stmt->execute()) {
            // Kiểm tra nếu là lỗi duplicate
            if ($this->conn->errno == 1062) {
                throw new Exception("Ghế này đã tồn tại trong phòng chiếu.");
            }
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        return $this->conn->insert_id;
    }

    public function updateSeat($seatId, $seatTypeId)
    {
        $sql = "UPDATE seats SET SeatTypeID = ? WHERE SeatID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("ii", $seatTypeId, $seatId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    public function deleteSeat($seatId)
    {
        // Soft delete
        $sql = "UPDATE seats SET Status = 0 WHERE SeatID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $seatId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    public function deleteAllSeatsByHall($hallId)
    {
        // Soft delete tất cả ghế trong phòng
        $sql = "UPDATE seats SET Status = 0 WHERE HallID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $hallId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        return $stmt->affected_rows;
    }

    public function createBulkSeats($hallId, $seats)
    {
        // Tạo nhiều ghế cùng lúc
        $sql = "INSERT INTO seats (HallID, SeatTypeID, RowName, SeatNumber, Status) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $this->conn->error);
        }

        $successCount = 0;
        $errors = [];

        foreach ($seats as $seat) {
            try {
                $stmt->bind_param("iisi", $hallId, $seat['seat_type_id'], $seat['row_name'], $seat['seat_number']);
                if ($stmt->execute()) {
                    $successCount++;
                } else {
                    // Bỏ qua duplicate, chỉ log lỗi khác
                    if ($this->conn->errno != 1062) {
                        $errors[] = "Lỗi khi tạo ghế {$seat['row_name']}{$seat['seat_number']}: " . $stmt->error;
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Lỗi khi tạo ghế {$seat['row_name']}{$seat['seat_number']}: " . $e->getMessage();
            }
        }

        return [
            'success_count' => $successCount,
            'errors' => $errors
        ];
    }
}
