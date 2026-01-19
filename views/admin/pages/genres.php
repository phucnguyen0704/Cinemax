<section class="genres">

    <header class="admin-header">
        <h1>Quản lý Thể loại Phim</h1>
        <div class="header-actions">

            <button class="btn-add" onclick="openModal('addGenreModal')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm thể loại</span>
            </button>

            <div class="user-menu">
                <img src="../../assets/images/default-avatar.png" alt="Admin">
                <span>Admin Cinema</span>
            </div>
        </div>
    </header>

    <div class="dashboard-content">

        <!-- Alert mẫu -->
        <!-- <div class="alert alert-success">Thao tác thành công!</div> -->
        <!-- <div class="alert alert-error">Có lỗi xảy ra</div> -->

        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th>Tên thể loại</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>#1</td>
                            <td><strong>Hành động</strong></td>
                            <td style="text-align: right;">
                                <a href="#" class="btn-action">Sửa</a>

                                <form action="../../Handle/genres_process.php"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Xóa thể loại này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="genre_id" value="1">
                                    <button class="btn-action danger">Xóa</button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>#2</td>
                            <td><strong>Kinh dị</strong></td>
                            <td style="text-align: right;">
                                <a href="#" class="btn-action">Sửa</a>

                                <form action="../../Handle/genres_process.php"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Xóa thể loại này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="genre_id" value="2">
                                    <button class="btn-action danger">Xóa</button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>#3</td>
                            <td><strong>Hài</strong></td>
                            <td style="text-align: right;">
                                <a href="#" class="btn-action">Sửa</a>

                                <form action="../../Handle/genres_process.php"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Xóa thể loại này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="genre_id" value="3">
                                    <button class="btn-action danger">Xóa</button>
                                </form>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal thêm thể loại -->
    <div id="addGenreModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Thêm thể loại mới</h2>
                <button class="btn-close" onclick="closeModal('addGenreModal')">&times;</button>
            </div>

            <form action="../../Handle/genres_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên thể loại</label>
                        <input type="text"
                               name="name"
                               placeholder="VD: Hành động, Kinh dị..."
                               required
                               class="custom-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn-action"
                            onclick="closeModal('addGenreModal')">
                        Hủy
                    </button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

</section>
