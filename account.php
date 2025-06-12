<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "foodievibe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: {$conn->connect_error}");
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT id, username, email, role, created_at FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
} else {
    die("User not found.");
}

// Get user's contributed recipes from all categories
$recipes = [];

// Get beverages
$beverages_query = "SELECT id, name, image, created_at FROM beverages WHERE created_by = ? ORDER BY created_at DESC";
$beverages_stmt = $conn->prepare($beverages_query);
$beverages_stmt->bind_param("i", $user_id);
$beverages_stmt->execute();
$beverages_result = $beverages_stmt->get_result();
while ($row = $beverages_result->fetch_assoc()) {
    $row['type'] = 'beverage';
    $recipes[] = $row;
}

// Get desserts
$desserts_query = "SELECT id, name, image, created_at FROM desserts WHERE created_by = ? ORDER BY created_at DESC";
$desserts_stmt = $conn->prepare($desserts_query);
$desserts_stmt->bind_param("i", $user_id);
$desserts_stmt->execute();
$desserts_result = $desserts_stmt->get_result();
while ($row = $desserts_result->fetch_assoc()) {
    $row['type'] = 'dessert';
    $recipes[] = $row;
}

// Get meals
$meals_query = "SELECT id, name, image, created_at FROM meals WHERE created_by = ? ORDER BY created_at DESC";
$meals_stmt = $conn->prepare($meals_query);
$meals_stmt->bind_param("i", $user_id);
$meals_stmt->execute();
$meals_result = $meals_stmt->get_result();
while ($row = $meals_result->fetch_assoc()) {
    $row['type'] = 'meal';
    $recipes[] = $row;
}

// Get snacks
$snacks_query = "SELECT id, name, image, created_at FROM snacks WHERE created_by = ? ORDER BY created_at DESC";
$snacks_stmt = $conn->prepare($snacks_query);
$snacks_stmt->bind_param("i", $user_id);
$snacks_stmt->execute();
$snacks_result = $snacks_stmt->get_result();
while ($row = $snacks_result->fetch_assoc()) {
    $row['type'] = 'snack';
    $recipes[] = $row;
}

// Sort all recipes by creation date
usort($recipes, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Foodie Vibe</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .account-header {
            background-color: #1c1f4a;
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 10px;
        }

        .user-info {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .user-info p {
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .user-info strong {
            color: #1c1f4a;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background-color: #ff4757;
        }

        .recipes-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .recipes-container h2 {
            color: #1c1f4a;
            border-bottom: 2px solid #ff6b6b;
            padding-bottom: 10px;
            margin-top: 0;
        }

        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .recipe-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
        }

        .recipe-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .recipe-info {
            padding: 15px;
        }

        .recipe-title {
            font-size: 1.2rem;
            margin: 0 0 5px 0;
            color: #333;
        }

        .recipe-meta {
            font-size: 0.9rem;
            color: #666;
            margin: 5px 0;
        }

        .recipe-type {
            display: inline-block;
            padding: 3px 8px;
            background-color: #1c1f4a;
            color: white;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .no-recipes {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            background-color: #4CAF50;
        }

        .btn-edit:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #f44336;
        }

        .btn-delete:hover {
            background-color: #d32f2f;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="account-header">
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p>Your Foodie Vibe Account Dashboard</p>
        </div>

        <div class="user-info">
            <h2>Account Information</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Account Type:</strong> <?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            <a href="logout.php" class="btn">Logout</a>
        </div>

        <div class="recipes-container">
            <h2>Your Contributed Recipes</h2>
            <?php if (!empty($recipes)): ?>
                <div class="recipe-grid">
                    <?php foreach ($recipes as $recipe): ?>
                        <div class="recipe-card">
                            <img src="<?php echo htmlspecialchars($recipe['image']); ?>"
                                alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
                            <div class="recipe-info">
                                <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['name']); ?></h3>
                                <p class="recipe-meta">
                                    <span class="recipe-type"><?php echo ucfirst(htmlspecialchars($recipe['type'])); ?></span>
                                    <br>
                                    Added on <?php echo date('M j, Y', strtotime($recipe['created_at'])); ?>
                                </p>
                                <div class="action-buttons">
                                    <a href="edit_<?php echo $recipe['type']; ?>.php?id=<?php echo $recipe['id']; ?>"
                                        class="btn btn-edit">Edit</a>
                                    <a href="delete_<?php echo $recipe['type']; ?>.php?id=<?php echo $recipe['id']; ?>"
                                        class="btn btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-recipes">
                    <p>You haven't contributed any recipes yet.</p>
                    <?php if ($user['role'] === 'admin'): ?>
                        <p>Start by adding a new recipe from one of our categories!</p>
                        <a href="index.php" class="btn">Browse Categories</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php
$conn->close();
?>