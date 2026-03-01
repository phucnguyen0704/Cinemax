<?php
// services/MovieService.php

require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../models/Genre.php';

class MovieService
{
    private $conn;
    private $movieModel;
    private $genreModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->movieModel = new Movie($conn);
        $this->genreModel = new Genre($conn);
    }

    /**
     * Dữ liệu cho UI filter movies admin:
     * - search: string (title)
     * - genreId: int|null
     * - statusText: "" | "Đang chiếu" | "Sắp chiếu" | "Ngừng chiếu"
     */
    public function listMoviesAdmin($search = '', $genreId = null, $statusText = '')
    {
        return $this->movieModel->getMoviesForAdmin($search, $genreId, $statusText);
    }

    public function getMovieDetail($movieId)
    {
        $movieId = (int)$movieId;
        if ($movieId <= 0) return null;

        // Nếu bạn muốn detail có kèm genres thì để Movie model tự attach
        // hoặc bạn có thể gọi thêm MovieGenre model ở đây (nhưng SQL vẫn ở model)
        return $this->movieModel->getMovieById($movieId);
    }

    public function createMovie($movieData, $genreIds = [])
    {
        // Validate tối thiểu
        $title = trim($movieData['title'] ?? '');
        $duration = (int)($movieData['duration_min'] ?? 0);
        $status = (int)($movieData['status'] ?? 1);

        if ($title === '') return ["ok" => false, "msg" => "Tên phim không được để trống"];
        if ($duration <= 0) return ["ok" => false, "msg" => "Thời lượng phải > 0"];
        if (!in_array($status, [1, 0, -1], true)) return ["ok" => false, "msg" => "Trạng thái không hợp lệ"];

        $newId = $this->movieModel->createMovieWithGenres($movieData, $genreIds);
        if (!$newId) return ["ok" => false, "msg" => "Thêm phim thất bại"];

        return ["ok" => true, "movie_id" => $newId];
    }

    public function updateMovie($movieId, $movieData, $genreIds = [])
    {
        $movieId = (int)$movieId;
        if ($movieId <= 0) return ["ok" => false, "msg" => "Movie ID không hợp lệ"];

        $title = trim($movieData['title'] ?? '');
        $duration = (int)($movieData['duration_min'] ?? 0);
        $status = (int)($movieData['status'] ?? 1);

        if ($title === '') return ["ok" => false, "msg" => "Tên phim không được để trống"];
        if ($duration <= 0) return ["ok" => false, "msg" => "Thời lượng phải > 0"];
        if (!in_array($status, [1, 0, -1], true)) return ["ok" => false, "msg" => "Trạng thái không hợp lệ"];

        $ok = $this->movieModel->updateMovieWithGenres($movieId, $movieData, $genreIds);
        if (!$ok) return ["ok" => false, "msg" => "Cập nhật phim thất bại"];

        return ["ok" => true];
    }

    public function deleteMovie($movieId)
    {
        $movieId = (int)$movieId;
        if ($movieId <= 0) return ["ok" => false, "msg" => "Movie ID không hợp lệ"];

        $ok = $this->movieModel->deleteMovie($movieId); // soft status=-1
        if (!$ok) return ["ok" => false, "msg" => "Xóa phim thất bại"];

        return ["ok" => true];
    }

    public function getAllGenres()
    {
        return $this->genreModel->getAllGenres();
    }
}