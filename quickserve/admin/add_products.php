<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

$msg = [];
//add product 
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $rank = trim($_POST['rank']);
    $category = trim($_POST['category']);
    $details = trim($_POST['details']);

    // File upload
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_folder = '../submitted_img/' . $image;

    // Check if product name already exists
    $s_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $s_products->bind_param("s", $name);
    $s_products->execute();
    $s_products->store_result();

    if ($s_products->num_rows > 0) {
        $msg[] = 'Product name already exists!';
    } else {
        // Insert new product
        $insert_products = $conn->prepare("INSERT INTO `products`(name,pd_rank, category, about, price, image) VALUES(?,?,?,?,?,?)");
        $insert_products->bind_param("ssssss", $name,$rank, $category, $details, $price, $image);
        $insert_products->execute();

        if ($insert_products) {
            // Check image size and move uploaded file
            if ($image_size > 2000000) {
                $msg[] = 'Image size is too large!';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
                $msg[] = 'New product added!';
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
    <title>Services</title>
    <link rel="stylesheet" href="./css/admin_add_product.css">

</head>

<body>
<?php include 'admin_header.php'; ?>
<?php
   if (isset($msg)) {
    echo '<script>';
    foreach ($msg as $message) {
        echo 'alert("' . $message . '");';
    }
    echo '</script>';
}
?>
<!-- add product page -->
    <section class="add__products">

        <h1 class="heading">Add-New-Service</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <table>
                <tr>
                    <td><label for="name">Product/Service Name:</label></td>
                    <td><input type="text" id="name" name="name" class="con" required placeholder="Enter Product/Service Name"></td>
                </tr>
                <tr>
                    <td><label for="category">Category:</label></td>
                    <td>
                        <select id="category" name="category" class="con" required>
                            <option value="" selected disabled>Select Category</option>
                            <option value="care">Personal Care</option>
                            <option value="cleaning">Cleaning</option>
                            <option value="repairing">Repairing</option>
                            <option value="household">Household</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="price">Price:</label></td>
                    <td><input type="number" id="price" min="0" name="price" class="con" required placeholder="Enter Service Price"></td>
                </tr>
                <tr>
                    <td><label for="rank">Rank:</label></td>
                    <td><input type="number" id="rank" min="0" name="rank" class="con" required placeholder="Enter Service Rank"></td>
                </tr>
                <tr>
                    <td><label for="image">Image:</label></td>
                    <td><input type="file" id="image" name="image" required class="con" accept="image/jpg, image/jpeg, image/png"></td>
                </tr>
                <tr>
                    <td ><label for="details">Details:</label></td>
                    <td ><textarea id="details" name="details" class="con" required placeholder="Enter Product/Service Details" cols="30" rows="10"></textarea></td>
                
                </tr>
                             
            </table>
            <input type="submit" class="button" value="Add Product" name="add_product">
        </form>

    </section>

</body>

</html>
