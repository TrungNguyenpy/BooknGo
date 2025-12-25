
<?php 
    include __DIR__ . '/../config/config.php';

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./css/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/nav.css?v=<?php echo time(); ?>">

</head>
<body>

<div class="main container my-5">   

      
  <div class="main container my-5">   

    <!-- Khách sạn -->
    <section id="Hotel">
        <?php include __DIR__ . '/../pages/hotel.php' ?>
    </section>

    <!-- Vui chơi / Tour -->
    <section id="Tour">
        <?php include __DIR__ . '/../pages/tour.php' ?>
    </section>

    <!-- Máy bay -->
    <section id="Flight">
        <?php include __DIR__ . '/../pages/flight.php' ?>
    </section>

    <!-- Địa điểm -->
    <section id="Destination">
        <?php include __DIR__ . '/../pages/destination.php' ?>
    </section>

</div>
 
    

                
<!-- Dialogflow Messenger -->
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
intent="WELCOME"
chat-title="TravelBot"
agent-id="2d987706-4913-4856-bd44-9c9654e96aa5"
language-code="vi">
</df-messenger>
</div>    
<script src="./js/main.js" defer></script>

</body>
</html>
