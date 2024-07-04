<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit; // Ensure script termination after redirection
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>orders</title>

    <script src="https://kit.fontawesome.com/8a540d2ee7.js" crossorigin="anonymous"></script>

    <!-- custom css file link for this page -->
    <link rel="stylesheet" href="./css/order_dispaly.css">

</head>

<body>
<!-- order placed -->
    <?php include 'header.php'; ?>

    <section class="placed-orders">

        <h1 class="title">Placed Orders</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Placed On</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Payment Method</th>
                        <th>Service name</th>
                        <th>Service id</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                        <th>Service Agent</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
                    $select_orders->bind_param("i", $user_id);
                    $select_orders->execute();
                    $result = $select_orders->get_result();
                    if ($result->num_rows > 0) {
                        while ($fetch_orders = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?= $fetch_orders['placed_on']; ?></td>
                                <td><?= $fetch_orders['name']; ?></td>
                                <td><?= $fetch_orders['number']; ?></td>
                                <td><?= $fetch_orders['email']; ?></td>
                                <td><?= $fetch_orders['address']; ?></td>
                                <td><?= $fetch_orders['method']; ?></td>
                                <td><?= $fetch_orders['product_name']; ?></td>
                                <td><?= $fetch_orders['product_id']; ?></td>
                                <td>Rs:<?= $fetch_orders['total_price']; ?></td>
                                <td style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'red' : 'green'; ?>"><?= $fetch_orders['payment_status']; ?></td>
                                <td><?= $fetch_orders['delivery_agent']; ?></td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="9" class="empty">No orders placed yet!</td></tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>

    </section>
</body>

</html>
