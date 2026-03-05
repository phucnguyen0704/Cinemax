<?php

class MovieService
{
    private $movieModel;
    private $genreModel;

    public function __construct($movieModel, $genreModel)
    {
        $this->movieModel = $movieModel;
        $this->genreModel = $genreModel;
    }

    public function getAllGenres()
    {
        return $this->genreModel->getAllGenres();
    }

    public function listMoviesAdmin($search = '', $genreId = null, $statusText = '')
    {
        $statusMap = [
            '' => null,
            'Đang chiếu' => 1,
            'Sắp chiếu' => 0,
            'Ngừng chiếu' => -1,
        ];
        if (!array_key_exists($statusText, $statusMap)) {
            throw new InvalidArgumentException("Trạng thái lọc không hợp lệ.");
        }

        if ($genreId !== null && $genreId !== '') {
            if (!is_numeric($genreId) || (int)$genreId <= 0) throw new InvalidArgumentException("Genre ID không hợp lệ.");
            $genreId = (int)$genreId;
        } else {
            $genreId = null;
        }

        return $this->movieModel->getMoviesForAdmin($search, $genreId, $statusMap[$statusText]);
    }

    public function createMovie($movieData, $genreIds = [])
    {
        $clean = $this->sanitizeMoviePayload($movieData, $genreIds);

        if (count($clean['genreIds']) > 0) {
            $cnt = $this->genreModel->countActiveByIds($clean['genreIds']);
            if ($cnt !== count($clean['genreIds'])) {
                throw new InvalidArgumentException("Có thể loại không tồn tại hoặc đã bị khóa.");
            }
        }

        $movieId = $this->movieModel->createMovieWithGenres($clean['data'], $clean['genreIds']);
        if (!$movieId) throw new Exception("Không thể thêm phim.");
        return $movieId;
    }

    public function updateMovie($movieId, $movieData, $genreIds = [])
    {
        if (!is_numeric($movieId) || (int)$movieId <= 0) throw new InvalidArgumentException("Movie ID không hợp lệ.");
        $movieId = (int)$movieId;

        $clean = $this->sanitizeMoviePayload($movieData, $genreIds);

        if (count($clean['genreIds']) > 0) {
            $cnt = $this->genreModel->countActiveByIds($clean['genreIds']);
            if ($cnt !== count($clean['genreIds'])) {
                throw new InvalidArgumentException("Có thể loại không tồn tại hoặc đã bị khóa.");
            }
        }

        $ok = $this->movieModel->updateMovieWithGenres($movieId, $clean['data'], $clean['genreIds']);
        if (!$ok) throw new Exception("Không thể cập nhật phim.");
        return true;
    }

    public function deleteMovie($movieId)
    {
        if (!is_numeric($movieId) || (int)$movieId <= 0) throw new InvalidArgumentException("Movie ID không hợp lệ.");
        if (!$this->movieModel->deleteMovie((int)$movieId)) throw new Exception("Không thể xóa phim.");
        return true;
    }

    private function sanitizeMoviePayload($movieData, $genreIds)
    {
        $title       = trim((string)($movieData['title'] ?? ''));
        $description = (string)($movieData['description'] ?? '');
        $durationMin = $movieData['duration_min'] ?? null;
        $releaseDate = $movieData['release_date'] ?? null;
        $posterUrl   = $movieData['poster_url'] ?? null;
        $trailerUrl  = $movieData['trailer_url'] ?? null;
        $status      = $movieData['status'] ?? null;

        if ($title === '') throw new InvalidArgumentException("Tên phim không được để trống.");
        if (mb_strlen($title) > 255) throw new InvalidArgumentException("Tên phim không được vượt quá 255 ký tự.");

        if ($durationMin === null || !is_numeric($durationMin) || (int)$durationMin <= 0) {
            throw new InvalidArgumentException("duration_min không hợp lệ.");
        }
        $durationMin = (int)$durationMin;

        if ($status === null || !is_numeric($status)) throw new InvalidArgumentException("status không hợp lệ.");
        $status = (int)$status;
        if (!in_array($status, [1, 0, -1], true)) throw new InvalidArgumentException("status không hợp lệ.");

        $releaseDate = $releaseDate !== null ? trim((string)$releaseDate) : '';
        if ($releaseDate === '') $releaseDate = null;
        elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $releaseDate)) {
            throw new InvalidArgumentException("release_date phải dạng YYYY-MM-DD.");
        }

        $posterUrl = $posterUrl !== null ? trim((string)$posterUrl) : null;
        if ($posterUrl === '') $posterUrl = null;

        $trailerUrl = $trailerUrl !== null ? trim((string)$trailerUrl) : null;
        if ($trailerUrl === '') $trailerUrl = null;

        // normalize genre ids
        $g = [];
        if (is_array($genreIds)) {
            foreach ($genreIds as $gid) {
                if (!is_numeric($gid)) continue;
                $gid = (int)$gid;
                if ($gid > 0) $g[$gid] = true;
            }
        }
        $genreIds = array_keys($g);

        return [
            'data' => [
                'title' => $title,
                'description' => $description,
                'duration_min' => $durationMin,
                'release_date' => $releaseDate,
                'poster_url' => $posterUrl,
                'trailer_url' => $trailerUrl,
                'status' => $status,
            ],
            'genreIds' => $genreIds
        ];
    }
}