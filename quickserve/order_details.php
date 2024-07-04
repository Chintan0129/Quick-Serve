<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit; // Ensure script termination after redirection
}
//if method is post and input type is submit
if (isset($_POST['order'])) {
    $name = trim($_POST['name']);
    $number = trim($_POST['number']);
    $email = trim($_POST['email']);
    $method = trim($_POST['method']);
    $address=$_POST['address'];
    $placed_on = date('d-M-Y');

    // Retrieve cart items and calculate total
    $cart_total = 0;
    $cart_products = [];
    $product_ids = [];
    $product_names = [];

    $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $result = $cart_query->get_result();

    if ($result->num_rows > 0) {
        while ($cart_item = $result->fetch_assoc()) {
            $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;

            // fetch product id and name  from cart 
            $product_ids[] = $cart_item['product_id'];
            $product_names[] = $cart_item['name'];
        }
    }

    // Convert arrays to comma-separated strings
    $product_ids_str = implode(',', $product_ids);
    $product_names_str = implode(',', $product_names);

    // Insert order details into the orders table
    $insert_order_query = $conn->prepare("INSERT INTO `orders`(user_id, product_id, product_name, name, number, email, method, address, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?,?)");
    $insert_order_query->bind_param("isssssssds", $user_id , $product_ids_str, $product_names_str , $name, $number, $email, $method, $address, $cart_total, $placed_on );

    $insert_order_query->execute();

    // Delete cart items after placing the order
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart->bind_param("i", $user_id);
    $delete_cart->execute();

    $message[] = 'Order placed successfully!';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Order-detail</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="./css/order_data.css">

</head>
<body>
   <!-- order details on checkout -->
<?php include 'header.php'; ?>
<?php
            $select_profile = $conn->prepare("SELECT * FROM `sc_users` WHERE id = ?");
            $select_profile->bind_param("i", $user_id);
            $select_profile->execute();
            $fetch_profile_result = $select_profile->get_result();
            if ($fetch_profile_result->num_rows > 0) {
                $fetch_profile = $fetch_profile_result->fetch_assoc();
                ?>
                <?php
            }
    ?>
      <h1 class="heading">Place Your Order</h1>
<section class="order__details">
 
   <form action="" method="POST">
      <table>
         <tr>
            <td><label for="name">Your Name:</label></td>
            <td><input type="text" name="name" id="name" value="<?= $fetch_profile['name']; ?>" placeholder="Enter your name" required></td>
         </tr>
         <tr>
            <td><label for="number">Your Number:</label></td>
            <td><input type="number" name="number" id="number" value="<?= $fetch_profile['mobile']; ?>" placeholder="Enter your number" required></td>
         </tr>
         <tr>
            <td><label for="email">Your Email:</label></td>
            <td><input type="email" name="email" id="email" value="<?= $fetch_profile['email']; ?>" placeholder="Enter your email" required></td>
         </tr>
         <tr>
            <td><label for="method">Payment Method:</label></td>
            <td>
               <select name="method" id="method" required>
                  <option value="cash on delivery">Cash on Delivery</option>
                  <option value="credit card">Credit Card</option>
                  <option value="debit card">Debit card</option>
                  <option value="google pay">Google Pay</option>
                  <option value="paypal">paypal</option>
                  <option value="net banking">Net banking</option>
               </select>
            </td>
         </tr>
         <tr>
            <td><label for="address">Address:</label></td>
            <td><input type="text" name="address" id="address" placeholder="enter address for service" required></td>
         </tr>
         <tr>
            <td><label for="pin_code">Pin Code:</label></td>
            <td><input type="number" min="0" name="pin_code" id="pin_code" placeholder="E.g. 123456" required></td>
         </tr>
      </table>
      <input type="submit" name="order" class="button"  value="Place Order">
   </form>
</section>
</body>
</html>
