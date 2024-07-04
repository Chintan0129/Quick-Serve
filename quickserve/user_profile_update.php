<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit; 
}
if(isset($_POST['delete_profile'])){
   // Delete profile from the database
   $delete_profile = $conn->prepare("DELETE FROM `sc_users` WHERE id = ?");
   $delete_profile->bind_param("i", $user_id);
   $delete_profile->execute();

   // Destroy session and redirect to login page
   session_destroy();
   header('location: login.php');
   exit;
}
// update logic
if(isset($_POST['update_profile'])){

   $name = isset($_POST['name']) ? trim($_POST['name']) : '';
   $email = isset($_POST['email']) ? trim($_POST['email']) : '';
   $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';

   $update_profile = $conn->prepare("UPDATE `sc_users` SET name = ?, email = ?,mobile= ? WHERE id = ?");
   $update_profile->bind_param("sssi", $name, $email,$mobile, $user_id);
   $update_profile->execute();

   $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
   $image_tmp_name = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';
   $old_image = isset($_POST['old_image']) ? $_POST['old_image'] : '';

   if(!empty($image)){
      $image_size = $_FILES['image']['size'];
      $image_folder = './submitted_img/'.$image;

      if($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `sc_users` SET image = ? WHERE id = ?");
         $update_image->bind_param("si", $image, $user_id);
         $update_image->execute();

         if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('./submitted_img/'.$old_image);
            $message[] = 'image updated successfully!';
         }
      }
   }

   $new_pass = isset($_POST['new_pass']) ? trim($_POST['new_pass']) : '';
   $confirm_pass = isset($_POST['confirm_pass']) ? trim($_POST['confirm_pass']) : '';

   if(!empty($new_pass) && !empty($confirm_pass)){
      if($new_pass != $confirm_pass){
         $message[] = 'confirm password not matched!';
      } else {
         // Hash the new password
         $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
         // Update the password
         $update_pass_query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
         $update_pass_query->bind_param("si", $hashed_password, $user_id);
         $update_pass_query->execute();
         $message[] = 'password and Profile updated successfully!';
      }
   } else {
      $message[] = 'Profile updated Successfully';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update User Profile</title>

   <script src="https://kit.fontawesome.com/8a540d2ee7.js" crossorigin="anonymous"></script>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="./css/update_profile.css">

</head>
<body>
   
<?php include 'header.php'; ?>
<!-- update profile of user -->
<section class="update-profile">

   <h1 class="title">Update Profile</h1>
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
   

   <form action="" method="POST" enctype="multipart/form-data">
      <table>
         <tr>
            <td>Username:</td>
            <td><input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="Update username" required class="box"></td>
         </tr>
         <tr>
            <td>Email:</td>
            <td><input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="Update email" required class="box"></td>
         </tr>
         <tr>
            <td>Profile Picture:</td>
            <td>
               <img src="submitted_img/<?= $fetch_profile['image']; ?>" alt="Profile Picture" style="max-width: 100px; max-height: 100px;"><br>
               <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
               <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
            </td>
         </tr>
         <tr>
            <td>Mobile:</td>
            <td><input type="text"  name="mobile" class="box" value="<?= $fetch_profile['mobile']; ?>"  placeholder="update mobile number" required></td>
         </tr>
         <tr>
            <td>New Password:</td>
            <td><input type="password" name="new_pass" placeholder="Enter new password" class="box"></td>
         </tr>
         <tr>
            <td>Confirm Password:</td>
            <td><input type="password" name="confirm_pass" placeholder="Confirm new password" class="box"></td>
         </tr>
         <tr>
            <td colspan="2">
               <input type="submit" class="button" value="Update Profile" name="update_profile">
               <input type="submit" class="del-btn" value="Delete Profile" name="delete_profile">
               <a href="home.php" class="opt-btn">Go Back</a>
            </td>
         </tr>
      </table>
   </form>

</section>
</body>
</html>
