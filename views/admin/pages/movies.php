<<<<<<< Updated upstream
=======
<?php
$search   = $_GET['q'] ?? '';
$genreId  = $_GET['genre_id'] ?? null;
$statusTx = $_GET['status_text'] ?? '';

$movies = $movieService->listMoviesAdmin($search, $genreId, $statusTx);
$genres = $movieService->getAllGenres();

$successMessage = $_GET['success'] ?? '';
$errorMessage   = $_GET['error'] ?? '';
$openModal      = $_GET['open_modal'] ?? '';
$editId         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editMovie      = null;

if ($openModal === 'edit' && $editId > 0) {
    try {
        $editMovie = $movieService->getMovieDetail($editId);
    } catch (Throwable $e) {
        $errorMessage = $e->getMessage();
    }
}

$editGenreIds = $editMovie['genre_ids'] ?? [];
?>

>>>>>>> Stashed changes
<section class="movies">
    <header class="admin-header">
        <h1>Quản lý phim</h1>
        <button class="btn-add " onclick="openModal('addMovieModal')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="3" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Thêm phim mới</span>
        </button>
    </header>

    <?php if ($successMessage): ?>
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#123524;color:#b8ffd2;border:1px solid #1f7a4d;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#3a1616;color:#ffd0d0;border:1px solid #a33;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-content">

        <div class="dashboard-card">

<<<<<<< Updated upstream
            <form method="GET" action="movies.php" class="filter-bar">
                <input
                    type="text"
                    name="search"
                    placeholder="Tìm tên phim..."
=======
            <form method="GET" action="index.php" class="filter-bar">
                <input type="hidden" name="page" value="movies">

                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    placeholder="Tìm tên phim / đạo diễn / diễn viên..."
>>>>>>> Stashed changes
                    style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">

                <select
                    name="genre"
                    style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
                    <option value="">Tất cả thể loại</option>
                    <option value="1">Hành động</option>
                    <option value="2">Hài</option>
                    <option value="3">Tình cảm</option>
                </select>

                <select
                    name="status"
                    style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
<<<<<<< Updated upstream
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đang chiếu">Đang chiếu</option>
                    <option value="Sắp chiếu">Sắp chiếu</option>
                    <option value="Ngừng chiếu">Ngừng chiếu</option>
=======
                    <option value="" <?= $st === '' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                    <option value="Đang chiếu" <?= $st === 'Đang chiếu' ? 'selected' : '' ?>>Đang chiếu</option>
                    <option value="Sắp chiếu" <?= $st === 'Sắp chiếu' ? 'selected' : '' ?>>Sắp chiếu</option>
                    <option value="Ngừng chiếu" <?= $st === 'Ngừng chiếu' ? 'selected' : '' ?>>Ngừng chiếu</option>
>>>>>>> Stashed changes
                </select>

                <button type="submit" class="btn-primary">Lọc</button>
                <a href="movies.php" class="btn-action">Reset</a>
            </form>

<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Poster</th>
                            <th>Tên phim</th>
                            <th>Đạo diễn</th>
                            <th>Diễn viên</th>
                            <th>Thể loại</th>
                            <th>Thời lượng</th>
                            <th>Ngày chiếu</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
<<<<<<< Updated upstream
                            <td>#1</td>
                            <td>
                                <img src="https://via.placeholder.com/40x60"
                                    style="width:40px;height:60px;object-fit:cover;border-radius:4px;">
                            </td>
                            <td><strong>Tên phim mẫu</strong></td>
                            <td>Hành động, Phiêu lưu</td>
                            <td>120p</td>
                            <td>01/01/2025</td>
                            <td><span class="badge badge-success">Đang chiếu</span></td>
                            <td>
                                <button class="btn-action">Sửa</button>
                                <button class="btn-action danger">Xóa</button>
                            </td>
                        </tr>
