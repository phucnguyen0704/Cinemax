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

    // list admin filter
    public function listMoviesAdmin($search = '', $genreId = null, $statusText = '')
    {
        $statusMap = [
            ''            => null,
            'Đang chiếu'  => 1,
            'Sắp chiếu'   => 0,
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

    public function getMovieDetail($movieId)
    {
<<<<<<< Updated upstream
        if (!is_numeric($movieId) || (int)$movieId <= 0) throw new InvalidArgumentException("Movie ID không hợp lệ.");
        return $this->movieModel->getMovieById((int)$movieId);
    }

    public function createMovie($movieData, $genreIds = [], $imageUrls = [], $posterImageUrl = null)
    {
        $clean = $this->sanitizeMoviePayload($movieData, $genreIds, $imageUrls, $posterImageUrl);

        // validate genreIds tồn tại (nếu có truyền)
        if (count($clean['genreIds']) > 0) {
=======
        if (!is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }

        $movie = $this->movieModel->getMovieById((int)$movieId);
        if (!$movie) {
            throw new RuntimeException("Không tìm thấy phim.");
        }

        return $movie;
    }

    public function createMovie($movieData, $genreIds = [], $posterFile = null)
    {
        $clean = $this->sanitizeMoviePayload($movieData, $genreIds, $posterFile, null);

        if (count($clean['genreIds']) > 0 && method_exists($this->genreModel, 'countActiveByIds')) {
>>>>>>> Stashed changes
            $cnt = $this->genreModel->countActiveByIds($clean['genreIds']);
            if ($cnt !== count($clean['genreIds'])) {
                throw new InvalidArgumentException("Có thể loại không tồn tại hoặc đã bị khóa.");
            }
        }

<<<<<<< Updated upstream
        return $this->movieModel->createMovieWithGenresAndImages(
            $clean['data'],
            $clean['genreIds'],
            $clean['imageUrls'],
            $clean['posterImageUrl']
        );
    }

    public function updateMovie($movieId, $movieData, $genreIds = [], $imageUrls = [], $posterImageUrl = null)
=======
        $movieId = $this->movieModel->createMovieWithGenres($clean['data'], $clean['genreIds']);
        if (!$movieId) {
            throw new RuntimeException("Không thể thêm phim.");
        }

        return $movieId;
    }

    public function updateMovie($movieId, $movieData, $genreIds = [], $posterFile = null)
>>>>>>> Stashed changes
    {
        if (!is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }

        $movieId = (int)$movieId;
        $existingMovie = $this->movieModel->getMovieById($movieId);

<<<<<<< Updated upstream
        $clean = $this->sanitizeMoviePayload($movieData, $genreIds, $imageUrls, $posterImageUrl);
=======
        if (!$existingMovie) {
            throw new RuntimeException("Không tìm thấy phim cần cập nhật.");
        }
>>>>>>> Stashed changes

        $clean = $this->sanitizeMoviePayload($movieData, $genreIds, $posterFile, $existingMovie);

        if (count($clean['genreIds']) > 0 && method_exists($this->genreModel, 'countActiveByIds')) {
            $cnt = $this->genreModel->countActiveByIds($clean['genreIds']);
            if ($cnt !== count($clean['genreIds'])) {
                throw new InvalidArgumentException("Có thể loại không tồn tại hoặc đã bị khóa.");
            }
        }

<<<<<<< Updated upstream
        return $this->movieModel->updateMovieWithGenresAndImages(
            $movieId,
            $clean['data'],
            $clean['genreIds'],
            $clean['imageUrls'],
            $clean['posterImageUrl']
        );
=======
        $ok = $this->movieModel->updateMovieWithGenres($movieId, $clean['data'], $clean['genreIds']);
        if (!$ok) {
            throw new RuntimeException("Không thể cập nhật phim.");
        }

        return true;
>>>>>>> Stashed changes
    }

    public function deleteMovie($movieId)
    {
        if (!is_numeric($movieId) || (int)$movieId <= 0) {
            throw new InvalidArgumentException("Movie ID không hợp lệ.");
        }

        if (!$this->movieModel->deleteMovie((int)$movieId)) {
            throw new RuntimeException("Không thể xóa phim.");
        }

        return true;
    }

<<<<<<< Updated upstream
    private function sanitizeMoviePayload($movieData, $genreIds, $imageUrls, $posterImageUrl)
=======
    private function sanitizeMoviePayload($movieData, $genreIds, $posterFile = null, $existingMovie = null)
>>>>>>> Stashed changes
    {
        $title       = trim((string)($movieData['title'] ?? ''));
        $director    = trim((string)($movieData['director'] ?? ''));
        $cast        = trim((string)($movieData['cast'] ?? ''));
        $description = trim((string)($movieData['description'] ?? ''));
        $durationMin = $movieData['duration_min'] ?? null;
        $releaseDate = $movieData['release_date'] ?? null;
        $posterUrl   = $movieData['poster_url'] ?? null;
        $trailerUrl  = $movieData['trailer_url'] ?? null;
        $status      = $movieData['status'] ?? null;

        if ($title === '') {
            throw new InvalidArgumentException("Tên phim không được để trống.");
        }
        if (mb_strlen($title) > 255) {
            throw new InvalidArgumentException("Tên phim không được vượt quá 255 ký tự.");
        }

        if ($director === '') {
            throw new InvalidArgumentException("Tên đạo diễn không được để trống.");
        }
        if (mb_strlen($director) > 150) {
            throw new InvalidArgumentException("Tên đạo diễn không được vượt quá 150 ký tự.");
        }

        if ($cast === '') {
            throw new InvalidArgumentException("Diễn viên không được để trống.");
        }
        if (mb_strlen($cast) > 1000) {
            throw new InvalidArgumentException("Diễn viên không được vượt quá 1000 ký tự.");
        }

        if ($durationMin === null || !is_numeric($durationMin) || (int)$durationMin <= 0) {
            throw new InvalidArgumentException("Thời lượng phim không hợp lệ.");
        }
        $durationMin = (int)$durationMin;

        if ($status === null || !is_numeric($status)) {
            throw new InvalidArgumentException("Trạng thái không hợp lệ.");
        }
        $status = (int)$status;
        if (!in_array($status, [1, 0, -1], true)) {
            throw new InvalidArgumentException("Trạng thái không hợp lệ.");
        }

        $releaseDate = trim((string)($releaseDate ?? ''));
        if ($releaseDate === '') {
            throw new InvalidArgumentException("Ngày chiếu không được để trống.");
        }

        $d = DateTime::createFromFormat('Y-m-d', $releaseDate);
        $errors = DateTime::getLastErrors();

<<<<<<< Updated upstream
        // normalize genre_ids
        $g = [];
=======
        if (
            !$d ||
            $d->format('Y-m-d') !== $releaseDate ||
            ($errors && $errors['warning_count'] > 0) ||
            ($errors && $errors['error_count'] > 0)
        ) {
            throw new InvalidArgumentException("Ngày chiếu phải đúng định dạng YYYY-MM-DD và là ngày hợp lệ.");
        }

        $year = (int)substr($releaseDate, 0, 4);
        if ($year < 1900 || $year > 2100) {
            throw new InvalidArgumentException("Năm chiếu phải trong khoảng 1900 đến 2100.");
        }

        $posterUrl = trim((string)($posterUrl ?? ''));
        if ($posterUrl === '') {
            $posterUrl = null;
        }

        $trailerUrl = trim((string)($trailerUrl ?? ''));
        if ($trailerUrl === '') {
            $trailerUrl = null;
        }

        $uploadedPosterUrl = $this->handlePosterUpload($posterFile);
        if ($uploadedPosterUrl !== null) {
            $posterUrl = $uploadedPosterUrl;
        } elseif ($posterUrl === null && $existingMovie) {
            $posterUrl = $existingMovie['poster_url'] ?? null;
        }

        $normalizedGenreIds = [];
>>>>>>> Stashed changes
        if (is_array($genreIds)) {
            foreach ($genreIds as $gid) {
                if (!is_numeric($gid)) {
                    continue;
                }
                $gid = (int)$gid;
                if ($gid > 0) {
                    $normalizedGenreIds[$gid] = true;
                }
            }
        }
        $genreIds = array_keys($normalizedGenreIds);

        if (empty($genreIds)) {
            throw new InvalidArgumentException("Vui lòng chọn ít nhất 1 thể loại.");
        }

        // images
        $imgs = [];
        if (is_array($imageUrls)) {
            foreach ($imageUrls as $u) {
                $u = trim((string)$u);
                if ($u !== '') $imgs[] = $u;
            }
        }

        $posterImageUrl = $posterImageUrl !== null ? trim((string)$posterImageUrl) : null;
        if ($posterImageUrl === '') $posterImageUrl = null;

        return [
            'data' => [
                'title'        => $title,
                'director'     => $director,
                'cast'         => $cast,
                'description'  => $description,
                'duration_min' => $durationMin,
                'release_date' => $releaseDate,
                'poster_url'   => $posterUrl,
                'trailer_url'  => $trailerUrl,
                'status'       => $status,
            ],
            'genreIds' => $genreIds,
<<<<<<< Updated upstream
            'imageUrls' => $imgs,
            'posterImageUrl' => $posterImageUrl
=======
>>>>>>> Stashed changes
        ];
    }

    private function handlePosterUpload($posterFile): ?string
    {
        if (!is_array($posterFile) || !isset($posterFile['error'])) {
            return null;
        }

        if ($posterFile['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($posterFile['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Upload poster thất bại.");
        }

        if (($posterFile['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new RuntimeException("Poster vượt quá 5MB.");
        }

        $tmpPath = $posterFile['tmp_name'] ?? '';
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            throw new RuntimeException("File poster không hợp lệ.");
        }

        $mime = mime_content_type($tmpPath);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        if (!isset($allowed[$mime])) {
            throw new RuntimeException("Poster chỉ chấp nhận JPG, PNG, WEBP hoặc GIF.");
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/posters';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            throw new RuntimeException("Không tạo được thư mục upload poster.");
        }

        $fileName = 'poster_' . date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $allowed[$mime];
        $destination = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException("Không thể lưu file poster.");
        }

        return '/public/uploads/posters/' . $fileName;
    }
}