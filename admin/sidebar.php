 <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="px-3">
        <div class="brand text-white mb-3">
          <i class="bi bi-speedometer2 fs-4"></i>
          <div>
            <div>MyAdmin</div>
            <div class="user-mini">Panel quản trị</div>
          </div>
        </div>

        <hr style="border-color:rgba(255,255,255,0.06)" />

        <ul class="nav nav-pills flex-column mb-3" id="menuList">
          <li class="nav-item"><a class="nav-link active" href="#" data-view="dashboard"><i class="bi bi-house-door me-2"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#users" data-view="users"><i class="bi bi-people me-2"></i> Quản lý người dùng</a></li>
        <li class="nav-item">
          <a class="nav-link d-flex justify-content-between align-items-center"   data-bs-toggle="collapse" href="#serviceMenu" role="button">
            <span><i class="bi bi-box-seam me-2"></i> Quản lý dịch vụ</span>
            <i class="bi bi-caret-down-fill"></i>
          </a>
        <div class="collapse ps-4" id="serviceMenu">
          <a href="#flights" class="nav-link small" data-view="flights">✈ Chuyến bay</a>
          <a href="#hotels" class="nav-link small" data-view="hotels">🏨 Khách sạn</a>
          <a href="#tours" class="nav-link small" data-view="tours">🗺 Tour du lịch</a>
        </div>
        </li>
          <li class="nav-item"><a class="nav-link" href="#travelGuide" data-view="travelGuide"><i class="bi bi-card-text me-2"></i>Cẩm nang du lịch</a></li>
        </ul>

        <div class="mt-4">
        </div>
      </div>
    </aside>