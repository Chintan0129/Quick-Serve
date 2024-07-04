<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location: login.php');
    exit; 
}

$message = [];

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
    $delete_cart_item->bind_param("i", $delete_id); // Bind the parameter
    $delete_cart_item->execute(); // Execute the statement
    header('location: cart.php');
    exit; 
}

if (isset($_GET['delete_all'])) {
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart_item->bind_param("i", $user_id); // Bind the parameter
    $delete_cart_item->execute(); // Execute the statement
    header('location: cart.php');
    exit; 
}

if (isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $p_qty = $_POST['p_qty'];
    $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
    $update_qty->bind_param("ii", $p_qty, $cart_id); // Bind the parameters
    $update_qty->execute(); // Execute the statement
    $message[] = 'cart quantity updated';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>
   <link rel="stylesheet" href="./css/shooping_cart.css">
   <script src="https://kit.fontawesome.com/8a540d2ee7.js" crossorigin="anonymous"></script>
</head>
<body>
   
<?php include 'header.php'; ?>
<!-- shopping cart details -->
<section class="shopping-cart">
   <h1 class="title">Services added</h1>
   <form action="" method="post">
   <table class="cart-table">
      <thead>
         <tr>
            <th>Action</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Update</th>
            <th>Sub Total</th>
         </tr>
      </thead>
      <tbody>
         <?php
            $total_amount = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->bind_param("i", $user_id); // Bind the parameter
            $select_cart->execute(); // Execute the statement
            $result = $select_cart->get_result();
            if ($result->num_rows > 0) {
               while ($fetch_cart = $result->fetch_assoc()) { 
                  ?>
                  <tr>
                     <td>
                        <a href="cart.php?delete=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this from cart?');"></a>
                        <a href="product_view.php?pid=<?= $fetch_cart['product_id']; ?>" class="fas fa-eye"></a>
                     </td>
                     <td><img src="submitted_img/<?= $fetch_cart['image']; ?>" alt=""></td>
                     <td><?= htmlspecialchars($fetch_cart['name']); ?></td>
                     <td>Rs:<?= htmlspecialchars($fetch_cart['price']); ?>/-</td>
                     <td>
                        <form action="" method="POST">
                           <input type="hidden" name="cart_id" value="<?= htmlspecialchars($fetch_cart['id']); ?>">
                           <input type="number" min="1" value="<?= htmlspecialchars($fetch_cart['quantity']); ?>" class="qty" name="p_qty">
                           
                        </form>
                     </td>
                     <td><input type="submit" value="Update" name="update_qty" class="option-btn"></td>
                     <td>Rs:<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</td>
                  </tr>
                  <?php
                  $total_amount += $sub_total;
               }
            } else {
               echo '<tr><td colspan="6" class="empty">your cart is empty</td></tr>';
            }
         ?>
      </tbody>
   </table>
   </form>
   <div class="total">
      <p>Total Amount : <span>Rs:<?= htmlspecialchars($total_amount); ?>/-</span></p>
      <a href="home.php" class="opt-btn">book any other service</a>
      <?php if ($result->num_rows > 0) { ?>
      <a href="cart.php?delete_all" class="opt-btn">delete all</a>
      
         <a href="order_details.php" class="opt-btn">Enter Details for order</a>
      <?php } ?>
   </div>
</section>
</body>
</html>