<?php
// bookingFlight_process.php
// Phiên bản: lưu user (nếu chưa có) -> lưu booking -> lưu passengers -> redirect payment
// Đặt file cùng folder với form (hoặc chỉnh include path)

include __DIR__ . '/../config/config.php';
session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

// --- LẤY DỮ LIỆU TỪ FORM ---
// Thông tin người đặt (người dùng chính)
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$identity_number_user = trim($_POST['identity_number'] ?? ''); // CCCD người đặt (nếu có)

// Thông tin chuyến / vé
$detail_id = intval($_POST['detail_id'] ?? 0);
if ($detail_id <= 0) {
    die('Missing flight detail id');
}
$adult_count = max(1, intval($_POST['adult_count'] ?? 1));
$child_count = max(0, intval($_POST['child_count'] ?? 0));
$infant_count = max(0, intval($_POST['infant_count'] ?? 0));

// Mảng hành khách (JS đã sinh)
$passenger_types = $_POST['passenger_type'] ?? [];
$passenger_titles = $_POST['passenger_title'] ?? [];
$passenger_last = $_POST['passenger_last_name'] ?? [];
$passenger_first = $_POST['passenger_first_name'] ?? [];
$passenger_dob = $_POST['passenger_dob'] ?? [];
$passenger_identity = $_POST['passenger_identity'] ?? []; // mảng CCCD mỗi hành khách

// Validate số lượng hành khách khớp
$totalExpected = $adult_count + $child_count + $infant_count;
if (count($passenger_types) !== $totalExpected
    || count($passenger_first) !== $totalExpected
    || count($passenger_last) !== $totalExpected) {
    die('Dữ liệu hành khách không hợp lệ hoặc bị thiếu.');
}

