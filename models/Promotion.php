<?php

class Promotion
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllPromotions($search = '')
    {
        $sql = "SELECT * FROM promotions WHERE status != -1";
        $types = '';
        $params = [];

        $search = trim((string)$search);
        if ($search !== '') {
            $sql .= " AND (code LIKE ? OR name LIKE ?)";
            $types = 'ss';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= " ORDER BY promotion_id DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getPromotionById($promotionId)
    {
        $sql = "SELECT * FROM promotions WHERE promotion_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $promotionId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }

    public function getPromotionByCode($code)
    {
        $sql = "SELECT * FROM promotions WHERE UPPER(code) = UPPER(?) LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }

    public function createPromotion(array $data)
    {
        $sql = "
            INSERT INTO promotions (
                code,
                name,
                discount_type,
                discount_value,
                discount_percent,
                start_date,
                end_date,
                min_amount,
                status,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $discountPercentCompat = $data['discount_type'] === 'percent'
            ? (float)$data['discount_value']
            : 0;

        $stmt->bind_param(
            "sssddssd",
            $data['code'],
            $data['name'],
            $data['discount_type'],
            $data['discount_value'],
            $discountPercentCompat,
            $data['start_date'],
            $data['end_date'],
            $data['min_amount']
        );

        return $stmt->execute() ? (int)$this->conn->insert_id : false;
    }

    public function updatePromotion(int $promotionId, array $data)
    {
        $sql = "
            UPDATE promotions
            SET
                code = ?,
                name = ?,
                discount_type = ?,
                discount_value = ?,
                discount_percent = ?,
                start_date = ?,
                end_date = ?,
                min_amount = ?
            WHERE promotion_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $discountPercentCompat = $data['discount_type'] === 'percent'
            ? (float)$data['discount_value']
            : 0;

        $stmt->bind_param(
            "sssddssdi",
            $data['code'],
            $data['name'],
            $data['discount_type'],
            $data['discount_value'],
            $discountPercentCompat,
            $data['start_date'],
            $data['end_date'],
            $data['min_amount'],
            $promotionId
        );

        return $stmt->execute();
    }

    public function deletePromotion(int $promotionId)
    {
        $sql = "UPDATE promotions SET status = -1 WHERE promotion_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $promotionId);
        return $stmt->execute();
    }
}