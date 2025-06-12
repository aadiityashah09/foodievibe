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

// Fetch all beverages
$beverages_result = $conn->query("SELECT * FROM beverages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beverages - Foodie Vibe</title>
    <style>
          /* --- Replace your <style> section in meal.php with the following --- */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e9d6f7 0%, #d1b3ff 100%);
    color: #5e548e;
}

a {
    color: #a259e6;
    text-decoration: none;
    transition: 0.3s;
}

a:hover {
    color: #fff;
    background: #a259e6;
    border-radius: 20px;
}

.container {
    max-width: 1600px;
    margin: auto;
    padding: 1rem;
    background: rgba(255,255,255,0.7);
    border-radius: 18px;
    box-shadow: 0 4px 24px 0 rgba(162,89,230,0.07);
}

.recipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.recipe-card {
    background-color: rgba(255,255,255,0.85);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(162, 89, 230, 0.10);
    transition: transform 0.3s ease, box-shadow 0.3s;
    text-align: center;
    position: relative;
    border: 2px solid #e9d6f7;
}

.recipe-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 12px 32px rgba(162, 89, 230, 0.18);
}



.recipe-image {
    width: 100%;
    height: 300px;         /* Match the .carousel height in index.php */
    object-fit: cover;     /* Fill the area, cropping if needed, just like index.php */
    background: transparent;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 2px 12px 0 rgba(162,89,230,0.08);
    display: block;
}

.recipe-info {
    padding: 1rem;
    position: relative;
    z-index: 3;
    background: rgba(233, 214, 247, 0.85);
    box-shadow: 0 4px 16px rgba(162,89,230,0.05);
    border-radius: 0 0 16px 16px;
}

.recipe-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: #a259e6;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 4;
    text-shadow: 0 2px 8px rgba(162,89,230,0.10);
    letter-spacing: 1px;
}

.recipe-info p {
    font-size: 1rem;
    color: #5e548e;
    margin: 0.5rem 0;
}

.btn, .add-recipe-btn {
    display: inline-block;
    padding: 10px 24px;
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    background-color: #a259e6;
    text-decoration: none;
    border-radius: 30px;
    transition: background-color 0.3s ease, color 0.3s;
    margin-top: 10px;
    border: none;
    box-shadow: 0 2px 8px 0 rgba(162,89,230,0.10);
    text-align: center;
}

.btn:hover, .add-recipe-btn:hover {
    background-color: #fff;
    color: #a259e6;
    border: 1px solid #a259e6;
}

.add-recipe-btn {
    width: 200px;
    margin: 30px auto;
    display: block;
}

header {
    text-align: center;
    background: transparent;
    padding: 20px 0;
}

header h1,
header h2 {
    display: inline-block;
    margin: 0;
    padding: 0 10px;
    font-family: 'Dancing Script', 'Poppins', sans-serif, cursive;
}

header h1 {
    font-size: 3rem;
    font-weight: bold;
    color: #a259e6;
    text-shadow: 0 2px 12px #e9d6f7;
}

header h2 {
    font-size: 3rem;
    font-weight: normal;
    color: #fff;
    text-shadow: 0 2px 12px #a259e6;
}

nav {
    background: rgba(233, 214, 247, 0.6);
    padding: 15px 0;
    border-radius: 30px;
    margin-bottom: 2rem;
}

nav ul {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.5rem;
    padding: 0;
    margin: 0;
}

nav a {
    color: #a259e6;
    text-decoration: none;
    padding: 0.5rem 1.2rem;
    border: 2px solid transparent;
    border-radius: 30px;
    font-weight: 500;
    background: transparent;
    transition: background-color 0.3s, color 0.3s, border 0.3s;
}

nav a:hover {
    border-color: #a259e6;
    background-color: #fff;
    color: #a259e6;
}

.footer {
    background-color: #e9d6f7;
    padding: 3rem 1rem 2rem;
    margin-top: 3rem;
    border-top: 2px solid #d1b3ff;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 2rem;
}

.footer-section h3 {
    color: #a259e6;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.footer-section p,
.footer-section ul li a {
    color: #5e548e;
}

.footer-bottom {
    text-align: center;
    color: #a59cc3;
    padding-top: 1rem;
    border-top: 1px solid #d1b3ff;
}

.social-icon img {
    width: 24px;
    margin-right: 10px;
    transition: 0.3s;
}

.social-icon:hover img {
    filter: brightness(1.5);
}

@media (max-width: 1024px) {
    .recipe-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .recipe-grid {
        grid-template-columns: 1fr;
    }
}
    
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Beverage Recipes</h1>
            <p>Explore refreshing drink recipes</p>
        </header>

        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="snacks.php">Snacks</a></li>
                <li><a href="meal.php">Meals</a></li>
                <li><a href="dessert.php">Desserts</a></li>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="admin_panel.php">Your Recipes</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <?php if ($user_role === 'admin'): ?>
            <a href="admin_beverages.php" class="btn add-recipe-btn">Add New Beverage</a>
        <?php endif; ?>

        <div class="recipe-grid">
            <?php if ($beverages_result && $beverages_result->num_rows > 0): ?>
                <?php while ($beverage = $beverages_result->fetch_assoc()): ?>
                    <div class="recipe-card">
                        <img src="<?= htmlspecialchars($beverage['image']) ?>" alt="<?= htmlspecialchars($beverage['name']) ?>"
                            class="recipe-image">
                        <div class="recipe-info">
                            <h3 class="recipe-title"><?= htmlspecialchars($beverage['name']) ?></h3>
                            <a href="view_beverages.php?id=<?= $beverage['id'] ?>" class="btn">Explore Recipe</a>
                            <?php if ($user_role === 'admin' && $beverage['created_by'] == $user_id): ?>
                                <div class="admin-actions">
                                    <a href="edit_beverage.php?id=<?= $beverage['id'] ?>" class="btn">Edit</a>
                                    <a href="delete_beverage.php?id=<?= $beverage['id'] ?>" class="btn"
                                        onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No beverage recipes found. Check back later!</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php
$conn->close();
?>