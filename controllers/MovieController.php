<?php
require_once __DIR__ . '/../services/MovieService.php';

class MovieController
{
    private MovieService $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function create(): void
    {
        try {
            $movieData = [
                'title'        => $_POST['title'] ?? '',
                'director'     => $_POST['director'] ?? '',
                'cast'         => $_POST['cast'] ?? '',
                'description'  => $_POST['description'] ?? '',
                'duration_min' => $_POST['duration_min'] ?? null,
                'release_date' => $_POST['release_date'] ?? null,
                'poster_url'   => $_POST['poster_url'] ?? null,
                'trailer_url'  => $_POST['trailer_url'] ?? null,
                'status'       => $_POST['status'] ?? 1,
            ];
<<<<<<< Updated upstream

            $genreIds = $_POST['genre_ids'] ?? []; // name="genre_ids[]"
=======
>>>>>>> Stashed changes

            $genreIds   = $_POST['genre_ids'] ?? [];
            $posterFile = $_FILES['poster_file'] ?? null;

            $this->movieService->createMovie($movieData, $genreIds, $posterFile);

            header('Location: index.php?page=movies&success=' . urlencode('Thêm phim thành công.'));
            exit;
        } catch (Throwable $e) {
            header('Location: index.php?page=movies&error=' . urlencode($e->getMessage()) . '&open_modal=add');
            exit;
        }
    }

    public function update($movieId): void
    {
        try {
            $movieId = (int)$movieId;

            $movieData = [
                'title'        => $_POST['title'] ?? '',
                'director'     => $_POST['director'] ?? '',
                'cast'         => $_POST['cast'] ?? '',
                'description'  => $_POST['description'] ?? '',
                'duration_min' => $_POST['duration_min'] ?? null,
                'release_date' => $_POST['release_date'] ?? null,
                'poster_url'   => $_POST['poster_url'] ?? null,
                'trailer_url'  => $_POST['trailer_url'] ?? null,
                'status'       => $_POST['status'] ?? 1,
            ];
<<<<<<< Updated upstream

            $genreIds = $_POST['genre_ids'] ?? [];
=======
>>>>>>> Stashed changes

            $genreIds   = $_POST['genre_ids'] ?? [];
            $posterFile = $_FILES['poster_file'] ?? null;

            $this->movieService->updateMovie($movieId, $movieData, $genreIds, $posterFile);

            header('Location: index.php?page=movies&success=' . urlencode('Cập nhật phim thành công.'));
            exit;
        } catch (Throwable $e) {
            header('Location: index.php?page=movies&error=' . urlencode($e->getMessage()) . '&open_modal=edit&id=' . (int)$movieId);
            exit;
        }
    }

    public function delete($movieId): void
    {
        try {
            $movieId = (int)$movieId;
            $this->movieService->deleteMovie($movieId);

            header('Location: index.php?page=movies&success=' . urlencode('Xóa phim thành công.'));
            exit;
        } catch (Throwable $e) {
            header('Location: index.php?page=movies&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}