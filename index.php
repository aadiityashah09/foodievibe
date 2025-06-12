<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit(); // Ensure no further code is executed
}

// Include database connection
include('db_connection.php'); // Ensure this file contains database connection logic

// Retrieve user role (admin or customer)
$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_role = ($result->num_rows > 0) ? $result->fetch_assoc()['role'] : 'customer';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Foodie Vibe</title>
    <style>
        /* Import Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

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
    background: transparent;
    border-radius: 18px;
    box-shadow: 0 4px 24px 0 rgba(162,89,230,0.07);
}

.collection-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

@media (max-width: 1024px) {
    .collection-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .collection-grid {
        grid-template-columns: 1fr;
    }
}

.collection-item {
    background-color:transparent;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(162, 89, 230, 0.10);
    transition: 0.4s ease;
    text-align: center;
    position: relative;
    border: 2px solid #e9d6f7;
}

.carousel {
    position: relative;
    width: 100%;
    height: 300px; /* or adjust as needed for your design */
    min-height: 220px;
    background: transparent;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

.carousel img {
    transition: opacity 0.7s ease;
    opacity: 0;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    object-fit: cover; /* Show full image, keep aspect ratio */
    z-index: 1;
    background: transparent !important;
    border-radius: 0;
    box-shadow: none;
}

.carousel img.active {
    opacity: 1;
    z-index: 2;
}
.collection-info {
    padding: 1rem;
    position: relative;
    z-index: 3;
    background: rgba(233, 214, 247, 0.85);
    box-shadow: 0 4px 16px rgba(162,89,230,0.05);
    border-radius: 0 0 16px 16px;
}

.collection-info h3 {
    font-size: 1.4rem;
    font-weight: bold;
    color: #a259e6;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 4;
    text-shadow: 0 2px 8px rgba(162,89,230,0.10);
    letter-spacing: 1px;
}

.collection-info p {
    font-size: 1rem;
    color: #5e548e;
    margin: 0.5rem 0;
}

.collection-info .btn {
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
}

.collection-info .btn:hover {
    background-color: #fff;
    color: #a259e6;
    border: 1px solid #a259e6;
}

header {
    text-align: center;
    background: transparent;
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
    font-weight: bold;
    color: #a259e6;
    text-shadow: 0 2px 12px  #e9d6f7;
}

.nav-menu ul {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.5rem;
    padding: 0;
    margin-bottom: 2rem;
    background: rgba(233, 214, 247, 0.6);
    border-radius: 30px;
}

.nav-menu li a {
    padding: 0.5rem 1.2rem;
    border: 2px solid transparent;
    border-radius: 30px;
    font-weight: 500;
    color: #a259e6;
    background: transparent;
}

.nav-menu li a:hover {
    border-color: #a259e6;
    background-color: #fff;
    color: #a259e6;
}

.featured-collection h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 1.5rem;
    color: #a259e6;
    letter-spacing: 2px;
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

.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #a259e6;
    padding: 10px;
    border-radius: 50%;
    display: inline-block;
    z-index: 999;
    box-shadow: 0 2px 8px 0 rgba(162,89,230,0.15);
}

.back-to-top img {
    width: 24px;
    height: 24px;
}

    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1 class="text-center">Foodie</h1>
            <h2 class="text-center">Vibe</h2>
        </header>

        <nav class="nav-menu">
            <ul>
                <li><a href="beverages.php">Beverages</a></li>
                <li><a href="snacks.php">Snacks</a></li>
                <li><a href="meal.php">Meal</a></li>
                <li><a href="dessert.php">Dessert</a></li>
                <li><a href="account.php">Account</a></li>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="admin_panel.php">Your Recipes</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            
            </ul>
        </nav>
    </div>


    <section class="featured-collection">
        <div class="container">
            <h2>Categories</h2>
