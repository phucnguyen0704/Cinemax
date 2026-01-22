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

    <div class="dashboard-content">

        <div class="dashboard-card">

            <form method="GET" action="movies.php" class="filter-bar">
                <input
                    type="text"
                    name="search"
                    placeholder="Tìm tên phim..."
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
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đang chiếu">Đang chiếu</option>
                    <option value="Sắp chiếu">Sắp chiếu</option>
                    <option value="Ngừng chiếu">Ngừng chiếu</option>
                </select>

                <button type="submit" class="btn-primary">Lọc</button>
                <a href="movies.php" class="btn-action">Reset</a>
            </form>


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
                        <tr>
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

    <!-- MODAL ADD MOVIE -->
    <div id="addMovieModal" class="modal">
        <div class="modal-content" style="max-width:800px;">
            <div class="modal-header">
                <h2>Thêm phim mới</h2>
                <button class="btn-close">&times;</button>
            </div>

            <form>
                <div class="modal-body">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <div class="form-group">
                                <label>Tên phim</label>
                                <input type="text">
                            </div>

                            <div class="form-group">
                                <label>Đạo diễn</label>
                                <input type="text">
                            </div>

                            <div class="form-group">
                                <label>Thể loại</label>
                                <div class="genre-grid">
                                    <label class="checkbox-item">
                                        <input type="checkbox"> Hành động
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox"> Hài
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
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
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Link Trailer</label>
                        <input type="text">
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea rows="2"></textarea>
                    </div>

                    <div style="display:flex;gap:10px;">
                        <div class="form-group" style="flex:1">
                            <label>Thời lượng</label>
                            <input type="number">
                        </div>
                        <div class="form-group" style="flex:1">
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
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-action">Hủy</button>
                    <button type="button" class="btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>

</section>