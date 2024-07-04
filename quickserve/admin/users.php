<?php
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}
// delete users
if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];
    $delete_users = $conn->prepare("DELETE FROM `sc_users` WHERE id = ?");
    $delete_users->bind_param("i", $delete_id); 
    $delete_users->execute();
    header('location:users.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Accounts-page</title>
    <link rel="stylesheet" href="./css/admin_users.css">
</head>

<body>
<!-- user accounts -->
    <?php include 'admin_header.php'; ?>

    <section class="users">

        <h1 class="heading">User Accounts</h1>

        <table>
            <thead>
                <tr>
                    <th>Profile Image</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_users = $conn->prepare("SELECT * FROM `sc_users`");
                $select_users->execute();
                $result = $select_users->get_result();
                while ($fetch_users = $result->fetch_assoc()) {
                ?>
                    <tr style="<?php if ($fetch_users['id'] == $admin_id) { echo 'display:none'; }; ?>">
                        <td><img src="../submitted_img/<?= $fetch_users['image']; ?>" alt=""></td>
                        <td><?= $fetch_users['id']; ?></td>
                        <td><?= $fetch_users['name']; ?></td>
                        <td><?= $fetch_users['email']; ?></td>
                        <td><a href="users.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('Delete this user?');" class="del-btn">Delete</a></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

    </section>
</body>

</html>