<div class="collection-info">
         
                   
    
                        <div class="collection-grid">
    <!-- Beverages Carousel -->
    <div class="collection-item">
        <div class="carousel" data-category="beverages">
            <img src="images/beverages/hot chocolate.jpg" alt="Beverages Collection" loading="lazy" />
            <img src="images/beverages/jamun shots.jpg" alt="Beverages Collection" loading="lazy" style="display:none;" />
            <img src="images/beverages/thandai.webp" alt="Beverages Collection" loading="lazy" style="display:none;" />
            <img src="images/beverages/mint mojito.jpg" alt="Beverages Collection" loading="lazy" style="display:none;" />
        </div>
        <div class="collection-info">
            <h3>Beverages</h3>
            <p>Explore the Beverages Recipes.</p>
            <a href="beverages.php" class="btn">Explore Now</a>
        </div>
    </div>
                <!-- Snacks Carousel -->
                <div class="collection-item">
                    <div class="carousel" data-category="snacks">
                        <img src="images/snacks/veg spring rolls.jpg" alt="Snacks Collection" loading="lazy" />
                        <img src="images/snacks/mac-n-cheese pasta.jpg" alt="Snacks Collection" loading="lazy" style="display:none;" />
                        <img src="images/snacks/cheese burger.jpg" alt="Snacks Collection" loading="lazy" style="display:none;" />
                        <img src="images/snacks/veg manchurian.jpg" alt="Snacks Collection" loading="lazy" style="display:none;" />
                        <img src="images/snacks/grilled veg cheese sandwich.webp" alt="Snacks Collection" loading="lazy" style="display:none;" />
                    </div>
                    <div class="collection-info">
                        <h3>Snacks</h3>
                        <p>Discover the Snacks Recipes.</p>
                        <a href="snacks.php" class="btn">Explore Now</a>
                    </div>
                </div>
                <!-- Meal Carousel -->
                <div class="collection-item">
                    <div class="carousel" data-category="meal">
                        <img src="images/meal/chesse butter masala with naan.jpg" alt="Meal Collection" loading="lazy" />
                        <img src="images/meal/malai kofta.jpg" alt="Meal Collection" loading="lazy" style="display:none;" />
                        <img src="images/meal/paneer angara.jpg" alt="Meal Collection" loading="lazy" style="display:none;" />
                        <img src="images/meal/Jini-dosa.jpg" alt="Meal Collection" loading="lazy" style="display:none;" />
                        <img src="images/meal/chole kulche.jpg" alt="Meal Collection" loading="lazy" style="display:none;" />
                    </div>
                    <div class="collection-info">
                        <h3>Meal</h3>
                        <p>Have a look at Meal Recipes.</p>
                        <a href="meal.php" class="btn">Explore Now</a>
                    </div>
                </div>
                <!-- Dessert Carousel -->
                <div class="collection-item">
                    <div class="carousel" data-category="dessert">
                        <img src="images/dessert/shrikhand.jpg" alt="Dessert Collection" loading="lazy" />
                        <img src="images/dessert/apple kheer.jpg" alt="Dessert Collection" loading="lazy" style="display:none;" />
                        <img src="images/dessert/rasgulla.jpg" alt="Dessert Collection" loading="lazy" style="display:none;" />
                        <img src="images/dessert/rasmalai.jpg" alt="Dessert Collection" loading="lazy" style="display:none;" />
                        <img src="images/dessert/gajar halwa.png" alt="Dessert Collection" loading="lazy" style="display:none;" />
                    </div>
                    <div class="collection-info">
                        <h3>Dessert</h3>
                        <p>Have a look at Dessert Recipes.</p>
                        <a href="dessert.php" class="btn">Explore Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <p>Foodie Vibe offers a curated selection of delicious recipes for beverages, snacks, meals, and
                        desserts. Discover unique culinary creations that blend taste with nutrition.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="beverages.php">Beverages</a></li>
                        <li><a href="snacks.php">Snacks</a></li>
                        <li> <a href="meal.php"> Meal</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <p>supportfoodievibe@gmail.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Foodie Vibe. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Carousel logic for each category with fade effect
        document.querySelectorAll('.carousel').forEach(function(carousel) {
            const images = carousel.querySelectorAll('img');
            let idx = 0;
            // Set initial state
            images.forEach((img, i) => {
                img.classList.remove('active');
                img.style.opacity = 0;
                img.style.display = 'block';
            });
            images[0].classList.add('active');
            images[0].style.opacity = 1;

            setInterval(() => {
                images[idx].classList.remove('active');
                images[idx].style.opacity = 0;
                let nextIdx = (idx + 1) % images.length;
                images[nextIdx].classList.add('active');
                images[nextIdx].style.opacity = 1;
                idx = nextIdx;
            }, 2000); // Change image every 2 seconds
        });
        // JavaScript to show/hide the button based on scroll position
          document.addEventListener('scroll', function () {
            const backToTopButton = document.getElementById('backToTop');
            if (backToTopButton) {
                if (window.scrollY > 300) {
                    backToTopButton.style.display = 'block';
                } else {
                    backToTopButton.style.display = 'none';
                }
            }
        });
    </script>
</body>

</html>