// --- LẤY THÔNG TIN GIÁ TỪ DB ---
$stmt = $conn->prepare("
    SELECT fd.*, f.flight_name, f.departure AS dep_airport, f.arrival AS arr_airport, f.airline, f.price_new, fd.departure_time
    FROM flight_details fd
    JOIN flights f ON fd.flight_id = f.id
    WHERE fd.id = ?
");
$stmt->bind_param("i", $detail_id);
$stmt->execute();
$res = $stmt->get_result();
$flightRow = $res->fetch_assoc();
$stmt->close();

if (!$flightRow) {
    die('Không tìm thấy chuyến bay.');
}
$basePrice = (float)$flightRow['price_new'];
$departure_date = $flightRow['departure_time'] ?? null;

// hệ số giá (theo thỏa thuận)
$coeff = ['adult' => 1.0, 'child' => 0.75, 'infant' => 0.10];
$price_adult  = round($basePrice * $coeff['adult'], 2);
$price_child  = round($basePrice * $coeff['child'], 2);
$price_infant = round($basePrice * $coeff['infant'], 2);
$total_price = $adult_count * $price_adult + $child_count * $price_child + $infant_count * $price_infant;

// --- BẮT ĐẦU TRANSACTION ---
$conn->begin_transaction();

try {
    // --- 1) TÌM HOẶC TẠO USER ---
    $user_id = null;
    if ($email !== '' || $phone !== '') {
        // tìm theo email hoặc phone
        $q = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $q->bind_param("ss", $email, $phone);
        $q->execute();
        $r = $q->get_result();
        if ($row = $r->fetch_assoc()) {
            $user_id = intval($row['user_id']);
        }
        $q->close();
    }

    if (!$user_id) {
        // chèn user mới (không password)
        $ins = $conn->prepare("INSERT INTO users (fullname, email, phone, identity_number, created_at) VALUES (?, ?, ?, ?, ?)");
        $now = date('Y-m-d H:i:s');
        $ins->bind_param("sssss", $fullname, $email, $phone, $identity_number_user, $now);
        if (!$ins->execute()) {
            throw new Exception('Lỗi khi tạo user: ' . $ins->error);
        }
        $user_id = $ins->insert_id;
        $ins->close();
    }

    // --- 2) INSERT BOOKING ---
    // Chuẩn cấu trúc bảng bookings theo bạn cung cấp
    $booking_date = date('Y-m-d H:i:s');
    $status = 'pending';
    $created_at = $booking_date;
    $updated_at = $booking_date;
    $return_date = null; // nếu có từ form, lấy ở đây

    $insertSql = "INSERT INTO bookings
    (user_id, service_type, service_id, booking_date, status, total_price, created_at, updated_at, departure_date, return_date,
     num_adults, num_children, num_infants, price_adult, price_child, price_infant)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $service_type = 'flights';
        $service_id = $detail_id;
        $dep_date_param = $departure_date ?: null;
        $ret_date_param = $return_date ?: null;

        $stmt = $conn->prepare($insertSql);
        if ($stmt === false) {
            throw new Exception('Prepare failed (bookings insert): ' . $conn->error);
        }

   
        $bindTypes = "isissdssssiiiddd";

        $stmt->bind_param(
            $bindTypes,
            $user_id,         // i
            $service_type,    // s
            $service_id,      // i
            $booking_date,    // s
            $status,          // s
            $total_price,     // d
            $created_at,      // s
            $updated_at,      // s
            $dep_date_param,  // s
            $ret_date_param,  // s
            $adult_count,     // i
            $child_count,     // i
            $infant_count,    // i
            $price_adult,     // d
            $price_child,     // d
            $price_infant     // d
        );


    if (!$stmt->execute()) {
        throw new Exception('Lỗi khi tạo booking: ' . $stmt->error);
    }
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // --- 3) INSERT passengers nếu bảng tồn tại ---
    $check = $conn->query("SHOW TABLES LIKE 'booking_passengers'");
    if ($check && $check->num_rows > 0) {
        // kiểm tra cột identity_number tồn tại không
        $hasIdentity = false;
        $colRes = $conn->query("SHOW COLUMNS FROM booking_passengers LIKE 'identity_number'");
        if ($colRes && $colRes->num_rows > 0) $hasIdentity = true;

        if ($hasIdentity) {
            $pstmt = $conn->prepare("INSERT INTO booking_passengers (booking_id, passenger_index, passenger_type, title, last_name, first_name, dob, identity_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        } else {
            $pstmt = $conn->prepare("INSERT INTO booking_passengers (booking_id, passenger_index, passenger_type, title, last_name, first_name, dob, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        }
        if ($pstmt === false) throw new Exception('Prepare failed booking_passengers: ' . $conn->error);

        $created_at_pass = date('Y-m-d H:i:s');
        for ($idx = 0; $idx < $totalExpected; $idx++) {
            $ptype = $passenger_types[$idx] ?? 'adult';
            $title = $passenger_titles[$idx] ?? '';
            $ln = $passenger_last[$idx] ?? '';
            $fn = $passenger_first[$idx] ?? '';
            $dob = $passenger_dob[$idx] ?? null;
            $identity = $passenger_identity[$idx] ?? '';

            $pIndex = $idx + 1;
            if ($hasIdentity) {
                // types: i (booking_id), i (passenger_index), s (passenger_type), s (title), s (last_name), s (first_name), s (dob), s (identity_number), s (created_at)
                $pstmt->bind_param("iisssssss", $booking_id, $pIndex, $ptype, $title, $ln, $fn, $dob, $identity, $created_at_pass);
            } else {
                // types: i i s s s s s s
                $pstmt->bind_param("iissssss", $booking_id, $pIndex, $ptype, $title, $ln, $fn, $dob, $created_at_pass);
            }
            if (!$pstmt->execute()) {
                throw new Exception('Lỗi khi tạo passenger #' . $pIndex . ': ' . $pstmt->error);
            }
        }
        $pstmt->close();
    }

    // COMMIT nếu mọi thứ ok
    $conn->commit();

    // Redirect to payment ( LỖI CẦN SỬA)
        // LƯU booking_id vào SESSION (an toàn)
        $_SESSION['booking_id'] = $booking_id;

        // Redirect to payment
        header("Location: payment.php");
        exit;

} catch (Exception $ex) {
    // ROLLBACK on error
    $conn->rollback();
    // Log and show error
    error_log("Booking error: " . $ex->getMessage());
    // Friendly message to user
    echo "<h3>Lỗi khi xử lý đặt vé</h3>";
    echo "<p>" . htmlspecialchars($ex->getMessage()) . "</p>";
    exit;
}
