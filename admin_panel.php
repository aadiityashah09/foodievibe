<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and check if admin is logged in

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "foodievibe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define food categories and their labels
$categories = [
    'meals' => 'Meals',
    'snacks' => 'Snacks',
    'desserts' => 'Desserts',
    'beverages' => 'Beverages'
];

// Fetch product counts for each category
$counts = [];
foreach ($categories as $table => $label) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $row = $result ? $result->fetch_assoc() : ['total' => 0];
    $counts[$table] = $row['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - FoodieVibe</title>
   <link rel="stylesheet" href = "adminpanelcss.css"> 
</head>

<body>
    <header>
        <h1>Admin Dashboard - FoodieVibe</h1>
    </header>
    <div class="container">
        <nav class="nav">
            <a href="admin_meals.php">Manage Meals</a>
            <a href="admin_snacks.php">Manage Snacks</a>
            <a href="admin_desserts.php">Manage Desserts</a>
            <a href="admin_beverages.php">Manage Beverages</a>
            <a href="logout.php">Logout</a>
        </nav>

        <section class="dashboard">
            <h2>Welcome, Admin!</h2>
            <div class="summary">
                <?php foreach ($categories as $table => $label): ?>
                    <div class="summary-item">
                        <h3><?= htmlspecialchars($label) ?></h3>
                        <p>Total Items: <?= $counts[$table] ?></p>
                        <a href="admin_<?= $table ?>.php">View <?= $label ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <footer>
        <p>&copy; 2025 FoodieVibe. All Rights Reserved.</p>
    </footer>
</body>

</html>