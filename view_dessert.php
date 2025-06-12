<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "foodievibe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dessert_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM desserts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $dessert_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Recipe not found.";
    exit();
}

$dessert = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($dessert['name']) ?> - Foodie Vibe</title>
     <style>
body {
    font-family: 'Georgia', serif;
    background: #fdfdfb;
    margin: 0;
    padding: 0;
    color: #333;
}

.container {
    max-width: 800px;
    margin: 40px auto;
    background: rgba(255, 255, 255, 0.99);
    border: 1px solid rgb(0, 0, 0);
    box-sizing: border-box;
    border-radius: 8px;
    padding: 48px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

h1 {
    font-size: 2.4rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-transform: uppercase;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
    color: #222;
    letter-spacing: 1px;
}

h2 {
    font-size: 1.4rem;
    font-weight: 600;
    color: #444;
    margin-top: 32px;
    margin-bottom: 16px;
    text-transform: uppercase;
    border-bottom: 1px solid #eee;
    padding-bottom: 6px;
}

.image-container {
    width: 100%;
    margin: 0 auto 32px auto;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.image-container img {
    width: 100%;
    height: 600px; /* Fixed height for consistency */
    display: inline-block;
    object-fit: cover;
}
p, li {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #555;
}

ul, ol {
    padding-left: 20px;
    margin-bottom: 24px;
}

/* Styles for side-by-side ingredients and instructions */
.details-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.section {
    flex: 1;
    min-width: 300px;
}
.btn-back {
            display: inline-block;
            margin-top: 20px;
            background-color: rgb(142, 0, 224);
            font-size: large;
            /* Sky blue for button */
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-back:hover {
            background-color:rgb(201, 106, 245);
            /* Yellowish hover effect */
            color: #333;
            /* Dark text on hover */
            transform: translateY(-3px);
            /* Slight lift effect */}

.section h3 {
    margin-bottom: 10px;
    font-size: 1.2rem;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 800px) {
    .container {
        max-width: 98vw;
        padding: 16px 4vw;
    }
    .image-container {
        width: 100%;
        max-width: 340px;
        margin: 0 auto 24px auto;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 12px 0 rgba(162,89,230,0.08);
        background: #f6f2ff;
        float: none;
        display: block;
    }
          
        }
    

</style>
</head>

<body>

   <body>
    <div class="container">
    <h1><?= htmlspecialchars($dessert['name']) ?></h1>
    <div class="image-container">
        <img src="<?= htmlspecialchars($dessert['image']) ?>" alt="<?= htmlspecialchars($dessert['name']) ?>">
    </div>
    <h2>Details</h2>
    <div class="details-container">
        <div class="section ingredients-section">
            <h3>Ingredients</h3>
            <p><?= nl2br(htmlspecialchars($dessert['ingredients'])) ?></p>
        </div>
        <div class="section instructions-section">
            <h3>Instructions</h3>
            <p><?= nl2br(htmlspecialchars($dessert['instructions'])) ?></p>
        </div>
    </div>
    <a href="dessert.php" class="btn-back">← Back to Desserts</a>
</body>

</html>
<?php
$conn->close();
?>