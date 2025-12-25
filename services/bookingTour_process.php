<?php
include __DIR__ . '/../config/config.php';

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("❌ Yêu cầu không hợp lệ.");
}

// Lấy dữ liệu người dùng từ form
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');

if (empty($fullname) || empty($email) || empty($phone)) {
    die("❌ Vui lòng nhập đầy đủ thông tin liên hệ!");
}

// Lấy dữ liệu vé
$ticket_id   = (int)($_POST['ticket_id'] ?? 0);
$adult_qty   = (int)($_POST['adult_qty'] ?? 0);
$child_qty   = (int)($_POST['child_qty'] ?? 0);
$total_price = (float)($_POST['total_price'] ?? 0);

$service_type = 'tours'; // loại dịch vụ
$service_id   = $ticket_id;
$status       = 'pending'; // mặc định là chờ xác nhận

if ($ticket_id <= 0 || $total_price <= 0) {
    die("❌ Dữ liệu không hợp lệ!");
}

// ========================
// 1️⃣ Lưu hoặc tìm user
// ========================
$sqlUser = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$resUser = $stmtUser->get_result();

if ($rowUser = $resUser->fetch_assoc()) {
    $user_id = $rowUser['user_id'];
} else {
    $sqlInsertUser = "INSERT INTO users (fullname, email, phone, created_at) VALUES (?, ?, ?, NOW())";
    $stmtInsertUser = $conn->prepare($sqlInsertUser);
    $stmtInsertUser->bind_param("sss", $fullname, $email, $phone);
    $stmtInsertUser->execute();
    $user_id = $stmtInsertUser->insert_id;
    $stmtInsertUser->close();
}
$stmtUser->close();

// ========================
// 2️⃣ Lưu vào bảng bookings
// ========================
$sql = "INSERT INTO bookings (
            user_id, service_type, service_id, total_price, status, booking_date, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isids", $user_id, $service_type, $service_id, $total_price, $status);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id;

    // (Tuỳ chọn) Lưu chi tiết số lượng người lớn / trẻ em vào bảng riêng
    // $conn->query("INSERT INTO booking_details (booking_id, adult_qty, child_qty) VALUES ($booking_id, $adult_qty, $child_qty)");

    echo json_encode(['success' => true, 'message' => 'Đặt vé thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu dữ liệu: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
