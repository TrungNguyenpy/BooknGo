<?php
include __DIR__ . '/flight-detail-data.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Chi tiết chuyến bay - <?= htmlspecialchars($flight['flight_name']) ?></title>
<link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
<link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../css/pageDetail.css?v=<?php echo time(); ?>">

</head>
<body>
<div class="header">
    <?php include '../includes/header.php'; ?>
</div>

<div class="container py-4">
    <!-- Top area: dùng dữ liệu từ $flight -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="top-search" style="margin-top: 70px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:18px;font-weight:700;">
                            <?= htmlspecialchars($flight['departure']) ?> → <?= htmlspecialchars($flight['arrival']) ?>
                        </div>
                        <div class="small-muted mt-1" style="color:#fff;">
                            <?= htmlspecialchars($flight['flight_name']) ?> | <?= htmlspecialchars($flight['airline']) ?> |
                            <span><?= htmlspecialchars(date('d/m/Y', strtotime($selected_date))) ?></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="pill">Giá tham khảo: <?= isset($flight['price_new']) ? number_format((float)$flight['price_new'],0,',','.') . ' VND' : '—' ?></div>
                    </div>
                </div>

                <!-- date tabs: render 7 ngày -->
                <div class="mt-3 date-tabs">
                    <ul class="nav d-flex">
                        <?php foreach ($days as $day): ?>
                            <?php
                                $isActive = $day['active'] ? 'active' : '';
                                $priceText = $day['min_price'] !== null ? number_format($day['min_price'],0,',','.') . ' VND' : '—';
                            ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $isActive ?>" href="?id=<?= $flight_id ?>&date=<?= $day['date'] ?>">
                                    <?= htmlspecialchars($day['label']) ?><br>
                                    <small class="d-block price-text"><?= $priceText ?></small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Main layout -->
    <div class="row">
        <!-- Left sidebar (giữ nguyên layout của bạn) -->
        <div class="col-lg-3 mb-3">
            <div class="left-sidebar">

            <form method="GET">
                <input type="hidden" name="id" value="<?= $flight_id ?>">
                <input type="hidden" name="date" value="<?= $selected_date ?>">

                <div class="filters-section">
                    <h6>Bộ lọc</h6>
                    <div class="left-scroll">
                        <!-- giữ nguyên nội dung filter -->
                        <div class="mt-3">
                        <h6>Số điểm dừng</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="stops[]" value="0" id="stop0" <?= (in_array('0', $_GET['stops'] ?? ['0']) ? 'checked' : '') ?>>
                            <label class="form-check-label small-muted" for="stop0">Bay thẳng</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="stops[]" value="1" id="stop1" <?= (in_array('1', $_GET['stops'] ?? []) ? 'checked' : '') ?>>
                            <label class="form-check-label small-muted" for="stop1">1 điểm dừng</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="stops[]" value="2" id="stop2" <?= (in_array('2', $_GET['stops'] ?? []) ? 'checked' : '') ?>>
                            <label class="form-check-label small-muted" for="stop2">Nhiều điểm dừng</label>
                        </div>

                        </div>

                        <div class="mt-3">
                        <h6>Hãng hàng không</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="airlines[]" value="Bamboo Airways"
                                    id="air_bamboo"
                                    <?= (in_array('Bamboo Airways', $_GET['airlines'] ?? ['Bamboo Airways']) ? 'checked' : '') ?>>
                                <label class="form-check-label small-muted" for="air_bamboo">Bamboo Airways</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="airlines[]" value="VietJet Air"
                                    id="air_vietjet"
                                    <?= (in_array('VietJet Air', $_GET['airlines'] ?? ['VietJet Air']) ? 'checked' : '') ?>>
                                <label class="form-check-label small-muted" for="air_vietjet">VietJet Air</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="airlines[]" value="Vietnam Airlines"
                                    id="air_vna"
                                    <?= (in_array('Vietnam Airlines', $_GET['airlines'] ?? ['Vietnam Airlines']) ? 'checked' : '') ?>>
                                <label class="form-check-label small-muted" for="air_vna">Vietnam Airlines</label>
                            </div>

                        </div>

                        <div class="mt-3">
                            <h6>Thời gian bay</h6>
                            <div class="small-muted">Giờ cất cánh</div>

                            <?php
                            $timeRanges = [
                                '0-6'   => '00:00 - 06:00',
                                '6-12'  => '06:00 - 12:00',
                                '12-18' => '12:00 - 18:00',
                                '18-24' => '18:00 - 24:00',
                            ];
                            $selectedTimes = $_GET['timeRanges'] ?? [];
                            ?>

                        <div class="d-grid gap-2 mt-2">
                                <?php foreach ($timeRanges as $key => $label): 
                                    $checked = in_array($key, $selectedTimes) ? 'checked' : '';
                                ?>
                                    <input type="checkbox" class="btn-check" name="timeRanges[]" value="<?= $key ?>" id="time_<?= $key ?>" autocomplete="off" <?= $checked ?>>
                                    <label class="btn btn-outline-secondary btn-sm" for="time_<?= $key ?>"><?= $label ?></label>
                                <?php endforeach; ?>
                            </div>               
                        </div>


                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Áp dụng</button>
                        </div>

                    </div>
                </div>
            </form>
           

            </div>
        </div>

        <!-- Right content: hiển thị các flight_details (chỉ cho $selected_date) -->
        <div class="col-lg-9">
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong><?= htmlspecialchars($flight['flight_name']) ?> — <?= htmlspecialchars($flight['airline']) ?></strong>
                        <div class="small-muted"><?= htmlspecialchars($flight['departure']) ?> → <?= htmlspecialchars($flight['arrival']) ?></div>
                    </div>
                    <div class="small-muted">Số chuyến trong ngày <?= htmlspecialchars(date('d/m/Y', strtotime($selected_date))) ?>: <span class="fw-bold"><?= count($details) ?></span></div>
                </div>
            </div>
            <?php
                // ===== PHÂN TRANG =====
                $perPage = 5; // mỗi trang 5 chuyến bay
                $totalDetails = count($details);
                $totalPages = ceil($totalDetails / $perPage);

                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $start = ($page - 1) * $perPage;

                // Chỉ lấy phần cần hiển thị trong trang
                $details_page = array_slice($details, $start, $perPage);

                // Tạo query giữ lại filter khi chuyển trang
                $queryString = $_GET;
                unset($queryString['page']); // bỏ page để tránh trùng
                $baseUrl = '?' . http_build_query($queryString) . '&page=';
            ?>
                        
            <?php if (count($details) === 0): ?>
                <div class="alert alert-warning">Không có giờ bay cho ngày này.</div>
            <?php else: ?>
              <?php foreach ($details_page as $i => $d): ?>

                    <div class="flight-card <?= $i === 0 ? 'best' : '' ?>">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-start">
                                <div class="logo me-3"><?= htmlspecialchars(substr($flight['airline'],0,2)) ?></div>
                                <div>
                                    <div style="font-weight:700;"><?= htmlspecialchars($flight['flight_name']) ?> - <?= htmlspecialchars($flight['airline']) ?></div>
                                     <!---->    <div class="small-muted" style="font-size:13px;">Chi tiết ID: <?= htmlspecialchars($d['id']) ?></div>
                                </div>
                            </div>

                            <div class="text-end">
                                <div class="flight-price"><?= isset($flight['price_new']) ? number_format((float)$flight['price_new'],0,',','.') . ' VND' : '—' ?></div>
                                <div class="small-muted">/khách</div>
                            </div>
                        </div>

                        <div class="row mt-3 align-items-center">
                            <div class="col-md-6">
                                <div class="flight-info d-flex align-items-center">
                                    <div class="me-3 text-center">
                                        <div class="flight-time"><?= htmlspecialchars(date('H:i', strtotime($d['departure_time']))) ?></div>
                                        <div class="small-muted"><?= htmlspecialchars($flight['departure']) ?></div>
                                    </div>

                                    <div style="margin:0 16px;text-align:center;">
                                        <?php
                                            $dur = '';
                                            if (!empty($d['departure_time']) && !empty($d['arrival_time'])) {
                                                $dt1 = strtotime($d['departure_time']);
                                                $dt2 = strtotime($d['arrival_time']);
                                                if ($dt1 && $dt2) {
                                                    $secs = $dt2 - $dt1;
                                                    if ($secs < 0) $secs += 24*3600;
                                                    $h = floor($secs/3600);
                                                    $m = floor(($secs%3600)/60);
                                                    $dur = ($h>0? $h.'h ':'') . ($m>0? $m.'m':'');
                                                }
                                            }
                                        ?>
                                        <div class="flight-duration"><?= $dur ?: '—' ?></div>
                                        <div class="small-muted"><?= htmlspecialchars(!empty($d['transit_info']) ? $d['transit_info'] : 'Bay thẳng') ?></div>
                                    </div>

                                    <div class="text-end">
                                        <div class="flight-time"><?= htmlspecialchars(date('H:i', strtotime($d['arrival_time']))) ?></div>
                                        <div class="small-muted"><?= htmlspecialchars($flight['arrival']) ?></div>
                                    </div>
                                </div>

                                <div class="mt-2 small-muted">
                                    Máy bay: <?= htmlspecialchars($d['aircraft'] ?: '—') ?> |
                                    Hành lý: <?= htmlspecialchars($d['baggage_info'] ?: '—') ?>
                                </div>
                            </div>

                            <div class="col-md-6 text-end">
                                <div class="mb-2">
                                    <span class="tag-promo">Chi tiết & lợi ích</span>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <!-- Chi tiết -->
                                    <button 
                                        class="btn btn-outline-secondary btn-sm"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#flightDetailModal"
                                        onclick="showFlightDetail(<?= htmlspecialchars(json_encode($d)) ?>)">
                                        Chi tiết
                                    </button>

                                    <!-- Lợi ích -->
                                    <button 
                                        class="btn btn-outline-secondary btn-sm"
                                        type="button"
                                        onclick="alert('Các lợi ích đi kèm hiện chưa được cập nhật.')">
                                        Các lợi ích đi kèm
                                    </button>

                                    <!-- Hoàn vé -->
                                    <button 
                                        class="btn btn-outline-secondary btn-sm"
                                        type="button"
                                        onclick="showRefundNotice()">
                                        Hoàn vé
                                    </button>

                                    <!-- Chọn -->
                                    <form method="POST" action="../services/booking-form.php">
                                        <input type="hidden" name="type" value="flight">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-choose">Chọn</button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">

                            <!-- Nút Previous -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl . ($page - 1) ?>">«</a>
                            </li>

                            <!-- Các số trang -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $baseUrl . $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Nút Next -->
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl . ($page + 1) ?>">»</a>
                            </li>

                        </ul>
                    </nav>
                    <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>

