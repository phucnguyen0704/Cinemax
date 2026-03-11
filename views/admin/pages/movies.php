<?php
// views/admin/pages/movies.php
// File này được include từ views/admin/index.php nên đã có: $movieService

$search   = $_GET['q'] ?? '';
$genreId  = $_GET['genre_id'] ?? null;
$statusTx = $_GET['status_text'] ?? '';

$movies = $movieService->listMoviesAdmin($search, $genreId, $statusTx);
$genres = $movieService->getAllGenres();
?>

<section class="movies">

    <header class="admin-header">
        <h1>Quản lý phim</h1>
        <button class="btn-add" onclick="openModal('addMovieModal')">
            <span>Thêm phim mới</span>
        </button>
    </header>

    <div class="dashboard-content">
        <div class="dashboard-card">

            <!-- FILTER: đi qua index.php để router xử lý -->
            <form method="GET" action="index.php" class="filter-bar">
                <input type="hidden" name="page" value="movies">

                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    placeholder="Tìm tên phim..."
                    style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">

                <select
                    name="genre_id"
                    style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
                    <option value="">Tất cả thể loại</option>
                    <?php foreach ($genres as $g): ?>
                        <option value="<?= (int)$g['genre_id'] ?>"
                            <?= ((string)$g['genre_id'] === (string)($_GET['genre_id'] ?? '')) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php $st = $_GET['status_text'] ?? ''; ?>
                <select
                    name="status_text"
                    style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
                    <option value="" <?= $st===''?'selected':'' ?>>Tất cả trạng thái</option>
                    <option value="Đang chiếu" <?= $st==='Đang chiếu'?'selected':'' ?>>Đang chiếu</option>
                    <option value="Sắp chiếu" <?= $st==='Sắp chiếu'?'selected':'' ?>>Sắp chiếu</option>
                    <option value="Ngừng chiếu" <?= $st==='Ngừng chiếu'?'selected':'' ?>>Ngừng chiếu</option>
                </select>

                <button type="submit" class="btn-primary">Lọc</button>
                <a href="index.php?page=movies" class="btn-action">Reset</a>
            </form>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Poster</th>
                            <th>Tên phim</th>
                            <th>Thể loại</th>
                            <th>Thời lượng</th>
                            <th>Ngày chiếu</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($movies)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding:16px;">
                                Chưa có dữ liệu phim
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movies as $m): ?>
                            <tr>
                                <td>#<?= (int)$m['movie_id'] ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($m['poster_url'] ?: 'https://via.placeholder.com/40x60') ?>"
                                         style="width:40px;height:60px;object-fit:cover;border-radius:4px;">
                                </td>
                                <td><strong><?= htmlspecialchars($m['title']) ?></strong></td>
                                <td><?= htmlspecialchars($m['genre_names'] ?? '') ?></td>
                                <td><?= (int)$m['duration_min'] ?>p</td>
                                <td><?= $m['release_date'] ? date('d/m/Y', strtotime($m['release_date'])) : '' ?></td>
                                <td>
                                    <?php
                                        $badge = 'badge-success'; $txt = 'Đang chiếu';
                                        if ((int)$m['status'] === 0) { $badge = 'badge-warning'; $txt = 'Sắp chiếu'; }
                                        if ((int)$m['status'] === -1) { $badge = 'badge-danger'; $txt = 'Ngừng chiếu'; }
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= $txt ?></span>
                                </td>
                                <td>
                                    <!-- DELETE: POST để index route qua controller -->
                                    <form method="POST"
                                          action="index.php?page=movies&action=delete&id=<?= (int)$m['movie_id'] ?>"
                                          style="display:inline;">
                                        <button type="submit" class="btn-action danger"
                                                onclick="return confirm('Xóa phim này?')">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- MODAL ADD MOVIE -->
    <div id="addMovieModal" class="modal">
        <div class="modal-content" style="max-width:800px;">
            <div class="modal-header">
                <h2>Thêm phim mới</h2>
                <button type="button" class="btn-close" onclick="closeModal('addMovieModal')">&times;</button>
            </div>

            <!-- IMPORTANT: POST về index để gọi controller -->
            <form method="POST" action="index.php?page=movies&action=create">
                <div class="modal-body">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <div class="form-group">
                                <label>Tên phim</label>
                                <input type="text" name="title" required>
                            </div>

                            <div class="form-group">
                                <label>Thể loại</label>
                                <div class="genre-grid">
                                    <?php foreach ($genres as $g): ?>
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="genre_ids[]" value="<?= (int)$g['genre_id'] ?>">
                                            <?= htmlspecialchars($g['name']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
                                <label>Poster URL</label>
                                <input type="text" name="poster_url" placeholder="https://...">
                            </div>

                            <div class="form-group">
                                <label>Link Trailer</label>
                                <input type="text" name="trailer_url" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" rows="2"></textarea>
                    </div>

                    <div style="display:flex;gap:10px;">
                        <div class="form-group" style="flex:1">
                            <label>Thời lượng (phút)</label>
                            <input type="number" name="duration_min" min="1" required>
                        </div>

                        <div class="form-group" style="flex:1">
                            <label>Trạng thái</label>
                            <select name="status" required>
                                <option value="0">Sắp chiếu</option>
                                <option value="1" selected>Đang chiếu</option>
                                <option value="-1">Ngừng chiếu</option>
                            </select>
                        </div>

                        <div class="form-group" style="flex:1">
                            <label>Ngày chiếu</label>
                            <input type="date" name="release_date">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addMovieModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>

</section>