<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:index.php');
    exit; 
}

$message = [];

if (isset($_POST['add_to_cart'])) {
    $pid = trim($_POST['pid']);
    $p_name = trim($_POST['p_name']);
    $p_price = trim($_POST['p_price']);
    $p_image = trim($_POST['p_image']);
    $p_qty = trim($_POST['p_qty']);



    // Check if item is already in cart
    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->bind_param("si", $p_name, $user_id);
    $check_cart_numbers->execute();
    $check_cart_numbers->store_result();

    if ($check_cart_numbers->num_rows > 0) {
        $message[] = 'already added to cart!';
    } else {
        // Insert item into cart
        $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_cart->bind_param("isssis", $user_id, $pid, $p_name, $p_price, $p_qty, $p_image);
        $insert_cart->execute();
        $message[] = 'added to cart!';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>
   <link rel="stylesheet" href="./css/index.css">
   <script src="https://kit.fontawesome.com/8a540d2ee7.js" crossorigin="anonymous"></script>

</head>
<body>
   
<?php include 'header.php'; ?>
 <!-- slideshow -->
 <div class="slideshow-container">

<div class="mySlides fade">

    <img src="./image/home-bg.jpg" style="width:100%">
</div>

<div class="mySlides fade">

    <img src="./image/salloon.jpg" style="width:100%">
</div>
<div class="mySlides fade">

    <img src="./image/ac.jpg" style="width:100%">
</div>

<a class="prev" onclick="plusSlides(-1)">❮</a>
<a class="next" onclick="plusSlides(1)">❯</a>

</div>
<br>

<div style="text-align:center">
<span class="dot" onclick="currentSlide(1)"></span>
<span class="dot" onclick="currentSlide(2)"></span>
<span class="dot" onclick="currentSlide(3)"></span>
</div>
<!-- scrolling text -->
<div class="scrolling-text-container">
<div class="scrolling-text-inner" style="--marquee-speed: 10s; --direction:scroll-right" role="marquee">
    <div class="scrolling-text">
        <div class="scrolling-text-item">Summer Discount On Ac service</div>
        <div class="scrolling-text-item">25% off on Saloon Service </div>
        <div class="scrolling-text-item">Sale On Utility Service !!!</div>
        <div class="scrolling-text-item">Deals On refurbishment of home with 40%off</div>
        <div class="scrolling-text-item">Sale Sale Sale !!!</div>
        <div class="scrolling-text-item">Buy yearly membership for solar cleaning and get 45% off!</div>
    </div>
</div>
</div>

<!-- category -->
<section class="category-section">

<h1 class="category-title">Book By Services Category</h1>

<div class="category-container">

    <div class="category-item">
        <img src="./image/cleaning-service.jpeg" alt="cleaning Logo" class="category-logo">
        <h3>Cleaning</h3>
        <a href="product_category.php?category=cleaning" class="category-link">Book Service</a>
    </div>

    <div class="category-item">
        <img src="./image/repair-maintenance.png" alt="repairing Logo" class="category-logo">
        <h3>Repairing</h3>
        <a href="product_category.php?category=repairing" class="category-link">Book Service</a>
    </div>

    <div class="category-item">
        <img src="./image/salloon.jpg" alt="Logo" class="category-logo">
        <h3>Personal Care</h3>
        <a href="product_category.php?category=care" class="category-link">Book Service</a>
    </div>

    <div class="category-item">
        <img src="./image/paintin.jpg" alt="Logo" class="category-logo">
        <h3>Household Service</h3>
        <a href="product_category.php?category=household" class="category-link">Book Service</a>
    </div>

</div>

</section>
<!-- product details for all products as per ranking -->
<section class="products">

   <h1 class="title">All SERVICES</h1>

   <div class="container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY pd_rank ASC");
      $select_products->execute();
      $result = $select_products->get_result();
      if($result->num_rows > 0){
         while($fetch_products = $result->fetch_assoc()){ 
   ?>
  <form action="" class="box" method="POST">
   <img src="submitted_img/<?= $fetch_products['image']; ?>" alt="">
   <div class="name"><?= $fetch_products['name']; ?></div>
   <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
   <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
   <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
   <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
   <div class="price">Rs:<span><?= $fetch_products['price']; ?></span>/-</div>
   <input type="number" min="1" value="1" name="p_qty" class="qty">
   <input type="submit" value="add to cart" class="button" name="add_to_cart">
   <a href="product_view.php?pid=<?= $fetch_products['id']; ?>" class="button">View Services</a>
</form>
   <?php
      }
   }else{
      echo '<p class="empty">no services added yet!</p>';
   }
   ?>

   </div>

</section>
<script src="./slider.js"></script>

</body>
</html>