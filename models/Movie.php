<?php

class Movie
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // List admin: search + filter genre + filter statusText -> status int
    public function getMoviesForAdmin($search = '', $genreId = null, $status = null)
    {
        $search = trim((string)$search);

        $sql = "
            SELECT
                m.*,
                GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS genre_names
            FROM movies m
            LEFT JOIN movie_genres mg ON mg.movie_id = m.movie_id
            LEFT JOIN genres g ON g.genre_id = mg.genre_id AND g.status = 1
            WHERE 1=1
        ";

        $types = "";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (m.title LIKE ? OR m.description LIKE ?)";
            $like = '%' . $search . '%';
            $types .= "ss";
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== null) {
            $sql .= " AND m.status = ?";
            $types .= "i";
            $params[] = (int)$status;
        }

        if ($genreId !== null) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM movie_genres mg2
                WHERE mg2.movie_id = m.movie_id AND mg2.genre_id = ?
            )";
            $types .= "i";
            $params[] = (int)$genreId;
        }

        $sql .= " GROUP BY m.movie_id ORDER BY m.movie_id DESC";

        $stmt = $this->conn->prepare($sql);
        if ($types !== "") $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();
        $movies = [];
        while ($row = $result->fetch_assoc()) $movies[] = $row;
        return $movies;
    }

    public function getMovieById($movieId)
    {
        $sql = "SELECT * FROM movies WHERE movie_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        $movie = $stmt->get_result()->fetch_assoc();

        if (!$movie) return null;

        // genres of movie
        $sqlG = "SELECT g.genre_id, g.name
                 FROM movie_genres mg
                 JOIN genres g ON g.genre_id = mg.genre_id
                 WHERE mg.movie_id = ? AND g.status = 1
                 ORDER BY g.name ASC";
        $stmtG = $this->conn->prepare($sqlG);
        $stmtG->bind_param("i", $movieId);
        $stmtG->execute();
        $genres = [];
        $rsG = $stmtG->get_result();
        while ($row = $rsG->fetch_assoc()) $genres[] = $row;

        $movie['genres'] = $genres;
        return $movie;
    }

    // Transaction: insert movies + insert movie_genres
    public function createMovieWithGenres($data, array $genreIds)
    {
        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO movies (title, description, duration_min, release_date, poster_url, trailer_url, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);

            $title = $data['title'];
            $description = $data['description'];
            $durationMin = (int)$data['duration_min'];
            $releaseDate = $data['release_date']; // null hoặc YYYY-MM-DD
            $posterUrl = $data['poster_url'];
            $trailerUrl = $data['trailer_url'];
            $status = (int)$data['status'];

            $stmt->bind_param("ssisssi", $title, $description, $durationMin, $releaseDate, $posterUrl, $trailerUrl, $status);

            if (!$stmt->execute()) throw new Exception("Insert movies failed.");

            $movieId = (int)$this->conn->insert_id;

            if (count($genreIds) > 0) {
                $sqlMG = "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)";
                $stmtMG = $this->conn->prepare($sqlMG);
                foreach ($genreIds as $gid) {
                    $gid = (int)$gid;
                    $stmtMG->bind_param("ii", $movieId, $gid);
                    if (!$stmtMG->execute()) throw new Exception("Insert movie_genres failed.");
                }
            }

            $this->conn->commit();
            return $movieId;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Transaction: update movies + replace movie_genres
    public function updateMovieWithGenres($movieId, $data, array $genreIds)
    {
        $this->conn->begin_transaction();
        try {
            $sql = "UPDATE movies
                    SET title = ?, description = ?, duration_min = ?, release_date = ?, poster_url = ?, trailer_url = ?, status = ?
                    WHERE movie_id = ?";
            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param(
                "ssisssii",
                $data['title'],
                $data['description'],
                $data['duration_min'],
                $data['release_date'],
                $data['poster_url'],
                $data['trailer_url'],
                $data['status'],
                $movieId
            );

            if (!$stmt->execute()) throw new Exception("Update movies failed.");

            // replace pivot
            $stmtDel = $this->conn->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
            $stmtDel->bind_param("i", $movieId);
            if (!$stmtDel->execute()) throw new Exception("Delete old movie_genres failed.");

            if (count($genreIds) > 0) {
                $stmtMG = $this->conn->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
                foreach ($genreIds as $gid) {
                    $gid = (int)$gid;
                    $stmtMG->bind_param("ii", $movieId, $gid);
                    if (!$stmtMG->execute()) throw new Exception("Insert movie_genres failed.");
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // soft delete: status = -1
    public function deleteMovie($movieId)
    {
        $sql = "UPDATE movies SET status = -1 WHERE movie_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        return $stmt->execute();
    }
}