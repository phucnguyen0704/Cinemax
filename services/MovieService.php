<?php
require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../models/Genre.php';

class MovieService
{
    private $movieModel;
    private $genreModel;

    public function __construct($movieModel, $genreModel)
    {
        $this->movieModel = $movieModel;
        $this->genreModel = $genreModel;
    }

    // =========================
    // ADMIN LIST + FILTER
    // =========================
    public function listMoviesAdmin($search = '', $genreId = null, $statusText = '')
    {
        // validate filter (nhẹ thôi)
        if ($genreId !== null && (!is_numeric($genreId) || (int)$genreId <= 0)) {
            throw new InvalidArgumentException("Genre ID không hợp lệ.");
        }

        if ($statusText !== '' && !in_array($statusText, ['Đang chiếu', 'Sắp chiếu', 'Ngừng chiếu'], true)) {
            throw new InvalidArgumentException("Trạng thái lọc không hợp lệ.");
        }

        return $this->movieModel->getMoviesForAdmin($search, $genreId, $statusText);
    }

    public function getAllGenres()
    {
        return $this->genreModel->getAllGenres();
    }

    public function getMovieById($movieId)
    {
        if (empty($movieId) || !is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }
        return $this->movieModel->getMovieById((int)$movieId);
    }

    // =========================
    // CREATE / UPDATE / DELETE
    // =========================

    public function createMovie($movieData, $genreIds = [])
    {
        $clean = $this->sanitizeMoviePayload($movieData, $genreIds, false);

        // optional: kiểm tra title trùng (nếu bạn có hàm model)
        // if ($this->movieModel->existsTitle($clean['data']['title'], null)) ...

        $movieId = $this->movieModel->createMovieWithGenres($clean['data'], $clean['genreIds']);
        if (!$movieId) {
            throw new Exception("Không thể thêm phim.");
        }
        return $movieId;
    }

    public function updateMovie($movieId, $movieData, $genreIds = [])
    {
        if (empty($movieId) || !is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }

        $clean = $this->sanitizeMoviePayload($movieData, $genreIds, true);

        $ok = $this->movieModel->updateMovieWithGenres((int)$movieId, $clean['data'], $clean['genreIds']);
        if (!$ok) {
            throw new Exception("Không thể cập nhật phim.");
        }
        return true;
    }

    public function deleteMovie($movieId)
    {
        if (empty($movieId) || !is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }

        $ok = $this->movieModel->deleteMovie((int)$movieId); // soft delete status = -1
        if (!$ok) {
            throw new Exception("Không thể xóa phim.");
        }
        return true;
    }

    // =========================
    // HELPERS
    // =========================

    /**
     * Chuẩn hóa + validate payload movies theo DB hiện tại:
     * movies(title, description, duration_min, release_date, poster_url, trailer_url, status)
     * status: 1 now showing, 0 coming soon, -1 archived
     */
    private function sanitizeMoviePayload($movieData, $genreIds, $isUpdate)
    {
        $title = trim($movieData['title'] ?? '');
        $description = $movieData['description'] ?? null;
        $durationMin = $movieData['duration_min'] ?? null;
        $releaseDate = $movieData['release_date'] ?? null;
        $posterUrl = $movieData['poster_url'] ?? null;
        $trailerUrl = $movieData['trailer_url'] ?? null;
        $status = $movieData['status'] ?? null;

        if ($title === '') {
            throw new InvalidArgumentException("Tên phim không được để trống.");
        }
        if (mb_strlen($title) > 255) {
            throw new InvalidArgumentException("Tên phim không được vượt quá 255 ký tự.");
        }

        if ($durationMin === null || !is_numeric($durationMin) || (int)$durationMin <= 0) {
            throw new InvalidArgumentException("Thời lượng không hợp lệ.");
        }
        $durationMin = (int)$durationMin;

        if ($status === null || !is_numeric($status)) {
            throw new InvalidArgumentException("Trạng thái không hợp lệ.");
        }
        $status = (int)$status;
        if (!in_array($status, [1, 0, -1], true)) {
            throw new InvalidArgumentException("Trạng thái không hợp lệ.");
        }

        // release_date có thể null/'' hoặc YYYY-MM-DD
        $releaseDate = $releaseDate !== null ? trim((string)$releaseDate) : '';
        if ($releaseDate === '') {
            $releaseDate = null;
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $releaseDate)) {
                throw new InvalidArgumentException("Ngày chiếu không đúng định dạng (YYYY-MM-DD).");
            }
        }

        // URL fields optional
        $posterUrl = $posterUrl !== null ? trim((string)$posterUrl) : null;
        if ($posterUrl === '') $posterUrl = null;

        $trailerUrl = $trailerUrl !== null ? trim((string)$trailerUrl) : null;
        if ($trailerUrl === '') $trailerUrl = null;

        // genreIds: normalize int unique
        $normalizedGenreIds = [];
        if (is_array($genreIds)) {
            foreach ($genreIds as $gid) {
                if (!is_numeric($gid)) continue;
                $gid = (int)$gid;
                if ($gid > 0) $normalizedGenreIds[$gid] = true;
            }
        }
        $normalizedGenreIds = array_keys($normalizedGenreIds);

        // Nếu muốn bắt buộc chọn ít nhất 1 thể loại thì mở comment:
        // if (count($normalizedGenreIds) === 0) throw new InvalidArgumentException("Vui lòng chọn ít nhất 1 thể loại.");

        return [
            "data" => [
                "title" => $title,
                "description" => $description,
                "duration_min" => $durationMin,
                "release_date" => $releaseDate,
                "poster_url" => $posterUrl,
                "trailer_url" => $trailerUrl,
                "status" => $status,
            ],
            "genreIds" => $normalizedGenreIds
        ];
    }
}