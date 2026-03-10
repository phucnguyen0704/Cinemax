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
        return $this->genreModel->getGenresForAdmin($q);
    }

    public function getAllGenres(): array
    {
        return $this->genreModel->getAllGenres();
    }

    public function create(string $name): bool
    {
        $name = trim($name);

        if ($name === '') {
            throw new Exception("Tên thể loại không được rỗng.");
        }

        if (mb_strlen($name) > 100) {
            throw new Exception("Tên thể loại không được vượt quá 100 ký tự.");
        }

        if ($this->genreModel->existsActiveName($name)) {
            throw new Exception("Thể loại đã tồn tại.");
        }

        $ok = $this->genreModel->createGenre($name);
        if (!$ok) {
            throw new Exception("Không thể thêm thể loại.");
        }

        return true;
    }

    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new Exception("ID thể loại không hợp lệ.");
        }

        $ok = $this->genreModel->deleteGenre($id);
        if (!$ok) {
            throw new Exception("Không thể xóa thể loại.");
        }

        return true;
    }
}