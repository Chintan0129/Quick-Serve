<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit; 
}

// Logic for adding product to cart
if(isset($_POST['add_to_cart'])){

   $pid = trim($_POST['product_id']);
   $p_name = trim($_POST['p_name']);
   $p_price = trim($_POST['p_price']);
   $p_image = trim($_POST['p_image']);
   $p_qty = trim($_POST['p_qty']);

   // Check if the product is already in the cart
   $check_cart_query = "SELECT * FROM `cart` WHERE name = ? AND user_id = ?";
   $check_cart_stmt = mysqli_prepare($conn, $check_cart_query);
   mysqli_stmt_bind_param($check_cart_stmt, "si", $p_name, $user_id);
   mysqli_stmt_execute($check_cart_stmt);
   $check_cart_result = mysqli_stmt_get_result($check_cart_stmt);

   if(mysqli_num_rows($check_cart_result) > 0){
      $message[] = 'Already added to cart!';
   }else{

      // Insert the product into the cart
      $insert_cart_query = "INSERT INTO `cart`(user_id, product_id, name, price, quantity, image) VALUES(?,?,?,?,?,?)";
      $insert_cart_stmt = mysqli_prepare($conn, $insert_cart_query);
      mysqli_stmt_bind_param($insert_cart_stmt, "isssis", $user_id, $pid, $p_name, $p_price, $p_qty, $p_image);
      mysqli_stmt_execute($insert_cart_stmt);
      $message[] = 'Added to cart!';
   }
}

// Logic for adding comments
if(isset($_POST['add_comment'])) {
   $comment = trim($_POST['comment']);
   $product_id = $_GET['pid'];

   // Insert comment into the database
   $insert_comment_query = "INSERT INTO `comments` (product_id, comment) VALUES (?, ?)";
   $insert_comment_stmt = mysqli_prepare($conn, $insert_comment_query);

   // Check if the statement was prepared successfully
   if ($insert_comment_stmt === false) {
      die('Error preparing statement: ' . mysqli_error($conn));
   }

   // Bind parameters and execute the statement
   mysqli_stmt_bind_param($insert_comment_stmt, "is", $product_id, $comment);
   mysqli_stmt_execute($insert_comment_stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Product_view</title>
   <script src="https://kit.fontawesome.com/8a540d2ee7.js" crossorigin="anonymous"></script>
   <!--  css link for this particular file only-->
   <link rel="stylesheet" href="./css/view_page.css">

</head>
<body>
   
<?php include 'header.php'; ?>
<!-- view product -->
<section class="view-product">

   <h1 class="title">Product/Service View</h1>

   <?php
      $pid = $_GET['pid'];
      $select_products_query = "SELECT * FROM `products` WHERE id = ?";
      $select_products_stmt = mysqli_prepare($conn, $select_products_query);
      mysqli_stmt_bind_param($select_products_stmt, "i", $pid);
      mysqli_stmt_execute($select_products_stmt);
      $select_products_result = mysqli_stmt_get_result($select_products_stmt);

      if(mysqli_num_rows($select_products_result) > 0){
         while($product = mysqli_fetch_assoc($select_products_result)){ 
   ?>
   <!-- particular product view -->
   <div class="product-card">
      <div class="product-image">
         <img src="submitted_img/<?= $product['image']; ?>" alt="<?= $product['name']; ?>">
      </div>
      <div class="product-details">
         <div class="product-name"><?= $product['name']; ?></div>
         <div class="product-description"><?= $product['about']; ?></div>
         <div class="product-price">Rs:<?= $product['price']; ?>/-</div>
         <div class="product-price">Ranking:<?= $product['pd_rank']; ?></div>
         <form action="" method="POST">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            <input type="hidden" name="p_name" value="<?= $product['name']; ?>">
            <input type="hidden" name="p_price" value="<?= $product['price']; ?>">
            <input type="hidden" name="p_image" value="<?= $product['image']; ?>">
            <label for="p_qty">Quantity:</label>
            <input type="number" id="p_qty" min="1" value="1" name="p_qty" class="qty">
            <button type="submit" class="button" name="add_to_cart">Add to Cart</button>
         </form>
         <div><a href="home.php" class="button">Back</a></div>
      </div>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">No products added yet!</p>';
      }
   ?>

   <!-- Comment section -->
   <div class="comment-section">
      <h2>Comments</h2>
      <?php
         // Retrieve comments for the product
         $comments_query = "SELECT * FROM `comments` WHERE product_id = ?";
         $comments_stmt = mysqli_prepare($conn, $comments_query);
         mysqli_stmt_bind_param($comments_stmt, "i", $pid);
         mysqli_stmt_execute($comments_stmt);
         $comments_result = mysqli_stmt_get_result($comments_stmt);

         if(mysqli_num_rows($comments_result) > 0){
            while($comment = mysqli_fetch_assoc($comments_result)){ 
      ?>
         <div class="comment">
            <p><?= $comment['comment']; ?></p>
         </div>
      <?php
            }
         } else {
            echo '<p>No comments yet.</p>';
         }
      ?>
      <!-- Form to add a new comment -->
      <form action="" method="POST">
         <textarea name="comment" placeholder="Add your comment here..." required></textarea>
         <button type="submit" class="button" name="add_comment">Add Comment</button>
      </form>
   </div>

</section>

</body>
</html>
