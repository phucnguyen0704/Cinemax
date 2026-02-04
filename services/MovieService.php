<?php

require_once __DIR__ . '/../Movie.php';
require_once __DIR__ . '/../Genre.php';
require_once __DIR__ . '/../MovieGenre.php';
require_once __DIR__ . '/../Format.php';

class MovieService
{
    private $conn;
    private $movieModel;
    private $genreModel;
    private $movieGenreModel;
    private $formatModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->movieModel = new Movie($conn);
        $this->genreModel = new Genre($conn);
        $this->movieGenreModel = new MovieGenre($conn);
        $this->formatModel = new Format($conn);
    }

    // helper bind_param dynamic (mysqli cần truyền tham chiếu)
    private function bindParams($stmt, $types, $params)
    {
        if ($types === "" || empty($params)) return;

        $bindNames = [];
        $bindNames[] = $types;

        for ($i = 0; $i < count($params); $i++) {
            $bindNames[] = &$params[$i]; // by reference
        }

        call_user_func_array([$stmt, 'bind_param'], $bindNames);
    }

    /**
     * List movies theo filter:
     * - $status: (0/1/-1) hoặc null
     * - $genreId: int hoặc null
     * - $formatId: int hoặc null (lọc theo shows.format_id)
     * - $keyword: string hoặc null (search title)
     */
    public function listMovies($status = null, $genreId = null, $formatId = null, $keyword = null)
    {
        $types = "";
        $params = [];

        $sql = "SELECT DISTINCT m.*
                FROM movies m ";

        // join theo genre
        if ($genreId !== null) {
            $sql .= " JOIN movie_genres mg ON mg.movie_id = m.movie_id ";
        }

        // join theo format (qua shows)
        if ($formatId !== null) {
            $sql .= " JOIN shows s ON s.movie_id = m.movie_id ";
        }

        $sql .= " WHERE m.status <> -1 ";

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

        if ($formatId !== null) {
            $sql .= " AND s.format_id = ? AND s.status = 1 ";
            $types .= "i";
            $params[] = (int)$formatId;
        }

        if ($keyword !== null && trim($keyword) !== "") {
            $sql .= " AND m.title LIKE ? ";
            $types .= "s";
            $params[] = "%" . trim($keyword) . "%";
        }

        $sql .= " ORDER BY m.created_at DESC ";

        $stmt = $this->conn->prepare($sql);
        $this->bindParams($stmt, $types, $params);
        $stmt->execute();

        $result = $stmt->get_result();
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        return $movies;
    }

    // Chi tiết 1 movie + genres + formats đang có show
    public function getMovieDetail($movieId)
    {
        $movie = $this->movieModel->getMovieById((int)$movieId);
        if (!$movie) return null;

        $movie['genres'] = $this->movieGenreModel->getGenresByMovieId((int)$movieId);
        $movie['formats'] = $this->getFormatsByMovieId((int)$movieId);

        // status_text cho dễ hiển thị
        $movie['status_text'] = $this->mapMovieStatusText((int)$movie['status']);

        return $movie;
    }

    private function mapMovieStatusText($status)
    {
        if ($status === 1) return "NOW_SHOWING";
        if ($status === 0) return "COMING_SOON";
        return "STOPPED";
    }

    // Lấy formats mà movie có show (DISTINCT)
    public function getFormatsByMovieId($movieId)
    {
        $sql = "SELECT DISTINCT f.format_id, f.name
                FROM shows s
                JOIN formats f ON f.format_id = s.format_id
                WHERE s.movie_id = ?
                  AND s.status = 1
                  AND f.status = 1
                ORDER BY f.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movieId);
        $stmt->execute();

        $result = $stmt->get_result();
        $formats = [];
        while ($row = $result->fetch_assoc()) {
            $formats[] = $row;
        }
        return $formats;
    }

    // Tạo movie + set genres (transaction)
    public function createMovieWithGenres($movieData, $genreIds)
    {
        $this->conn->begin_transaction();
        try {
            $movieId = $this->movieModel->createMovie(
                $movieData['title'],
                $movieData['description'],
                (int)$movieData['duration_min'],
                $movieData['release_date'],
                $movieData['poster_url'],
                $movieData['trailer_url'],
                (int)$movieData['status']
            );

            if ($movieId === false || $movieId <= 0) {
                throw new Exception("Create movie failed");
            }

            $ok = $this->movieGenreModel->setGenresForMovie((int)$movieId, $genreIds);
            if (!$ok) {
                throw new Exception("Set genres failed");
            }

            $this->conn->commit();
            return (int)$movieId;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Update movie + set genres (transaction)
    public function updateMovieWithGenres($movieId, $movieData, $genreIds)
    {
        $this->conn->begin_transaction();
        try {
            $ok1 = $this->movieModel->updateMovie(
                (int)$movieId,
                $movieData['title'],
                $movieData['description'],
                (int)$movieData['duration_min'],
                $movieData['release_date'],
                $movieData['poster_url'],
                $movieData['trailer_url']
            );
            if (!$ok1) {
                throw new Exception("Update movie failed");
            }

            $ok2 = $this->movieGenreModel->setGenresForMovie((int)$movieId, $genreIds);
            if (!$ok2) {
                throw new Exception("Set genres failed");
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function changeMovieStatus($movieId, $status)
    {
        return $this->movieModel->updateMovieStatus((int)$movieId, (int)$status);
    }

    // Data dùng cho UI filter
    public function getAllGenres()
    {
        return $this->genreModel->getAllGenres();
    }

    public function getAllFormats()
    {
        return $this->formatModel->getAllFormats();
    }
}