=======
                            <td colspan="10" style="text-align:center; padding:16px;">
                                Chưa có dữ liệu phim
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movies as $m): ?>
                            <tr>
                                <td>#<?= (int)$m['movie_id'] ?></td>
                                <td>
                                    <img
                                        src="<?= htmlspecialchars(($m['poster_url'] ?? '') ?: 'https://via.placeholder.com/40x60') ?>"
                                        style="width:40px;height:60px;object-fit:cover;border-radius:4px;">
                                </td>
                                <td><strong><?= htmlspecialchars($m['title']) ?></strong></td>
                                <td><?= htmlspecialchars($m['director'] ?? '') ?></td>
                                <td style="max-width:220px;"><?= htmlspecialchars($m['cast'] ?? '') ?></td>
                                <td><?= htmlspecialchars($m['genre_names'] ?? '') ?></td>
                                <td><?= (int)$m['duration_min'] ?>p</td>
                                <td><?= !empty($m['release_date']) ? htmlspecialchars($m['release_date']) : '' ?></td>
                                <td>
                                    <?php
                                        $badge = 'badge-success';
                                        $txt = 'Đang chiếu';
                                        if ((int)$m['status'] === 0) {
                                            $badge = 'badge-warning';
                                            $txt = 'Sắp chiếu';
                                        }
                                        if ((int)$m['status'] === -1) {
                                            $badge = 'badge-danger';
                                            $txt = 'Ngừng chiếu';
                                        }
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= $txt ?></span>
                                </td>
                                <td style="white-space:nowrap;">
                                    <a href="index.php?page=movies&open_modal=edit&id=<?= (int)$m['movie_id'] ?>" class="btn-action">
                                        Sửa
                                    </a>

                                    <form method="POST"
                                          action="index.php?page=movies&action=delete&id=<?= (int)$m['movie_id'] ?>"
                                          style="display:inline;">
                                        <button type="submit"
                                                class="btn-action danger"
                                                onclick="return confirm('Xóa phim này?')">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
>>>>>>> Stashed changes
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <a class="page-link active">1</a>
                <a class="page-link">2</a>
                <a class="page-link">3</a>
            </div>

        </div>
    </div>

    <!-- ADD MODAL -->
    <div id="addMovieModal" class="modal">
        <div class="modal-content" style="max-width:900px;">
            <div class="modal-header">
                <h2>Thêm phim mới</h2>
                <button class="btn-close">&times;</button>
            </div>

<<<<<<< Updated upstream
            <form>
=======
            <form
                method="POST"
                action="index.php?page=movies&action=create"
                enctype="multipart/form-data"
                onsubmit="return validateMovieForm(this)">
>>>>>>> Stashed changes
                <div class="modal-body">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <div class="form-group">
<<<<<<< Updated upstream
                                <label>Tên phim</label>
                                <input type="text">
                            </div>

                            <div class="form-group">
                                <label>Đạo diễn</label>
                                <input type="text">
=======
                                <label>Tên phim <span style="color:#ff6b6b">*</span></label>
                                <input type="text" name="title" required maxlength="255" placeholder="Nhập tên phim">
>>>>>>> Stashed changes
                            </div>

                            <div class="form-group">
                                <label>Tên đạo diễn <span style="color:#ff6b6b">*</span></label>
                                <input type="text" name="director" required maxlength="150" placeholder="Ví dụ: Christopher Nolan">
                            </div>

                            <div class="form-group">
                                <label>Diễn viên <span style="color:#ff6b6b">*</span></label>
                                <textarea
                                    name="cast"
                                    rows="3"
                                    required
                                    maxlength="1000"
                                    placeholder="Nhập danh sách diễn viên, cách nhau bằng dấu phẩy"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Thể loại <span style="color:#ff6b6b">*</span></label>
                                <div class="genre-grid">
<<<<<<< Updated upstream
                                    <label class="checkbox-item">
                                        <input type="checkbox"> Hành động
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox"> Hài
                                    </label>
=======
                                    <?php foreach ($genres as $g): ?>
                                        <label class="checkbox-item">
                                            <input
                                                type="checkbox"
                                                class="genre-checkbox"
                                                name="genre_ids[]"
                                                value="<?= (int)$g['genre_id'] ?>">
                                            <?= htmlspecialchars($g['name']) ?>
                                        </label>
                                    <?php endforeach; ?>
>>>>>>> Stashed changes
                                </div>
                                <small style="display:block;margin-top:6px;color:#aaa;">
                                    Phải chọn ít nhất 1 thể loại.
                                </small>
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
<<<<<<< Updated upstream
                                <label>Diễn viên</label>
                                <select multiple class="actor-select">
                                    <option>Diễn viên A</option>
                                    <option>Diễn viên B</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Poster</label>
                                <div style="display:flex;gap:10px;align-items:center;">
                                    <img src="https://via.placeholder.com/50x75">
                                    <input type="file">
                                </div>
