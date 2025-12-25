     <div id="view-dashboard">
            <div class="row g-3 mb-3">
              <div class="col-12 col-md-3">
                <div class="card stat-card shadow-sm p-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="text-muted small">Người dùng</div>
                      <div class="h4" id="statUsers">0</div>
                    </div>
                    <div class="fs-3 text-primary"><i class="bi bi-people"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-3">
                <div class="card stat-card shadow-sm p-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="text-muted small">Sản phẩm</div>
                      <div class="h4" id="statProducts">0</div>
                    </div>
                    <div class="fs-3 text-success"><i class="bi bi-box-seam"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-3">
                <div class="card stat-card shadow-sm p-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="text-muted small">Doanh thu (tháng)</div>
                      <div class="h4" id="statRevenue">0₫</div>
                    </div>
                    <div class="fs-3 text-warning"><i class="bi bi-currency-dollar"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-3">
                <div class="card stat-card shadow-sm p-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="text-muted small">Bài viết</div>
                      <div class="h4" id="statPosts">0</div>
                    </div>
                    <div class="fs-3 text-info"><i class="bi bi-card-text"></i></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-12 col-lg-8">
                <div class="card p-3 shadow-sm">
                  <h6>Biểu đồ lượt truy cập (demo)</h6>
                  <canvas id="visitorsChart" height="120"></canvas>
                </div>
              </div>
              <div class="col-12 col-lg-4">
                <div class="card p-3 shadow-sm">
                  <h6>Công việc gần đây</h6>
                  <ul id="recentTasks" class="list-group list-group-flush mt-2">
                    <li class="list-group-item">Khởi tạo giao diện Admin</li>
                    <li class="list-group-item">Thêm mẫu dữ liệu demo</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>