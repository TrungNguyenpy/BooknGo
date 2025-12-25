
<?php 
    include __DIR__ . '/../config/config.php';

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travel Search</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 
</head>
    <body>
    <div class="container">
              <!-- MENU -->
              <div class="menu">
                <div class="menu-item active" data-tab="hotel"><i class="fa-solid fa-hotel"></i>Khách sạn</div>
                <div class="menu-item" data-tab="flight"><i class="fa-solid fa-plane"></i>Vé máy bay</div>
                <div class="menu-item" data-tab="bus"><i class="fa-solid fa-bus"></i>Vé xe khách</div>
                <div class="menu-item" data-tab="activity"><i class="fa-solid fa-scissors"></i>Hoạt động & Vui chơi</div>
                <div class="menu-item" data-tab="more"><i class="fa-solid fa-ellipsis"></i>Khác</div>
              </div>

              <!-- TAB: Khách sạn -->
              <div id="hotel" class="tab-content active">
                <div class="search-row">
                  <div class="search-box"><i class="fa-solid fa-location-dot"></i><input type="text" placeholder="Thành phố, khách sạn, điểm đến"></div>
                  <div class="search-box"><i class="fa-solid fa-calendar"></i><input type="text" placeholder="Ngày nhận phòng - trả phòng"></div>
                  <div class="search-box"><i class="fa-solid fa-user"></i><input type="text" placeholder="2 người lớn, 0 trẻ em, 1 phòng"></div>
                  <button class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
              </div>

              <!-- TAB: Vé máy bay -->
              <div id="flight" class="tab-content">
                  <form action="search_flights.php" method="get" class="search-row">
                      <div class="search-box">
                          <i class="fa-solid fa-plane-departure"></i>
                          <select name="originLocation" required>
                              <option value="">Từ</option>
                          
                          </select>
                      </div>
                      <div class="search-box">
                          <i class="fa-solid fa-plane-arrival"></i>
                          <select name="destinationLocation" required>
                              <option value="">Đến</option>
                          
                          </select>
                      </div>
                      <div class="search-box">
                          <i class="fa-solid fa-calendar"></i>
                          <input type="date" name="departureDate" required>
                      </div>
                      <div class="search-box">
                          <i class="fa-solid fa-user"></i>
                          <input type="number" name="adults" value="1" min="1" placeholder="Người lớn">
                      </div>
                          <button type="submit" class="search-btn">
                              <i class="fa-solid fa-magnifying-glass"></i> 
                          </button> 
                  </form>
              </div>

              <!-- TAB: Vé xe khách -->
              <div id="bus" class="tab-content">
                <div class="search-row">
                  <div class="search-box"><i class="fa-solid fa-bus"></i><input type="text" placeholder="Từ (thành phố, bến xe)"></div>
                  <div class="search-box"><i class="fa-solid fa-bus"></i><input type="text" placeholder="Đến (thành phố, bến xe)"></div>
                  <div class="search-box"><i class="fa-solid fa-calendar"></i><input type="text" placeholder="Ngày khởi hành"></div>
                  <div class="search-box"><i class="fa-solid fa-user"></i><input type="text" placeholder="1 hành khách"></div>
                  <button class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
              </div>

              <!-- TAB: Hoạt động & Vui chơi -->
              <div id="activity" class="tab-content">
                <div class="search-row">
                  <div class="search-box" style="flex:3"><i class="fa-solid fa-lightbulb"></i><input type="text" placeholder="Bạn có ý tưởng gì cho chuyến đi tiếp theo không?"></div>
                  <button class="search-btn">Khám phá</button>
                </div>
              </div>
    </div>
  <!-- JS -->
  <script>
    const menuItems = document.querySelectorAll(".menu-item");
    const tabs = document.querySelectorAll(".tab-content");

    menuItems.forEach(item => {
      item.addEventListener("click", () => {
        menuItems.forEach(i => i.classList.remove("active"));
        tabs.forEach(t => t.classList.remove("active"));

        item.classList.add("active");
        const tabId = item.dataset.tab;
        const tabContent = document.getElementById(tabId);
        if (tabContent) tabContent.classList.add("active");
      });
    });
  </script>
</body>
</html>