=======
                                <label>Poster URL</label>
                                <input
                                    type="text"
                                    name="poster_url"
                                    placeholder="https://..."
                                    oninput="updatePosterFromUrl(this, 'addPosterPreview')">
                            </div>

                            <div class="form-group">
                                <label>Poster (Tải ảnh lên)</label>
                                <div style="display:flex; align-items:center; gap:16px;">
                                    <img
                                        id="addPosterPreview"
                                        src="https://via.placeholder.com/60x90?text=No+Image"
                                        alt="Poster preview"
                                        style="width:60px; height:90px; object-fit:cover; border-radius:4px; border:1px solid #444; background:#111;">

                                    <input
                                        type="file"
                                        name="poster_file"
                                        accept="image/*"
                                        onchange="previewPoster(this, 'addPosterPreview')"
                                        style="flex:1;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Link Trailer</label>
                                <input type="text" name="trailer_url" maxlength="255" placeholder="https://...">
                            </div>

                            <div class="form-group">
                                <label>Ngày chiếu <span style="color:#ff6b6b">*</span></label>
                                <input
                                    type="date"
                                    name="release_date"
                                    required
                                    min="1900-01-01"
                                    max="2100-12-31"
                                    onclick="this.showPicker && this.showPicker()">
>>>>>>> Stashed changes
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Link Trailer</label>
                        <input type="text">
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
<<<<<<< Updated upstream
                        <textarea rows="2"></textarea>
=======
                        <textarea name="description" rows="3" maxlength="2000"></textarea>
>>>>>>> Stashed changes
                    </div>

                    <div style="display:flex;gap:10px;">
                        <div class="form-group" style="flex:1">
<<<<<<< Updated upstream
                            <label>Thời lượng</label>
                            <input type="number">
=======
                            <label>Thời lượng (phút) <span style="color:#ff6b6b">*</span></label>
                            <input type="number" name="duration_min" min="1" max="1000" required placeholder="Ví dụ: 120">
>>>>>>> Stashed changes
                        </div>
                        <div class="form-group" style="flex:1">
<<<<<<< Updated upstream
                            <label>Trạng thái</label>
                            <select>
                                <option>Sắp chiếu</option>
                                <option>Đang chiếu</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1">
                            <label>Ngày chiếu</label>
                            <input type="date">
                        </div>
=======
                            <label>Trạng thái <span style="color:#ff6b6b">*</span></label>
                            <select name="status" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="0">Sắp chiếu</option>
                                <option value="1">Đang chiếu</option>
                                <option value="-1">Ngừng chiếu</option>
                            </select>
                        </div>
