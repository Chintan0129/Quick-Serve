
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
<header class="main__container">
    <div class="header-flex">
        <a href="home.php" class="logo">QuickServe</a>
        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="user_profile_update.php">User-Profile</a>
            <a href="orders.php">Past-Orders</a>
         <a href="job.php">Openings</a>
            <a href="logout.php">Logout</a>
        </nav>
        <div class="links">
            <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->bind_param("i", $user_id);
            $count_cart_items->execute();
            $count_cart_items_result = $count_cart_items->get_result();
            $num_cart_items = $count_cart_items_result->num_rows;
            ?>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= htmlspecialchars($num_cart_items); ?>)</span></a>
        </div>
    </div>
</header>
</body>
</html>
<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>