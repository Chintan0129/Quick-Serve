<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}
// update product logic
if (isset($_POST['update_product'])) {

    $pid = trim($_POST['pid']);
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $rank = trim($_POST['rank']);
    $category = trim($_POST['category']);
    $details = trim($_POST['details']);
    $image = trim($_FILES['image']['name']);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $old_image = trim($_POST['old_image']);

    $image_folder = '../submitted_img/' . $image;

    $update_product = $conn->prepare("UPDATE `products` SET name = ?,pd_rank = ? , category = ?, about = ?, price = ? WHERE id = ?");
    $update_product->bind_param("sssssi", $name,$rank, $category, $details, $price, $pid);
    $update_product->execute();

    $msg[] = 'Product updated successfully!';

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $msg[] = 'Image size is too large!';
        } else {
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $update_image->bind_param("si", $image, $pid);
            $update_image->execute();

            if ($update_image) {
                move_uploaded_file($image_tmp_name, $image_folder);
                unlink('../submitted_img/' . $old_image);
                $msg[] = 'Image updated successfully!';
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
    <title>update products</title>
    <link rel="stylesheet" href="./css/update_product.css">

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
<!-- update product page -->
    <section class="update">

        <h1 class="heading">update-product</h1>

        <?php
        $update_id = $_GET['update'];
        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $select_products->bind_param("i", $update_id);
        $select_products->execute();
        $result = $select_products->get_result();
        if ($result->num_rows > 0) {
            while ($fetch_products = $result->fetch_assoc()) {
        ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
                    <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                    <img src="../submitted_img/<?= $fetch_products['image']; ?>" alt="">
                    <input type="text" name="name" placeholder="enter product name" required class="input" value="<?= $fetch_products['name']; ?>">
                    <input type="number" name="rank" placeholder="enter product Rank" required class="input" value="<?= $fetch_products['pd_rank']; ?>">
                    <input type="number" name="price" min="0" placeholder="enter product price" required class="input" value="<?= $fetch_products['price']; ?>">
                    <select name="category" class="input" required>
                        <option selected><?= $fetch_products['category']; ?></option>
                        <option value="mi">MI</option>
                        <option value="apple">Apple</option>
                        <option value="samsung">Samsung</option>
                        <option value="oneplus">Oneplus</option>
                    </select>
                    <textarea name="details" required placeholder="enter product details" class="input" cols="10" rows="3"><?= $fetch_products['about']; ?></textarea>
                    <input type="file" name="image" class="input" accept="image/jpg, image/jpeg, image/png">
                    <div class="button">
                        <input type="submit" class="up_btn" value="update product" name="update_product">
                        <a href="admin_homepage.php" class="opbtn">go back</a>
                    </div>
                </form>
        <?php
            }
        } else {
            echo '<p class="empty">no products found!</p>';
        }
        ?>

    </section>
</body>

</html>