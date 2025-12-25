<?php
include __DIR__ . '/../../../config/config.php';

$city_id = $_GET['city_id'] ?? '';
$type = $_GET['type'] ?? '';

$tables = ['hotels','places','foods','events'];
if(!$city_id || !in_array($type,$tables)){
    echo "<p>Dữ liệu không hợp lệ!</p>";
    exit;
}

// Lấy dữ liệu
$sql = "SELECT * FROM $type WHERE city_id = '$city_id'";
$result = $conn->query($sql);

if($result && $result->num_rows>0){
    echo '<div class="row g-3">';
    while($row=$result->fetch_assoc()){
        $name = htmlspecialchars($row['name']);
        $desc = htmlspecialchars($row['description'] ?? '');
        $image = htmlspecialchars($row['image'] ?? $row['image_url'] ?? 'img/default.png');

        echo '<div class="col-md-4">';
        echo '<div class="card">';
        echo '<img src="'.$base_url.$image.'" class="card-img-top" alt="'.$name.'">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">'.$name.'</h5>';
        if(!empty($desc)) echo '<p class="card-text">'.$desc.'</p>';
        echo '</div></div></div>';
    }
    echo '</div>';
}else{
    echo "<p>Chưa có dữ liệu $type cho city_id=$city_id</p>";
}
