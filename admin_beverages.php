<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to FoodieVibe DB
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "foodievibe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add new beverage
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_product"])) {
    $name = $conn->real_escape_string($_POST["product_name"]);
    $ingredients = $conn->real_escape_string($_POST["product_ingredients"]);
    $instructions = $conn->real_escape_string($_POST["product_instructions"]);

    if (isset($_FILES['product_image'])) {
        $image_name = $_FILES['product_image']['name'];
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $upload_path = "uploads/" . basename($image_name);

        if (move_uploaded_file($image_tmp, $upload_path)) {
            $sql = "INSERT INTO beverages (name, ingredients, instructions, image) 
                    VALUES ('$name', '$ingredients', '$instructions', '$upload_path')";
            $conn->query($sql);
        }
    }
}

// Update beverage
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
    $id = $conn->real_escape_string($_POST["update_product_id"]);
    $name = $conn->real_escape_string($_POST["update_product_name"]);
    $ingredients = $conn->real_escape_string($_POST["update_product_ingredients"]);
    $instructions = $conn->real_escape_string($_POST["update_product_instructions"]);

    $sql = "UPDATE beverages SET 
                name='$name', 
                ingredients='$ingredients', 
                instructions='$instructions' 
            WHERE id=$id";
    $conn->query($sql);
}

// Delete beverage
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_product"])) {
    $id = $_POST["product_id"];
    $result = $conn->query("SELECT image FROM beverages WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        if (file_exists($row['image'])) {
            unlink($row['image']);
        }
    }
    $conn->query("DELETE FROM beverages WHERE id = $id");
}

// Fetch all beverages
$result = $conn->query("SELECT * FROM beverages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Beverages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        header {
            text-align: center;
            padding: 1rem;
            background: #2f3542;
            color: white;
        }

        form.admin-form input,
        form.admin-form textarea {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form.admin-form button {
            background: #ff6b6b;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 30px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 1rem;
            text-align: left;
        }

        img {
            max-width: 60px;
        }

        .modal {
            display: none;
            position: fixed;
            background: white;
            padding: 20px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 999;
            border: 1px solid #ccc;
        }
    </style>
    <script>
        function showUpdateModal(id, name, ingredients, instructions) {
            document.getElementById('update_product_id').value = id;
            document.getElementById('update_product_name').value = name;
            document.getElementById('update_product_ingredients').value = ingredients;
            document.getElementById('update_product_instructions').value = instructions;
            document.getElementById('updateModal').style.display = 'block';
        }
        function closeUpdateModal() {
            document.getElementById('updateModal').style.display = 'none';
        }
    </script>
</head>

<body>
    <header>
        <h1>Admin Panel - Beverages</h1>
    </header>

    <section class="admin-section">
        <h2>Add New Beverage</h2>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="text" name="product_name" placeholder="Beverage Name" required>
            <textarea name="product_ingredients" placeholder="Ingredients" required></textarea>
            <textarea name="product_instructions" placeholder="Instructions" required></textarea>
            <input type="file" name="product_image" required>
            <button type="submit" name="add_product">Add Beverage</button>
        </form>

        <h2>Manage Beverages</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Ingredients</th>
                    <th>Instructions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row["id"] ?></td>
                            <td><img src="<?= htmlspecialchars($row["image"]) ?>" alt="Image"></td>
                            <td><?= htmlspecialchars($row["name"]) ?></td>
                            <td><?= htmlspecialchars($row["ingredients"]) ?></td>
                            <td><?= htmlspecialchars($row["instructions"]) ?></td>
                            <td>
                                <button onclick="showUpdateModal(
                                    <?= $row['id'] ?>,
                                    `<?= htmlspecialchars(addslashes($row['name'])) ?>`,
                                    `<?= htmlspecialchars(addslashes($row['ingredients'])) ?>`,
                                    `<?= htmlspecialchars(addslashes($row['instructions'])) ?>`
                                )">Update</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $row["id"] ?>">
                                    <button type="submit" name="delete_product"
                                        onclick="return confirm('Are you sure you want to delete this beverage?');">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No beverages found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Update Modal -->
    <div id="updateModal" class="modal">
        <h2>Update Beverage</h2>
        <form method="POST" class="admin-form">
            <input type="hidden" name="update_product_id" id="update_product_id">
            <input type="text" name="update_product_name" id="update_product_name" placeholder="Beverage Name" required>
            <textarea name="update_product_ingredients" id="update_product_ingredients" placeholder="Ingredients"
                required></textarea>
            <textarea name="update_product_instructions" id="update_product_instructions" placeholder="Instructions"
                required></textarea>
            <button type="submit" name="update_product">Update Beverage</button>
            <button type="button" onclick="closeUpdateModal()">Cancel</button>
        </form>
    </div>

    <footer style="text-align:center; margin-top:2rem;">
        <p>&copy; 2025 FoodieVibe. Admin Panel.</p>
    </footer>
</body>

</html>