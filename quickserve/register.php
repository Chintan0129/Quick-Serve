<?php

include 'config.php';

$message = []; // Initialize an empty array to store validation messages

if(isset($_POST['submit'])){

   $name = trim($_POST['name']);
   $email = trim($_POST['email']);
   $pass = trim($_POST['pass']);
   $cpass = trim($_POST['cpass']);
   $mobile = trim($_POST['mobile']);

   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_size = $_FILES['image']['size'];
   $image_folder = 'submitted_img/'.$image;

   // Validate username length
   if(strlen($name) < 6) {
      $message['name'] = 'Username must be at least 6 characters long!';
   }

   // Validate image size
   if($image_size > 2000000){
      $message['image'] = 'Image size is too large!';
   }

   // Validate password length
   if(strlen($pass) < 8) {
      $message['pass'] = 'Password must be at least 8 characters long!';
   }

   // Check if passwords match
   if($pass !== $cpass){
      $message['cpass'] = 'Passwords do not match!';
   }

   // Validate mobile number
   if(!preg_match('/^\d{10}$/', $mobile)) {
      $message['mobile'] = 'Mobile number must be 10 digits long!';
   }

   // Check if email already exists
   $select = mysqli_prepare($conn, "SELECT * FROM sc_users WHERE email = ?");
   mysqli_stmt_bind_param($select, 's', $email);
   mysqli_stmt_execute($select);
   mysqli_stmt_store_result($select);

   if(mysqli_stmt_num_rows($select) > 0){
      $message['email'] = 'User email already exists!';
   } else {
      if(empty($message)) { // Proceed with registration only if there are no validation errors
         // Hash password
         $hashed_password = password_hash($pass, PASSWORD_BCRYPT);
         
         // Insert user data into database
         $insert = mysqli_prepare($conn, "INSERT INTO sc_users (name, email, password, mobile, image, registration_date) VALUES (?, ?, ?, ?, ?, NOW())");
         mysqli_stmt_bind_param($insert, 'sssss', $name, $email, $hashed_password, $mobile, $image);
         mysqli_stmt_execute($insert);

         if($insert){
            // Move uploaded image to folder
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Registered successfully!';
            header('location:login.php');
            exit;
         } else {
            $message[] = 'Failed to register!';
         }
      }
   }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <!-- custom css file link  -->
   <link rel="stylesheet" href="./css/register.css">
  
</head>
<body>
<h1>Register now</h1>
   <section class="form-container">
   <form action="" enctype="multipart/form-data" method="POST">
    
      <table>
         <tr>
            <td><label for="name">Name:</label></td>
            <td><input type="text" id="name" name="name" class="box" placeholder="Enter your name" required></td>
         </tr>
         <tr>
            <td></td>
            <td class="error">
               <?php echo isset($message['name']) ? $message['name'] : ''; ?>
            </td>
         </tr>
         <tr>
            <td><label for="email">Email:</label></td>
            <td><input type="email" id="email" name="email" class="box" placeholder="Enter your email" required></td>
         </tr>
         <tr>
            <td></td>
            <td class="error">
               <?php echo isset($message['email']) ? $message['email'] : ''; ?>
            </td>
         </tr>
         <tr>
            <td><label for="pass">Password:</label></td>
            <td><input type="password" id="pass" name="pass" class="box" placeholder="Enter your password" required></td>
         </tr>
         <tr>
            <td></td>
            <td class="error">
               <?php echo isset($message['pass']) ? $message['pass'] : ''; ?>
            </td>
         </tr>
         <tr>
            <td><label for="cpass">Confirm Password:</label></td>
            <td><input type="password" id="cpass" name="cpass" class="box" placeholder="Confirm your password" required></td>
         </tr>
         <tr>
            <td></td>
            <td class="error">
               <?php echo isset($message['cpass']) ? $message['cpass'] : ''; ?>
            </td>
         </tr>
         <tr>
            <td><label for="mobile">Mobile Number:</label></td>
            <td><input type="text" id="mobile" name="mobile" class="box" placeholder="Enter your mobile number" required></td>
         </tr>
         <tr>
            <td></td>
            <td class="error">
               <?php echo isset($message['mobile']) ? $message['mobile'] : ''; ?>
            </td>
         </tr>
         <tr>
            <td><label for="image">Image:</label></td>
            <td><input type="file" id="image" name="image" class="box" required accept="image/jpg, image/jpeg, image/png"></td>
         </tr>
         <tr>
            <td colspan="2"><input type="submit" value="Register now" class="button" name="submit"></td>
         </tr>
      </table>
      <p>Already have an account? <a href="login.php">Login now</a></p>
   </form>
</section>

</body>
</html>
