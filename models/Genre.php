<?php

class Genre
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function getAllGenres(string $q = ''): array
    {
        $q = trim($q);

        $sql = "SELECT genre_id, name, status
                FROM genres
                WHERE status = 1";

        $types = "";
        $params = [];

        if ($q !== '') {
            $sql .= " AND name LIKE ?";
            $types .= "s";
            $params[] = "%{$q}%";
        }

        $sql .= " ORDER BY name ASC";

        $stmt = $this->conn->prepare($sql);
        if ($types !== '') $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $rs = $stmt->get_result();
        $out = [];
        while ($row = $rs->fetch_assoc()) $out[] = $row;
        return $out;
    }

    public function createGenre(string $name): bool
    {
        $sql = "INSERT INTO genres (name, status) VALUES (?, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    // soft delete theo schema: status = 0
    public function deleteGenre(int $id): bool
    {
        $sql = "UPDATE genres SET status = 0 WHERE genre_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function countActiveByIds(array $ids): int
    {
        if (empty($ids)) return 0;

        $ids = array_values(array_unique(array_map('intval', $ids)));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $sql = "SELECT COUNT(*) AS c FROM genres WHERE status = 1 AND genre_id IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['c'] ?? 0);
    }
}