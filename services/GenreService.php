<?php
require_once __DIR__ . '/../models/Genre.php';

class GenreService
{
    private Genre $genreModel;

    public function __construct(Genre $genreModel)
    {
        $this->genreModel = $genreModel;
    }

    public function listAdmin(string $q = ''): array
    {
        return $this->genreModel->getAllGenres($q);
    }

    public function create(string $name): bool
    {
        $name = trim($name);
        if ($name === '') throw new Exception("Tên thể loại không được rỗng");
        if (mb_strlen($name) > 100) throw new Exception("Tên thể loại không được vượt quá 100 ký tự");
        return $this->genreModel->createGenre($name);
    }

    public function delete(int $id): bool
    {
        if ($id <= 0) throw new Exception("ID thể loại không hợp lệ");
        return $this->genreModel->deleteGenre($id);
    }
}