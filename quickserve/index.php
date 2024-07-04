<?php
@include 'config.php';
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$message = [];

function sanitizeInput($data)
{
    return trim($data);
}

if (isset($_POST['add_to_cart'])) {
    // Sanitize input data
    $pid = isset($_POST['pid']) ? sanitizeInput($_POST['pid']) : '';
    $p_name = isset($_POST['p_name']) ? sanitizeInput($_POST['p_name']) : '';
    $p_price = isset($_POST['p_price']) ? sanitizeInput($_POST['p_price']) : '';
    $p_image = isset($_POST['p_image']) ? sanitizeInput($_POST['p_image']) : '';
    $p_qty = isset($_POST['p_qty']) ? sanitizeInput($_POST['p_qty']) : '';

    // Handle cart items using cookies
    $cart_item = array(
        'pid' => $pid,
        'p_name' => $p_name,
        'p_price' => $p_price,
        'p_image' => $p_image,
        'p_qty' => $p_qty
    );

    // Initialize a flag to check if the item already exists in the cart
    $item_exists = false;

    // Check if the cart cookie exists and if the item already exists in the cart
    if (isset($_COOKIE['cart'])) {
        // Get existing cart items from cookie
        $cart = json_decode($_COOKIE['cart'], true);

        // Check if the item already exists in the cart
        foreach ($cart as $item) {
            if ($item['pid'] == $pid) {
                // Set the flag to true if the item already exists
                $item_exists = true;
                break;
            }
        }

        // If the item exists, show a message and do not add it again to the cart
        if ($item_exists) {
            $message[] = 'Item already in cart!';
        } else {
            // Append new item to cart array
            $cart[] = $cart_item;
            $message[] = 'Item added to cart!';
        }
    } else {
        // Create new cart array with the current item
        $cart = array($cart_item);
        $message[] = 'Item added to cart!';
    }

    // Store the updated cart array in a cookie if the item was not already in the cart
    if (!$item_exists) {
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePage</title>
    <!-- custom css file link  -->
    <link rel="stylesheet" href="./css/index.css">
    <script src="https://kit.fontawesome.com/8a540d2ee7.js" crossorigin="anonymous"></script>

</head>

<body>
    <!-- header -->
    <header class="main__container">

        <div class="header-flex">
            <nav class="navbar">
                <a href="home.php">QuickServe</a>
            </nav>

            <div class="links">
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </div>


        </div>

    </header>
    <?php
    // to show messages
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
  <div class="message">
	 <span>' . $message . '</span>
	 <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
  </div>
  ';
        }
    }

    ?>
    <!-- slideshow -->
    <div class="slideshow-container">

        <div class="mySlides fade">

            <img src="./image/home-bg.jpg" style="width:100%">
        </div>

        <div class="mySlides fade">

            <img src="./image/salloon.jpg" style="width:100%">
        </div>
        <div class="mySlides fade">

            <img src="./image/ac.jpg" style="width:100%">
        </div>

        <a class="prev" onclick="plusSlides(-1)">❮</a>
        <a class="next" onclick="plusSlides(1)">❯</a>

    </div>
    <br>

    <div style="text-align:center">
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>
    <!-- scrolling text -->
    <div class="scrolling-text-container">
        <div class="scrolling-text-inner" style="--marquee-speed: 10s; --direction:scroll-right" role="marquee">
            <div class="scrolling-text">
                <div class="scrolling-text-item">Summer Discount On Ac service</div>
                <div class="scrolling-text-item">25% off on Saloon Service </div>
                <div class="scrolling-text-item">Sale On Utility Service !!!</div>
                <div class="scrolling-text-item">Deals On refurbishment of home with 40%off</div>
                <div class="scrolling-text-item">Sale Sale Sale !!!</div>
                <div class="scrolling-text-item">Buy yearly membership for solar cleaning and get 45% off!</div>
            </div>
        </div>
    </div>
   
    <!-- category -->
    <section class="category-section">

        <h1 class="category-title">Book By Services Category</h1>

        <div class="category-container">

            <div class="category-item">
                <img src="./image/cleaning-service.jpeg" alt="cleaning Logo" class="category-logo">
                <h3>Cleaning</h3>
                <a href="product_category.php?category=cleaning" class="category-link">Book Service</a>
            </div>

            <div class="category-item">
                <img src="./image/repair-maintenance.png" alt="repairing Logo" class="category-logo">
                <h3>Repairing</h3>
                <a href="product_category.php?category=repairing" class="category-link">Book Service</a>
            </div>

            <div class="category-item">
                <img src="./image/salloon.jpg" alt="Logo" class="category-logo">
                <h3>Personal Care</h3>
                <a href="product_category.php?category=care" class="category-link">Book Service</a>
            </div>

            <div class="category-item">
                <img src="./image/paintin.jpg" alt="Logo" class="category-logo">
                <h3>Household Service</h3>
                <a href="product_category.php?category=household" class="category-link">Book Service</a>
            </div>

        </div>

    </section>
    <!-- product  -->

    <section class="products">

        <h1 class="title">All SERVICES</h1>

        <div class="container">

            <?php
            $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY pd_rank ASC");
            $select_products->execute();
            $result = $select_products->get_result();
            if ($result->num_rows > 0) {
                while ($fetch_products = $result->fetch_assoc()) {
            ?>

                    <form action="" class="box" method="POST">

                        <img src="submitted_img/<?= $fetch_products['image']; ?>" alt="">
                        <div class="name"><?= $fetch_products['name']; ?></div>
                        <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                        <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
                        <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
                        <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
                        <div class="price">Rs:<span><?= $fetch_products['price']; ?></span>/-</div>
                        <input type="number" min="1" value="1" name="p_qty" class="qty">
                        <input type="submit" value="add to cart" class="button" name="add_to_cart">
                        <a href="product_view.php?pid=<?= $fetch_products['id']; ?>" class="button">View Item</a>
                    </form>
            <?php
                }
            } else {
                echo '<p class="empty">no services added yet!</p>';
            }
            ?>

        </div>

    </section>
    <script src="./slider.js"></script>

</body>

</html>