>>>>>>> Stashed changes
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action">Hủy</button>
                    <button type="button" class="btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <?php if ($editMovie): ?>
    <div id="editMovieModal" class="modal" style="display:<?= $openModal === 'edit' ? 'flex' : 'none' ?>;">
        <div class="modal-content" style="max-width:900px;">
            <div class="modal-header">
                <h2>Chỉnh sửa phim</h2>
                <button type="button" class="btn-close" onclick="window.location.href='index.php?page=movies'">&times;</button>
            </div>

            <form
                method="POST"
                action="index.php?page=movies&action=update&id=<?= (int)$editMovie['movie_id'] ?>"
                enctype="multipart/form-data"
                onsubmit="return validateMovieForm(this)">
                <div class="modal-body">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <div class="form-group">
                                <label>Tên phim <span style="color:#ff6b6b">*</span></label>
                                <input type="text" name="title" required maxlength="255" value="<?= htmlspecialchars($editMovie['title'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label>Tên đạo diễn <span style="color:#ff6b6b">*</span></label>
                                <input type="text" name="director" required maxlength="150" value="<?= htmlspecialchars($editMovie['director'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label>Diễn viên <span style="color:#ff6b6b">*</span></label>
                                <textarea name="cast" rows="3" required maxlength="1000"><?= htmlspecialchars($editMovie['cast'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Thể loại <span style="color:#ff6b6b">*</span></label>
                                <div class="genre-grid">
                                    <?php foreach ($genres as $g): ?>
                                        <label class="checkbox-item">
                                            <input
                                                type="checkbox"
                                                class="genre-checkbox"
                                                name="genre_ids[]"
                                                value="<?= (int)$g['genre_id'] ?>"
                                                <?= in_array((int)$g['genre_id'], $editGenreIds, true) ? 'checked' : '' ?>>
                                            <?= htmlspecialchars($g['name']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <small style="display:block;margin-top:6px;color:#aaa;">
                                    Phải chọn ít nhất 1 thể loại.
                                </small>
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
                                <label>Poster URL</label>
                                <input
                                    type="text"
                                    name="poster_url"
                                    placeholder="https://..."
                                    value="<?= htmlspecialchars($editMovie['poster_url'] ?? '') ?>"
                                    oninput="updatePosterFromUrl(this, 'editPosterPreview')">
                            </div>

                            <div class="form-group">
                                <label>Poster (Tải ảnh lên)</label>
                                <div style="display:flex; align-items:center; gap:16px;">
                                    <img
                                        id="editPosterPreview"
                                        src="<?= htmlspecialchars(($editMovie['poster_url'] ?? '') ?: 'https://via.placeholder.com/60x90?text=No+Image') ?>"
                                        alt="Poster preview"
                                        style="width:60px; height:90px; object-fit:cover; border-radius:4px; border:1px solid #444; background:#111;">

                                    <input
                                        type="file"
                                        name="poster_file"
                                        accept="image/*"
                                        onchange="previewPoster(this, 'editPosterPreview')"
                                        style="flex:1;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Link Trailer</label>
                                <input type="text" name="trailer_url" maxlength="255" placeholder="https://..." value="<?= htmlspecialchars($editMovie['trailer_url'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label>Ngày chiếu <span style="color:#ff6b6b">*</span></label>
                                <input
                                    type="date"
                                    name="release_date"
                                    required
                                    min="1900-01-01"
                                    max="2100-12-31"
                                    value="<?= htmlspecialchars($editMovie['release_date'] ?? '') ?>"
                                    onclick="this.showPicker && this.showPicker()">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" rows="3" maxlength="2000"><?= htmlspecialchars($editMovie['description'] ?? '') ?></textarea>
                    </div>

                    <div style="display:flex;gap:10px;">
                        <div class="form-group" style="flex:1">
                            <label>Thời lượng (phút) <span style="color:#ff6b6b">*</span></label>
                            <input type="number" name="duration_min" min="1" max="1000" required value="<?= (int)($editMovie['duration_min'] ?? 0) ?>">
                        </div>

                        <div class="form-group" style="flex:1">
                            <label>Trạng thái <span style="color:#ff6b6b">*</span></label>
                            <select name="status" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="0" <?= (int)($editMovie['status'] ?? 0) === 0 ? 'selected' : '' ?>>Sắp chiếu</option>
                                <option value="1" <?= (int)($editMovie['status'] ?? 0) === 1 ? 'selected' : '' ?>>Đang chiếu</option>
                                <option value="-1" <?= (int)($editMovie['status'] ?? 0) === -1 ? 'selected' : '' ?>>Ngừng chiếu</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <a href="index.php?page=movies" class="btn-action">Hủy</a>
                    <button type="submit" class="btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</section>

<script>
function previewPoster(input, previewId) {
    const preview = document.getElementById(previewId);

    if (!input.files || !input.files[0]) {
        return;
    }

    const file = input.files[0];

    if (!file.type.startsWith('image/')) {
        alert('Vui lòng chọn file ảnh hợp lệ.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function updatePosterFromUrl(input, previewId) {
    const preview = document.getElementById(previewId);
    const url = input.value.trim();

    if (url !== '') {
        preview.src = url;
    }
}

function validateMovieForm(form) {
    const title = form.querySelector('[name="title"]')?.value.trim() || '';
    const director = form.querySelector('[name="director"]')?.value.trim() || '';
    const cast = form.querySelector('[name="cast"]')?.value.trim() || '';
    const releaseDate = form.querySelector('[name="release_date"]')?.value.trim() || '';
    const duration = form.querySelector('[name="duration_min"]')?.value.trim() || '';
    const status = form.querySelector('[name="status"]')?.value.trim() || '';
    const checkedGenres = form.querySelectorAll('.genre-checkbox:checked');

    if (title === '') {
        alert('Vui lòng nhập tên phim.');
        return false;
    }

    if (director === '') {
        alert('Vui lòng nhập tên đạo diễn.');
        return false;
    }

    if (cast === '') {
        alert('Vui lòng nhập diễn viên.');
        return false;
    }

    if (checkedGenres.length === 0) {
        alert('Vui lòng chọn ít nhất 1 thể loại.');
        return false;
    }

    if (duration === '' || isNaN(duration) || Number(duration) <= 0) {
        alert('Thời lượng phim phải lớn hơn 0.');
        return false;
    }

    if (status === '') {
        alert('Vui lòng chọn trạng thái.');
        return false;
    }

    if (releaseDate === '') {
        alert('Vui lòng chọn ngày chiếu.');
        return false;
    }

    return true;
}

<?php if ($openModal === 'add'): ?>
document.addEventListener('DOMContentLoaded', function () {
    openModal('addMovieModal');
});
<?php endif; ?>
</script>