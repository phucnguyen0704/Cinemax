<?php
// models/Movie.php

class Movie
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Map status text UI -> int DB
    private function mapStatusTextToInt($statusText)
    {
        if ($statusText === 'Đang chiếu') return 1;
        if ($statusText === 'Sắp chiếu') return 0;
        if ($statusText === 'Ngừng chiếu') return -1;
        return null;
    }

    /**
     * Dùng cho UI admin list + filter:
     * - search: title LIKE
     * - genreId: lọc theo movie_genres.genre_id
     * - statusText: "Đang chiếu"/"Sắp chiếu"/"Ngừng chiếu"
     *
     * Return: fields alias PascalCase + GenresText
     */
    public function getMoviesForAdmin($search = '', $genreId = null, $statusText = '')
    {
        $search = trim((string)$search);
        $status = $this->mapStatusTextToInt($statusText);

        $types = "";
        $params = [];

        $sql = "SELECT
                    m.movie_id AS MovieID,
                    m.title AS Title,
                    m.description AS Description,
                    m.duration_min AS DurationMin,
                    m.release_date AS ReleaseDate,
                    m.poster_url AS PosterUrl,
                    m.trailer_url AS TrailerUrl,
                    m.status AS Status,
                    m.created_at AS CreatedAt,
                    GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS GenresText
                FROM movies m
                LEFT JOIN movie_genres mg ON mg.movie_id = m.movie_id
                LEFT JOIN genres g ON g.genre_id = mg.genre_id AND g.status = 1
                WHERE 1=1 ";

        if ($status !== null) {
            $sql .= " AND m.status = ? ";
            $types .= "i";
            $params[] = (int)$status;
        }

        if ($genreId !== null) {
            $sql .= " AND mg.genre_id = ? ";
            $types .= "i";
            $params[] = (int)$genreId;
        }

        if ($search !== '') {
            $sql .= " AND m.title LIKE ? ";
            $types .= "s";
            $params[] = "%" . $search . "%";
        }

        $sql .= " GROUP BY m.movie_id
                  ORDER BY m.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        if ($types !== "") {
            $bind = [];
            $bind[] = $types;
            for ($i = 0; $i < count($params); $i++) {
                $bind[] = &$params[$i];
            }
            call_user_func_array([$stmt, 'bind_param'], $bind);
        }

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }

        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        return $movies;
    }

    public function getMovieById($movieId)
    {
        $sql = "SELECT
                    movie_id AS MovieID,
                    title AS Title,
                    description AS Description,
                    duration_min AS DurationMin,
                    release_date AS ReleaseDate,
                    poster_url AS PosterUrl,
                    trailer_url AS TrailerUrl,
                    status AS Status,
                    created_at AS CreatedAt
                FROM movies
                WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $movieId);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    // ===== Movie <-> Genres mapping =====

    public function getGenreIdsByMovieId($movieId)
    {
        $sql = "SELECT genre_id FROM movie_genres WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $movieId);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("SQL Get Result Error: " . $stmt->error);
        }

        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int)$row['genre_id'];
        }
        return $ids;
    }

    private function setGenresForMovie($movieId, $genreIds)
    {
        // delete old
        $delSql = "DELETE FROM movie_genres WHERE movie_id = ?";
        $delStmt = $this->conn->prepare($delSql);
        if (!$delStmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $delStmt->bind_param("i", $movieId);
        if (!$delStmt->execute()) {
            throw new Exception("SQL Execute Error: " . $delStmt->error);
        }

        if (!is_array($genreIds) || count($genreIds) === 0) {
            return true;
        }

        $insSql = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
        $insStmt = $this->conn->prepare($insSql);
        if (!$insStmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        foreach ($genreIds as $gid) {
            if (!is_numeric($gid)) continue;
            $gid = (int)$gid;
            if ($gid <= 0) continue;

            $insStmt->bind_param("ii", $movieId, $gid);
            if (!$insStmt->execute()) {
                throw new Exception("SQL Execute Error: " . $insStmt->error);
            }
        }

        return true;
    }

    // ===== CRUD + transaction with genres =====

    public function createMovieWithGenres($movieData, $genreIds)
    {
        $title = $movieData['title'] ?? '';
        $description = $movieData['description'] ?? null;
        $durationMin = (int)($movieData['duration_min'] ?? 0);
        $releaseDate = $movieData['release_date'] ?? null;
        $posterUrl = $movieData['poster_url'] ?? null;
        $trailerUrl = $movieData['trailer_url'] ?? null;
        $status = (int)($movieData['status'] ?? 1);

        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO movies (title, description, duration_min, release_date, poster_url, trailer_url, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("SQL Error: " . $this->conn->error);
            }
            $stmt->bind_param("ssisssi", $title, $description, $durationMin, $releaseDate, $posterUrl, $trailerUrl, $status);

            if (!$stmt->execute()) {
                throw new Exception("SQL Execute Error: " . $stmt->error);
            }

            $movieId = (int)$this->conn->insert_id;

            $this->setGenresForMovie($movieId, $genreIds);

            $this->conn->commit();
            return $movieId;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e; // để service/controller bắt và show msg
        }
    }

    public function updateMovieWithGenres($movieId, $movieData, $genreIds)
    {
        $movieId = (int)$movieId;
        $title = $movieData['title'] ?? '';
        $description = $movieData['description'] ?? null;
        $durationMin = (int)($movieData['duration_min'] ?? 0);
        $releaseDate = $movieData['release_date'] ?? null;
        $posterUrl = $movieData['poster_url'] ?? null;
        $trailerUrl = $movieData['trailer_url'] ?? null;
        $status = (int)($movieData['status'] ?? 1);

        $this->conn->begin_transaction();
        try {
            $sql = "UPDATE movies
                    SET title = ?, description = ?, duration_min = ?, release_date = ?, poster_url = ?, trailer_url = ?, status = ?
                    WHERE movie_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("SQL Error: " . $this->conn->error);
            }
            $stmt->bind_param("ssisssii", $title, $description, $durationMin, $releaseDate, $posterUrl, $trailerUrl, $status, $movieId);

            if (!$stmt->execute()) {
                throw new Exception("SQL Execute Error: " . $stmt->error);
            }

            $this->setGenresForMovie($movieId, $genreIds);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function deleteMovie($movieId)
    {
        // soft delete => status = -1
        $sql = "UPDATE movies SET status = -1 WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $movieId);

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }
        return true;
    }
}