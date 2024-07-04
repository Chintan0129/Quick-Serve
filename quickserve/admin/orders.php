<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Update order payment status
if (isset($_POST['update_order'])) {
    $order_id = trim($_POST['order_id']);
    $update_payment = isset($_POST['update_payment']) ? trim($_POST['update_payment']) : ''; // Check if key exists
    $delivery_agent = trim($_POST['delivery_agent']);

    // Check if the update_payment is empty, if so, retain the existing payment status
    if (empty($update_payment)) {
        // Retrieve existing payment status
        $existing_payment_query = $conn->prepare("SELECT payment_status FROM `orders` WHERE id = ?");
        $existing_payment_query->bind_param("i", $order_id);
        $existing_payment_query->execute();
        $existing_payment_result = $existing_payment_query->get_result();
        $existing_payment_row = $existing_payment_result->fetch_assoc();
        $update_payment = $existing_payment_row['payment_status'];
    }

    $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ?, delivery_agent = ? WHERE id = ?");
    $update_orders->bind_param("ssi", $update_payment, $delivery_agent, $order_id);
    $update_orders->execute();
    $msg[] = 'Order details updated!';
}

// Delete order
if (isset($_GET['delete'])) {
    $delete_id = trim($_GET['delete']);
    $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    $delete_orders->bind_param("i", $delete_id);
    $delete_orders->execute();
    header('location:orders.php');
    exit;
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="./css/admin_orders.css">
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
<section class="orders">
    <h1 class="heading">Orders</h1>
    <?php
    $total_pendings = 0;
    $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
    $status = 'pending';
    $select_pendings->bind_param("s", $status);
    $select_pendings->execute();
    $result_pendings = $select_pendings->get_result();
    while ($fetch_pendings = $result_pendings->fetch_assoc()) {
        $total_pendings += $fetch_pendings['total_price'];
    }
    ?>
    <h3>Total Pendings: Rs:<?= $total_pendings; ?></h3>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>User ID</th>
                <th>Placed On</th>
                <th>Name</th>
                <th>Email</th>
                <th>Service Name</th>
                <th>Service Id</th>
                <th>Number</th>
                <th>Address</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>status</th>
                <th>Service Agent</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            $result = $select_orders->get_result();
            if ($result->num_rows > 0) {
                while ($fetch_orders = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <form action="" method="POST">
                            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                            <td><?= $fetch_orders['user_id']; ?></td>
                            <td><?= $fetch_orders['placed_on']; ?></td>
                            <td><?= $fetch_orders['name']; ?></td>
                            <td><?= $fetch_orders['email']; ?></td>
                            <td><?= $fetch_orders['product_name']; ?></td>
                            <td><?= $fetch_orders['product_id']; ?></td>
                            <td><?= $fetch_orders['number']; ?></td>
                            <td><?= $fetch_orders['address']; ?></td>
                            <td>Rs:<?= $fetch_orders['total_price']; ?></td>
                            <td><?= $fetch_orders['method']; ?></td>
                            <td>
                                <select name="update_payment">
                                    <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </td>
                            <td><input type="text" name="delivery_agent" placeholder="<?= $fetch_orders['delivery_agent'];?>"></td>
                            <td>
                                <div class="buttons">
                                    <input type="submit" name="update_order" class="opt-btn" value="Update">
                                    <a href="orders.php?delete=<?= $fetch_orders['id']; ?>"
                                       class="del-btn"
                                       onclick="return confirm('Delete this order?');">Delete</a>
                                </div>
                            </td>
                        </form>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="10" class="empty">No orders placed yet!</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</section>
</body>
</html>
