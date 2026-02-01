<?php
class HallStatus
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllStatuses()
    {
        $sql = "SELECT * FROM hall_status WHERE Status = 1 ORDER BY StatusID";
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

    public function getStatusById($statusId)
    {
        $sql = "SELECT * FROM hall_status WHERE StatusID = ? AND Status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $statusId);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    public function getStatusByName($statusName)
    {
        $sql = "SELECT * FROM hall_status WHERE StatusName = ? AND Status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("s", $statusName);
        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    public function createStatus($statusName)
    {
        $sql = "INSERT INTO hall_status (StatusName) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("s", $statusName);

        return $stmt->execute();
    }

    public function updateStatus($statusId, $statusName)
    {
        $sql = "UPDATE hall_status SET StatusName = ? WHERE StatusID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("si", $statusName, $statusId);

        return $stmt->execute();
    }

    public function deleteStatus($statusId)
    {
        // Kiểm tra xem có phòng nào đang sử dụng trạng thái này không
        $checkSql = "SELECT COUNT(*) as count FROM halls WHERE StatusID = ? AND Status = 1";
        $checkStmt = $this->conn->prepare($checkSql);
        if (!$checkStmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $checkStmt->bind_param("i", $statusId);
        if (!$checkStmt->execute()) {
            throw new Exception("SQL Execute Error: " . $checkStmt->error);
        }
        $result = $checkStmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $checkStmt->error);
        }
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Không thể xóa trạng thái này vì đang có phòng chiếu đang sử dụng.");
        }

        $sql = "UPDATE hall_status SET Status = 0 WHERE StatusID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $statusId);

        return $stmt->execute();
    }
}

