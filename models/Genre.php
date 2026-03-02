<?php
// models/Genre.php

class Genre
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllGenres()
    {
        $sql = "SELECT genre_id AS GenreID, name AS Name, status AS Status
                FROM genres
                WHERE status = 1
                ORDER BY genre_id";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        $genres = [];
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
        return $genres;
    }

    public function getGenreById($genreId)
    {
        $sql = "SELECT genre_id AS GenreID, name AS Name, status AS Status
                FROM genres
                WHERE genre_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $genreId);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    public function getGenreByName($name)
    {
        $sql = "SELECT genre_id AS GenreID, name AS Name, status AS Status
                FROM genres
                WHERE name = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("s", $name);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    public function createGenre($name)
    {
        $sql = "INSERT INTO genres (name, status) VALUES (?, 1)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("s", $name);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        return true;
    }

    public function updateGenre($genreId, $name)
    {
        $sql = "UPDATE genres SET name = ? WHERE genre_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("si", $name, $genreId);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        return true;
    }

    public function deleteGenre($genreId)
    {
        // Optional check: nếu genre đang được gán cho movie thì không cho xóa
        $checkSql = "SELECT COUNT(*) AS count
                     FROM movie_genres
                     WHERE genre_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        if (!$checkStmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $checkStmt->bind_param("i", $genreId);

        if (!$checkStmt->execute()) {
            throw new Exception("SQL Execute Error: " . $checkStmt->error);
        }
        $checkRes = $checkStmt->get_result();
        if (!$checkRes) {
            throw new Exception("SQL Get Result Error: " . $checkStmt->error);
        }
        $row = $checkRes->fetch_assoc();
        if (($row['count'] ?? 0) > 0) {
            throw new Exception("Không thể xóa thể loại này vì đang được dùng cho phim.");
        }

        $sql = "UPDATE genres SET status = 0 WHERE genre_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $genreId);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        return true;
    }
}