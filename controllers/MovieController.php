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
                'description'  => $_POST['description'] ?? '',
                'duration_min' => $_POST['duration_min'] ?? null,
                'release_date' => $_POST['release_date'] ?? null,
                'poster_url'   => $_POST['poster_url'] ?? null,
                'trailer_url'  => $_POST['trailer_url'] ?? null,
                'status'       => $_POST['status'] ?? 1,
            ];

            $genreIds = $_POST['genre_ids'] ?? []; // name="genre_ids[]"

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
            $movieData = [
                'title'        => $_POST['title'] ?? '',
                'description'  => $_POST['description'] ?? '',
                'duration_min' => $_POST['duration_min'] ?? null,
                'release_date' => $_POST['release_date'] ?? null,
                'poster_url'   => $_POST['poster_url'] ?? null,
                'trailer_url'  => $_POST['trailer_url'] ?? null,
                'status'       => $_POST['status'] ?? 1,
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