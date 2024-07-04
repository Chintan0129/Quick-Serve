<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Check if the delete button is clicked
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->bind_param("i", $delete_id);
    $delete_product->execute();

    if ($delete_product) {
        header('location:admin_homepage.php');
        exit;
    } else {
        // Handle error if deletion fails
        echo "Error: Failed to delete product.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>
   <!-- Custom CSS file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product display -->
   <section class="view__products">
   <h1 class="heading">All Service</h1>
<div class="main__container">
    <?php
    $show_products = $conn->prepare("SELECT * FROM `products`");
    $show_products->execute();
    $result = $show_products->get_result();
    if ($result->num_rows > 0) {
        while ($fetch_products = $result->fetch_assoc()) {
    ?>
            <div class="box">
                
                <img src="../submitted_img/<?= $fetch_products['image']; ?>" alt="">
                <div class="name"><?= $fetch_products['name']; ?></div>
                <div class="cat"><?= $fetch_products['category']; ?></div>
                <div class="cost">Rs:<?= $fetch_products['price']; ?>/-</div>
                <div class="cost">Ranked:<?= $fetch_products['pd_rank']; ?></div>
                <div class="flex-btn">
                    <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="opt-btn">Update</a>
                   
                    <a href="admin_homepage.php?delete=<?= $fetch_products['id']; ?>" class="del-btn" onclick="return confirm('Delete this product?');">Delete</a>
                </div>
            </div>
    <?php
        }
    } else {
        echo '<p class="empty">No products added yet!</p>';
    }
    ?>

</div>
</section>
</html>
