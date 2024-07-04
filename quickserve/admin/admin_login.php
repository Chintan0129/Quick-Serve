<?php

@include 'config.php';

session_start();

$message = [];
//login logic
if(isset($_POST['submit'])){

   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $pass = $_POST['pass'];

   $sql = "SELECT id, email, password FROM admins WHERE email = ?";
   $stmt = mysqli_prepare($conn, $sql);
   mysqli_stmt_bind_param($stmt, 's', $email);
   mysqli_stmt_execute($stmt);
   mysqli_stmt_store_result($stmt);
   $rowCount = mysqli_stmt_num_rows($stmt);  

   if($rowCount > 0){

      mysqli_stmt_bind_result($stmt, $id, $email, $password);
      mysqli_stmt_fetch($stmt);

      if(password_verify($pass, $password)){

         $_SESSION['admin_id'] = $id;
         header('location:admin_homepage.php');

      } else {
         $message[] = 'Incorrect email or password!';
      }

   } else {
      $message[] = 'Incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <link rel="stylesheet" href="../css/login.css">
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message">' . $msg . '</div>';
   }
}
?>
<!-- login form -->
<section class="form-container">
   <form action="" method="POST">
      <h3>Admin Login</h3>
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
      <input type="submit" value="Login" class="btn" name="submit">
   </form>
   <a href="../login.php">User login</a>
</section>

</body>
</html>
