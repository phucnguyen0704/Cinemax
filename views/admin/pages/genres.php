<?php
// views/admin/pages/genres.php
$q = $_GET['q'] ?? '';
$genres = $genreService->listAdmin($q);
?>

<section class="genres">

    <header class="admin-header">
        <h1>Quản lý Thể loại Phim</h1>

        <div class="header-actions">
            <button class="btn-add" onclick="openModal('addGenreModal')">
                <span>Thêm thể loại</span>
            </button>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="dashboard-card">

            <!-- SEARCH -->
            <form method="GET" action="index.php" class="filter-bar">
                <input type="hidden" name="page" value="genres">
                <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Tìm thể loại...">
                <button type="submit" class="btn-primary">Tìm</button>
                <a href="index.php?page=genres" class="btn-action">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">ID</th>
                            <th>Tên thể loại</th>
                            <th style="text-align:right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($genres)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding:16px;">Chưa có thể loại</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($genres as $g): ?>
                            <tr>
                                <td>#<?= (int)$g['genre_id'] ?></td>
                                <td><strong><?= htmlspecialchars($g['name']) ?></strong></td>
                                <td style="text-align:right;">
                                    <form method="POST"
                                          action="index.php?page=genres&action=delete&id=<?= (int)$g['genre_id'] ?>"
                                          style="display:inline;"
                                          onsubmit="return confirm('Xóa thể loại này?');">
                                        <button class="btn-action danger" type="submit">Xóa</button>
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

    <!-- Modal add -->
    <div id="addGenreModal" class="modal">
        <div class="modal-content" style="max-width: 420px;">
            <div class="modal-header">
                <h2>Thêm thể loại mới</h2>
                <button class="btn-close" type="button" onclick="closeModal('addGenreModal')">&times;</button>
            </div>

            <form method="POST" action="index.php?page=genres&action=create">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên thể loại</label>
                        <input type="text" name="name" required placeholder="VD: Hành động, Kinh dị...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addGenreModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

</section>