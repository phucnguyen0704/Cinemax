<?php

class Genre
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllGenres()
    {
        $sql = "SELECT genre_id, name, status
                FROM genres
                WHERE status = 1
                ORDER BY name ASC";
        $result = $this->conn->query($sql);

        $genres = [];
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
        return $genres;
    }

    public function countActiveByIds(array $ids): int
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn($x) => $x > 0));
        if (count($ids) === 0) return 0;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $sql = "SELECT COUNT(*) AS cnt
                FROM genres
                WHERE status = 1 AND genre_id IN ($placeholders)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return (int)($row['cnt'] ?? 0);
    }
}