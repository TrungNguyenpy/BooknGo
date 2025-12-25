<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "bookngo"; 

// Kết nối đến MySQL
$conn = new mysqli($host, $user, $pass, $dbname);




// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập charset UTF-8
if (!$conn->set_charset("utf8")) {
    die("Lỗi khi thiết lập charset UTF-8: " . $conn->error);
}

 // Cập nhật chỉ những chuyến bay trước ngày hôm nay
$sql = "UPDATE flight_details
SET 
    departure_time = CONCAT(CURDATE(), ' ', TIME(departure_time)),
    arrival_time = CONCAT(CURDATE(), ' ', TIME(arrival_time))
WHERE DATE(departure_time) < CURDATE()";

$result = $conn->query($sql);

if ($result) {

} else {
echo "❌ Lỗi: " . $conn->error;
}


$base_url = "/Web/BooknGo";

?>
