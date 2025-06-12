<?php
session_start();

// Check if customer is logged in
if (!isset($_SESSION['customer_logged_in']) || !isset($_SESSION['customer_id'])) {
    echo "Error: customer_id not found in session. Please log in again.";
    exit();
}

// Get customer ID from session
$customer_id = $_SESSION['customer_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vibevintage";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch customer details
$sql = "SELECT * FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer_result = $stmt->get_result();
if ($customer_result->num_rows > 0) {
    $customer = $customer_result->fetch_assoc();
} else {
    echo "No customer found.";
    exit();
}

// Fetch customer orders
$order_sql = "SELECT * FROM orders WHERE customer_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $customer_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

// Close the database connection
$stmt->close();
$order_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($customer['username']); ?>!</h1>

    <h3>Your Information</h3>
    <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']); ?></p>

    <h3>Your Orders</h3>
    <?php if ($order_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
            <?php while ($order = $order_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']); ?></td>
                    <td><?= htmlspecialchars($order['order_date']); ?></td>
                    <td><?= htmlspecialchars($order['status']); ?></td>
                    <td>₹<?= htmlspecialchars($order['total']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
    <nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="logout.php">Logout</a></li> <!-- Logout link -->
    </ul>
</nav>

</body>
</html>
