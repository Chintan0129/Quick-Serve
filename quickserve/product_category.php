<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit; // Ensure script termination after redirection
}

if (isset($_POST['add_to_cart'])) {

   $pid = trim($_POST['pid']);
   $p_name = trim($_POST['p_name']);
   $p_price = trim($_POST['p_price']);
   $p_image = trim($_POST['p_image']);
   $p_qty = trim($_POST['p_qty']);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->bind_param("si", $p_name, $user_id);
   $check_cart_numbers->execute();
   $result = $check_cart_numbers->get_result();

   if ($result->num_rows > 0) {
      $message[] = 'Already added to cart!';
   } else {

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, product_id, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->bind_param("isssis", $user_id, $pid, $p_name, $p_price, $p_qty, $p_image);
      $insert_cart->execute();
      $message[] = 'Added to cart!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>category</title>
   <script src="https://kit.fontawesome.com/8a540d2ee7.js" crossorigin="anonymous"></script>
   

</head>

<body>

   <?php include 'header.php'; ?>

   <section class="products">

      <h1 class="title">Particular Category</h1>
<!-- product dispaly -->
      <div class="container">

         <?php
        $category_name = $_GET['category'];
        $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ? ORDER BY pd_rank ASC");
        $select_products->bind_param("s", $category_name);
        $select_products->execute();
        $result = $select_products->get_result();
         if ($result->num_rows > 0) {
            while ($fetch_products = $result->fetch_assoc()) {
         ?>
               <form action="" class="box" method="POST">
                  <img src="submitted_img/<?= $fetch_products['image']; ?>" alt="">
                  <div class="name"><?= $fetch_products['name']; ?></div>
                  <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                  <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
                  <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
                  <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
                  <div class="price">Rs:<span><?= $fetch_products['price']; ?></span>/-</div>
                  <div class="rank">Ranking:<span><?= $fetch_products['pd_rank']; ?></span></div>
                  <input type="number" min="1" value="1" name="p_qty" class="qty">
                  <input type="submit" value="add to cart" class="button" name="add_to_cart">
                  <div ><a href="home.php" class="button">Back</a></div>
                  
               </form>
             
         <?php
            }
         } else {
            echo '<p class="empty">no products available!</p>';
         }
         ?>

      </div>

   </section>
</body>

</html>