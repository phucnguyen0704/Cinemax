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
            if (!is_numeric($genreId) || (int)$genreId <= 0) {
                throw new InvalidArgumentException("Genre ID không hợp lệ.");
            }
            $genreId = (int)$genreId;
        } else {
            $genreId = null;
        }

        return $this->movieModel->getMoviesForAdmin($search, $genreId, $statusMap[$statusText]);
    }

    public function getMovieById($movieId)
    {
        if (!is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }

        return $this->movieModel->getMovieById((int)$movieId);
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
        if (!$movieId) {
            throw new Exception("Không thể thêm phim.");
        }

        return $movieId;
    }

    public function updateMovie($movieId, $movieData, $genreIds = [])
    {
        if (!is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }
        $movieId = (int)$movieId;

        $clean = $this->sanitizeMoviePayload($movieData, $genreIds);

        if (count($clean['genreIds']) > 0) {
            $cnt = $this->genreModel->countActiveByIds($clean['genreIds']);
            if ($cnt !== count($clean['genreIds'])) {
                throw new InvalidArgumentException("Có thể loại không tồn tại hoặc đã bị khóa.");
            }
        }

        $ok = $this->movieModel->updateMovieWithGenres($movieId, $clean['data'], $clean['genreIds']);
        if (!$ok) {
            throw new Exception("Không thể cập nhật phim.");
        }

        return true;
    }

    public function deleteMovie($movieId)
    {
        if (!is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }

        if (!$this->movieModel->deleteMovie((int)$movieId)) {
            throw new Exception("Không thể xóa phim.");
        }

        return true;
    }

    private function sanitizeMoviePayload($movieData, $genreIds)
    {
        $title       = trim((string)($movieData['title'] ?? ''));
        $description = trim((string)($movieData['description'] ?? ''));
        $durationMin = $movieData['duration_min'] ?? null;
        $releaseDate = $movieData['release_date'] ?? null;
        $posterUrl   = $movieData['poster_url'] ?? null;
        $trailerUrl  = $movieData['trailer_url'] ?? null;
        $status      = $movieData['status'] ?? null;
        $director    = trim((string)($movieData['director'] ?? ''));
        $actors      = trim((string)($movieData['actors'] ?? ''));

        // title
        if ($title === '') {
            throw new InvalidArgumentException("Tên phim không được để trống.");
        }
        if (mb_strlen($title) > 255) {
            throw new InvalidArgumentException("Tên phim không được vượt quá 255 ký tự.");
        }

        // description
        if ($description === '') {
            $description = '';
        }

        // duration
        if ($durationMin === null || !is_numeric($durationMin) || (int)$durationMin <= 0) {
            throw new InvalidArgumentException("Thời lượng phim không hợp lệ.");
        }
        $durationMin = (int)$durationMin;

        // status
        if ($status === null || !is_numeric($status)) {
            throw new InvalidArgumentException("Trạng thái phim không hợp lệ.");
        }
        $status = (int)$status;
        if (!in_array($status, [1, 0, -1], true)) {
            throw new InvalidArgumentException("Trạng thái phim không hợp lệ.");
        }

        // release date
        $releaseDate = $releaseDate !== null ? trim((string)$releaseDate) : '';
        if ($releaseDate === '') {
            $releaseDate = null;
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $releaseDate)) {
            throw new InvalidArgumentException("Ngày chiếu phải có định dạng YYYY-MM-DD.");
        }

        // poster_url
        $posterUrl = $posterUrl !== null ? trim((string)$posterUrl) : null;
        if ($posterUrl === '') {
            $posterUrl = null;
        }

        // trailer_url
        $trailerUrl = $trailerUrl !== null ? trim((string)$trailerUrl) : null;
        if ($trailerUrl === '') {
            $trailerUrl = null;
        }

        // director / actors
        if (mb_strlen($director) > 255) {
            throw new InvalidArgumentException("Tên đạo diễn quá dài.");
        }

        // genre ids normalize
        $normalizedGenreIds = [];
        if (is_array($genreIds)) {
            foreach ($genreIds as $gid) {
                if (!is_numeric($gid)) continue;
                $gid = (int)$gid;
                if ($gid > 0) {
                    $normalizedGenreIds[$gid] = true;
                }
            }
        }
        $normalizedGenreIds = array_keys($normalizedGenreIds);

        return [
            'data' => [
                'title'        => $title,
                'description'  => $description,
                'duration_min' => $durationMin,
                'release_date' => $releaseDate,
                'poster_url'   => $posterUrl,
                'trailer_url'  => $trailerUrl,
                'status'       => $status,
                'director'     => $director,
                'actors'       => $actors,
            ],
            'genreIds' => $normalizedGenreIds
        ];
    }
}