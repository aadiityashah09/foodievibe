<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "foodievibe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user role
$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_role = ($result->num_rows > 0) ? $result->fetch_assoc()['role'] : 'user';

// Fetch all snacks
$snacks_result = $conn->query("SELECT * FROM snacks ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snacks - Foodie Vibe</title>
    <link rel ="stylesheet" href="universalcss.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Snacks Recipes</h1>
            <p>Explore delicious snack recipes</p>
        </header>

        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="beverages.php">Beverages</a></li>
                <li><a href="meal.php">Meals</a></li>
                <li><a href="dessert.php">Desserts</a></li>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="admin_panel.php">Your Recipes</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <?php if ($user_role === 'admin'): ?>
            <a href="admin_snacks.php" class="btn add-recipe-btn">Add New Snack</a>
        <?php endif; ?>

        <div class="recipe-grid">
            <?php if ($snacks_result && $snacks_result->num_rows > 0): ?>
                <?php while ($snack = $snacks_result->fetch_assoc()): ?>
                    <div class="recipe-card">
                        <img src="<?= htmlspecialchars($snack['image']) ?>" alt="<?= htmlspecialchars($snack['name']) ?>"
                            class="recipe-image">
                        <div class="recipe-info">
                            <h3 class="recipe-title"><?= htmlspecialchars($snack['name']) ?></h3>
                            <a href="view_snacks.php?id=<?= $snack['id'] ?>" class="btn">Explore Recipe</a>
                            <?php if ($user_role === 'admin' && $snack['created_by'] == $user_id): ?>
                                <div class="admin-actions">
                                    <a href="edit_snack.php?id=<?= $snack['id'] ?>" class="btn">Edit</a>
                                    <a href="delete_snack.php?id=<?= $snack['id'] ?>" class="btn"
                                        onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No snack recipes found. Check back later!</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php
$conn->close();
?>