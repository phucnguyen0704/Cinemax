<?php

class MovieGenre
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // lấy list genre (id+name) của 1 movie
    public function getGenresByMovieId($movieId)
    {
        $sql = "SELECT g.genre_id, g.name
                FROM movie_genres mg
                JOIN genres g ON g.genre_id = mg.genre_id
                WHERE mg.movie_id = ? AND g.status = 1
                ORDER BY g.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();

        $result = $stmt->get_result();
        $genres = [];
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
        return $genres;
    }

    // lọc movie theo genre
    public function getMoviesByGenreId($genreId)
    {
        $sql = "SELECT DISTINCT m.*
                FROM movies m
                JOIN movie_genres mg ON mg.movie_id = m.movie_id
                WHERE mg.genre_id = ?
                  AND m.status <> -1
                ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $genreId);
        $stmt->execute();

        $result = $stmt->get_result();
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        return $movies;
    }

    // xóa hết genre mapping của movie
    public function deleteByMovieId($movieId)
    {
        $sql = "DELETE FROM movie_genres WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        return $stmt->execute();
    }

    // add 1 mapping
    public function addMapping($movieId, $genreId)
    {
        $sql = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $movieId, $genreId);
        return $stmt->execute();
    }

    // set lại list genre cho movie (delete old -> insert new)
    public function setGenresForMovie($movieId, $genreIds)
    {
        // không tự transaction ở đây (để Service quản lý)
        if (!$this->deleteByMovieId($movieId)) return false;

        if (!is_array($genreIds) || count($genreIds) === 0) {
            return true; // movie không có genre cũng ok
        }

        foreach ($genreIds as $gid) {
            $gid = (int)$gid;
            if ($gid <= 0) continue;
            if (!$this->addMapping($movieId, $gid)) return false;
        }
        return true;
    }
}
