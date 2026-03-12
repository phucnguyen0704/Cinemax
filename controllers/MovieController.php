<?php

require_once __DIR__ . '/../services/MovieService.php';

class MovieController
{
    private MovieService $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    private function uploadPoster(string $fieldName = 'poster'): ?string
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload poster thất bại.");
        }

        $tmpName = $_FILES[$fieldName]['tmp_name'];
        $originalName = $_FILES[$fieldName]['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            throw new Exception("Poster chỉ hỗ trợ jpg, jpeg, png, webp.");
        }

        $folder = __DIR__ . '/../assets/posters/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $fileName = time() . '_' . uniqid() . '.' . $ext;
        $destination = $folder . $fileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new Exception("Không thể lưu file poster.");
        }

        return 'assets/posters/' . $fileName;
    }

    public function create(): void
    {
        try {
            $posterPath = $this->uploadPoster('poster');

            $movieData = [
                'title'        => $_POST['title'] ?? '',
                'description'  => $_POST['description'] ?? '',
                'duration_min' => $_POST['duration_min'] ?? null,
                'release_date' => $_POST['release_date'] ?? null,
                'poster_url'   => $posterPath,
                'trailer_url'  => $_POST['trailer_url'] ?? null,
                'status'       => $_POST['status'] ?? 1,
                'director'     => $_POST['director'] ?? '',
                'actors'       => $_POST['actors'] ?? '',
            ];

            $genreIds = $_POST['genre_ids'] ?? [];

            $this->movieService->createMovie($movieData, $genreIds);

            header('Location: ../../views/admin/index.php?page=movies&add=1');
            exit;
        } catch (Exception $e) {
            header('Location: ../../views/admin/index.php?page=movies&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function update($movieId): void
    {
        try {
            $posterPath = $this->uploadPoster('poster');

            $movieData = [
                'title'        => $_POST['title'] ?? '',
                'description'  => $_POST['description'] ?? '',
                'duration_min' => $_POST['duration_min'] ?? null,
                'release_date' => $_POST['release_date'] ?? null,
                'poster_url'   => $posterPath ?: ($_POST['existing_poster_url'] ?? null),
                'trailer_url'  => $_POST['trailer_url'] ?? null,
                'status'       => $_POST['status'] ?? 1,
                'director'     => $_POST['director'] ?? '',
                'actors'       => $_POST['actors'] ?? '',
            ];

            $genreIds = $_POST['genre_ids'] ?? [];

            $this->movieService->updateMovie($movieId, $movieData, $genreIds);

            header('Location: ../../views/admin/index.php?page=movies&update=1');
            exit;
        } catch (Exception $e) {
            header('Location: ../../views/admin/index.php?page=movies&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function delete($movieId): void
    {
        try {
            $this->movieService->deleteMovie($movieId);

            header('Location: ../../views/admin/index.php?page=movies&delete=1');
            exit;
        } catch (Exception $e) {
            header('Location: ../../views/admin/index.php?page=movies&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}