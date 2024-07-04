<?php

@include 'config.php'; 

session_start(); 

$message = []; 

if(isset($_POST['submit'])){ 

   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize the email input
   $pass = $_POST['pass']; // Get the password input

   $sql = "SELECT id, email, password FROM sc_users WHERE email = ?"; 
   $stmt = mysqli_prepare($conn, $sql); 
   mysqli_stmt_bind_param($stmt, 's', $email); 
   mysqli_stmt_execute($stmt); 
   mysqli_stmt_store_result($stmt); 
   $rowCount = mysqli_stmt_num_rows($stmt);  

   if($rowCount > 0){ // If user exists in the database

      mysqli_stmt_bind_result($stmt, $id, $email, $password); // Bind the result variables
      mysqli_stmt_fetch($stmt); // Fetch the result

      if(password_verify($pass, $password)){ // Verify the password

         $_SESSION['user_id'] = $id; // Store user ID in session
         mergeCartItems($conn, $id); // Merge cart items if any
         header('location:home.php'); // Redirect to home page

      } else {
         $message[] = 'Incorrect email or password!'; // Password verification failed
      }

   } else {
      $message[] = 'Incorrect email or password!'; // User does not exist
   }

}

function mergeCartItems($conn, $user_id) {
    if(isset($_COOKIE['cart'])) { // Check if cart cookie is set
        $cookie_cart = json_decode($_COOKIE['cart'], true); 
        foreach($cookie_cart as $item) { 
            $pid = $item['pid']; 
            $p_name = $item['p_name']; 
            $p_price = $item['p_price']; 
            $p_image = $item['p_image']; 
            $p_qty = $item['p_qty']; 

            // Prepare and execute SQL statement to insert cart items into the database
            $insert_cart = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert_cart, 'iisdis', $user_id, $pid, $p_name, $p_price, $p_qty, $p_image);
            mysqli_stmt_execute($insert_cart);
        }
        // Clear the cart cookie after merging items
        setcookie('cart', '', time() - 3600, '/');
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="css/login.css">
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message">' . $msg . '</div>';
   }
}
?>

<section class="form-container">
   <form action="" method="POST">
      <h3>Login Form</h3>
      <table>
         <tr>
            <td><label for="email">Email:</label></td>
            <td><input type="email" id="email" name="email" class="box" placeholder="Enter your email" required></td>
         </tr>
         <tr>
            <td><label for="pass">Password:</label></td>
            <td><input type="password" id="pass" name="pass" class="box" placeholder="Enter your password" required></td>
         </tr>
      </table>
      <input type="submit" value="Login now" class="btn" name="submit">
      <p>Don't have an account? <a href="register.php">Register now</a></p>
      <p>Admin Login <a href="./admin/admin_login.php">Admin</a></p>
   </form>
</section>

</body>
</html>
