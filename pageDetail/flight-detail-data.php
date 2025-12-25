<?php
// flight-detail-data.php
include __DIR__ . '/../config/config.php';

if (!isset($conn) || !$conn) {
    die("Connection failed: please check config.php and \$conn.");
}

// Lấy flight_id từ URL
$flight_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($flight_id <= 0) {
    die("❌ Thiếu hoặc không hợp lệ ID chuyến bay!");
}

// Lấy ngày được chọn
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    $selected_date = date('Y-m-d');
}

// Lấy thông tin chung từ flights
$sqlFlight = "SELECT id, flight_name, departure, arrival, airline, image, price_old, price_new
              FROM flights
              WHERE id = ? LIMIT 1";

$stmt = $conn->prepare($sqlFlight);
if (!$stmt) die("Prepare failed (flight): " . $conn->error);
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$resFlight = $stmt->get_result();
$flight = $resFlight->fetch_assoc();
$stmt->close();
if (!$flight) die("❌ Không tìm thấy chuyến bay (ID: {$flight_id})");

// Kiểm tra flight_details có cột price không
$hasPriceColumn = false;
$dbname = $conn->query("SELECT DATABASE() AS dbname")->fetch_assoc()['dbname'];
if ($dbname) {
    $stmtChk = $conn->prepare("
        SELECT COUNT(*) AS cnt
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_schema = ? AND table_name = 'flight_details' AND column_name = 'price'
    ");
    if ($stmtChk) {
        $stmtChk->bind_param("s", $dbname);
        $stmtChk->execute();
        $r = $stmtChk->get_result()->fetch_assoc();
        $hasPriceColumn = intval($r['cnt']) > 0;
        $stmtChk->close();
    }
}

// Hàm lấy giá nhỏ nhất
function getMinPriceForDate($conn, $flight_id, $date, $hasPriceColumn, $fallbackPrice) {
    if ($hasPriceColumn) {
        $stmt = $conn->prepare("SELECT MIN(price) AS min_price FROM flight_details WHERE flight_id = ? AND DATE(departure_time) = ?");
        if (!$stmt) return $fallbackPrice;
        $stmt->bind_param("is", $flight_id, $date);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res['min_price'] !== null ? floatval($res['min_price']) : $fallbackPrice;
    } else {
        return $fallbackPrice !== null ? floatval($fallbackPrice) : null;
    }
}

// Sinh 7 ngày và lấy min price
$days = [];
$center = new DateTime($selected_date);

// Lùi 3 ngày để tab đầu tiên = selected_date -3
$center->modify('-2 day');

for ($i = 0; $i < 7; $i++) {
    $d = clone $center;
    $d->modify("+{$i} day");

    $dateStr = $d->format('Y-m-d');

    if ($d->format('Y-m-d') === date('Y-m-d')) {
        $label = "Hôm nay";
    } elseif ($d->format('Y-m-d') === date('Y-m-d', strtotime('+1 day'))) {
        $label = "Ngày mai";
    } else {
        $label = $d->format('d/m');
    }

    $minPrice = getMinPriceForDate($conn, $flight_id, $dateStr, $hasPriceColumn, $flight['price_new'] ?? null);

    $days[] = [
        'label' => $label,
        'date' => $dateStr,
        'min_price' => $minPrice,
        'active' => ($dateStr === $selected_date)
    ];
}


// Xử lý filter "Số điểm dừng"
$filterStops = $_GET['stops'] ?? ['0']; // mặc định chỉ bay thẳng

$stopConditions = [];
$params = [];
$paramTypes = "is"; // flight_id, date

foreach ($filterStops as $stop) {
    if ($stop == '0') {
        // Bay thẳng
        $stopConditions[] = "(transit_info = 'Bay thẳng' OR transit_info IS NULL OR transit_info = '')";
    } elseif ($stop == '1') {
        // 1 điểm dừng (Transit nhưng không có dấu phẩy)
        $stopConditions[] = "(transit_info LIKE 'Transit%' AND transit_info NOT LIKE '%,%')";
    } elseif ($stop == '2') {
        // Nhiều điểm dừng (có dấu phẩy)
        $stopConditions[] = "(transit_info LIKE '%,%')";
    }
}
$whereStops = "";
if (!empty($stopConditions)) {
    $whereStops = " AND (" . implode(" OR ", $stopConditions) . ")";
}


// ----- FILTER HÃNG HÀNG KHÔNG -----// Lấy filter hãng hàng không từ form
$allAirlines = ['Bamboo Airways','VietJet Air','Vietnam Airlines']; // tất cả các hãng

$filterAirlines = $_GET['airlines'] ?? []; // checkbox gửi lên
if (empty($filterAirlines)) {
    // Nếu không tick gì → mặc định tất cả
    $filterAirlines = $allAirlines;
}

// nếu không chọn gì -> mặc định chuyến bay chính của 'flights'

// Tạo điều kiện WHERE cho airline
$whereAirlines = "";
$airlineParams = [];
if (!empty($filterAirlines)) {
    $placeholders = implode(" OR ", array_fill(0, count($filterAirlines), "f.airline = ?"));
    $whereAirlines = " AND ($placeholders)";

    foreach ($filterAirlines as $al) {
        $airlineParams[] = $al;
    }
}
// ----- FILTER giờ bay -----//
$filterTimes = $_GET['timeRanges'] ?? [];
$timeConditions = [];

foreach ($filterTimes as $range) {
    switch ($range) {
        case '0-6':
            $timeConditions[] = "(HOUR(fd.departure_time) >= 0 AND HOUR(fd.departure_time) < 6)";
            break;
        case '6-12':
            $timeConditions[] = "(HOUR(fd.departure_time) >= 6 AND HOUR(fd.departure_time) < 12)";
            break;
        case '12-18':
            $timeConditions[] = "(HOUR(fd.departure_time) >= 12 AND HOUR(fd.departure_time) < 18)";
            break;
        case '18-24':
            $timeConditions[] = "(HOUR(fd.departure_time) >= 18 AND HOUR(fd.departure_time) < 24)";
            break;
    }
}

// Nếu người dùng không chọn gì → mặc định tất cả
$whereTimes = "";
if (!empty($timeConditions)) {
    $whereTimes = " AND (" . implode(" OR ", $timeConditions) . ")";
}



// Lấy chi tiết flight_details cho ngày được chọn
$sqlDetails = "
    SELECT fd.id, fd.flight_id, fd.departure_time, fd.arrival_time, fd.aircraft, fd.baggage_info, fd.transit_info, fd.description, f.airline
    FROM flight_details fd
    INNER JOIN flights f ON fd.flight_id = f.id
    WHERE fd.flight_id = ? 
      AND DATE(fd.departure_time) = ?
      $whereStops
      $whereAirlines
      $whereTimes
    ORDER BY fd.departure_time ASC
";

$stmt2 = $conn->prepare($sqlDetails);
if (!$stmt2) die("Prepare failed (details): " . $conn->error);

$paramTypes = "is" . str_repeat("s", count($airlineParams));
$params = array_merge([$flight_id, $selected_date], $airlineParams);

$stmt2->bind_param($paramTypes, ...$params);
$stmt2->execute();
$resDetails = $stmt2->get_result();
$details = $resDetails->fetch_all(MYSQLI_ASSOC);
$stmt2->close();




?>


