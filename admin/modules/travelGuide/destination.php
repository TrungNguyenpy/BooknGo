<?php 
require_once __DIR__ . '/../../../config/config.php';

// ---- Phân trang ----
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ---- Lấy dữ liệu destinations + COUNT ----
$sql = "SELECT d.*,
        c.slogan,
        c.description,
        c.hero_image,
        d.country_flag,
        (SELECT COUNT(*) FROM hotels h WHERE h.city_id = d.city_id) AS hotel_count,
        (SELECT COUNT(*) FROM places p WHERE p.city_id = d.city_id) AS places_count,
        (SELECT COUNT(*) FROM foods f WHERE f.city_id = d.city_id) AS foods_count,
        (SELECT COUNT(*) FROM events e WHERE e.city_id = d.city_id) AS events_count
        FROM destinations d
        LEFT JOIN cities c ON c.city_id = d.city_id
        ORDER BY d.id ASC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// ---- Tổng số bản ghi ----
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM destinations");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<div id="view-travelGuide" class="view">

  <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Quản lý Destination</h2>
      <a href="modules/travelGuide/city.php" class="btn btn-primary" style="margin-left: auto;">+ Thêm City</a>
      <a href="modules/travelGuide/destination/create.php" class="btn btn-primary" style="margin-left: 10px;">+ Thêm Destination</a>
      
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>

          <th>City ID</th>
          <th>Tên</th>
          <th>Slogan</th>
          <th>Mô tả</th>
          <th>Ảnh</th>
          <th>Cờ</th>
          <th>Khách sạn</th>
          <th>Điểm tham quan</th>
          <th>Ẩm thực</th>
          <th>Sự kiện</th>
          <th width="150">Hành động</th>
        </tr>
      </thead>
      <tbody>
      <?php if($result && $result->num_rows>0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
         
            <td><?= htmlspecialchars($row['city_id']); ?></td>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['slogan'] ?? ''); ?></td>
            <td style="max-width:200px;"><?= htmlspecialchars($row['description'] ?? ''); ?></td>
            <td>
              <?php if(!empty($row['image_url'])): ?>
                <img src="<?= $base_url; ?><?= htmlspecialchars($row['image_url']); ?>" width="70">
              <?php else: ?>
                <span class="text-muted">No image</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if(!empty($row['country_flag'])): ?>
                <img src="<?= $base_url; ?><?= htmlspecialchars($row['country_flag']); ?>" width="40">
              <?php else: ?>
                <span class="text-muted">No flag</span>
              <?php endif; ?>
            </td>

            <!-- COUNT + BUTTON MODAL -->
            <td>
              <?= $row['hotel_count']; ?> 
              <?php if($row['hotel_count']>0): ?>
                <button class="btn btn-sm btn-info ms-1 show-detail" 
                        data-city="<?= htmlspecialchars($row['city_id']) ?>" 
                        data-type="hotels">Xem</button>
              <?php endif; ?>
            </td>
            <td>
              <?= $row['places_count']; ?> 
              <?php if($row['places_count']>0): ?>
                <button class="btn btn-sm btn-info ms-1 show-detail" 
                        data-city="<?= htmlspecialchars($row['city_id']) ?>" 
                        data-type="places">Xem</button>
              <?php endif; ?>
            </td>
            <td>
              <?= $row['foods_count']; ?> 
              <?php if($row['foods_count']>0): ?>
                <button class="btn btn-sm btn-info ms-1 show-detail" 
                        data-city="<?= htmlspecialchars($row['city_id']) ?>" 
                        data-type="foods">Xem</button>
              <?php endif; ?>
            </td>
            <td>
              <?= $row['events_count']; ?> 
              <?php if($row['events_count']>0): ?>
                <button class="btn btn-sm btn-info ms-1 show-detail" 
                        data-city="<?= htmlspecialchars($row['city_id']) ?>" 
                        data-type="events">Xem</button>
              <?php endif; ?>
            </td>

            <td>
              <a href="modules/travelGuide/destination/edit.php?city_id=<?= $row['city_id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
              <a href="modules/travelGuide/delete.php?city_id=<?= $row['city_id']; ?>" class="btn btn-sm btn-danger"
                onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="12" class="text-center">Chưa có dữ liệu</td>
        </tr>
      <?php endif; ?>
      </tbody>


    </table>
  </div>

  <!-- Phân trang -->
  <?php if($totalPages>1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <?php if($page>1): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page-1; ?>">&laquo;</a></li>
        <?php endif; ?>
        <?php for($i=1;$i<=$totalPages;$i++): ?>
          <li class="page-item <?= ($i==$page)?'active':''; ?>">
            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
          </li>
        <?php endfor; ?>
        <?php if($page<$totalPages): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page+1; ?>">&raquo;</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>

</div>

<!-- Modal Xem chi tiết -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Load dữ liệu AJAX ở đây -->
      </div>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.show-detail').forEach(btn=>{
  btn.addEventListener('click',function(){
    const cityId = this.dataset.city;
    const type = this.dataset.type;
    const modalBody = document.getElementById('modalContent');
    modalBody.innerHTML = "Đang tải...";

    fetch(`modules/travelGuide/get_details.php?city_id=${cityId}&type=${type}`)
      .then(res=>res.text())
      .then(html=>{
        modalBody.innerHTML = html;
        new bootstrap.Modal(document.getElementById('detailModal')).show();
      });
  });
});
</script>