</div>
<div class="modal fade" id="flightDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết chuyến bay</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Giờ khởi hành:</strong> <span id="fd_departure"></span></li>
                    <li class="list-group-item"><strong>Giờ đến:</strong> <span id="fd_arrival"></span></li>
                    <li class="list-group-item"><strong>Máy bay:</strong> <span id="fd_aircraft"></span></li>
                    <li class="list-group-item"><strong>Hành lý:</strong> <span id="fd_baggage"></span></li>
                    <li class="list-group-item"><strong>Trung chuyển:</strong> <span id="fd_transit"></span></li>
                    <li class="list-group-item"><strong>Mô tả:</strong> <span id="fd_description"></span></li>
                    <li class="list-group-item text-danger fw-bold">
                        Giá: <span id="fd_price"><?= isset($flight['price_new']) ? number_format((float)$flight['price_new'],0,',','.') . ' VND' : '—' ?></span> 
                    </li>
                </ul>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
function showFlightDetail(data) {
    document.getElementById('fd_departure').innerText = data.departure_time || '—';
    document.getElementById('fd_arrival').innerText = data.arrival_time || '—';
    document.getElementById('fd_aircraft').innerText = data.aircraft || '—';
    document.getElementById('fd_baggage').innerText = data.baggage_info || '—';
    document.getElementById('fd_transit').innerText = data.transit_info || '—';
    document.getElementById('fd_description').innerText = data.description || 'Không có mô tả';
     document.getElementById('fd_price').innerText =
        price > 0 ? price.toLocaleString('vi-VN') : 'Liên hệ';
}

function showRefundNotice() {
    if (confirm("Chính sách hoàn vé vui lòng liên hệ bộ phận hỗ trợ.\n\nBạn có muốn mở hỗ trợ ngay không?")) {
        const supportModal = new bootstrap.Modal(document.getElementById('supportModal'));
        supportModal.show();
    }
}
</script>

</html>
