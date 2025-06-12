<?php
session_start();
include 'includes/admin_header.php';
include 'includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM users");

?>

<h1>Manage Users</h1>
<table>
    <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    <?php while ($user = mysqli_fetch_assoc($users)) { ?>
        <tr>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['user_id']; ?>">Edit</a>
                <a href="delete_user.php?id=<?php echo $user['user_id']; ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<?php include 'includes/admin_footer.php'; ?>
