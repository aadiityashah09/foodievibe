<?php
session_start();
include 'db_connection.php'; // Ensure db_connection.php is correct

$success = "";
$error = "";

if (isset($_GET['status']) && $_GET['status'] === 'loggedin') {
    $success = "You have successfully logged in.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $query = "SELECT id, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Recommended: Use password_verify if password is hashed
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: login.php?status=loggedin");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodieVibe</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: url('./images/Login&Register.jpeg') no-repeat center center fixed;
            /* Ensure the path is correct */
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(243, 239, 239, 0.14);
            /* Adjust transparency here */
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(16, 17, 31, 0.98);
            text-align: center;
            color: #333;
            animation: fadeIn 1s ease-in-out;
        }

        .login-container h1 {
            margin-bottom: 30px;
            font-size: 2rem;
            color: rgb(230, 231, 236);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background-color: #ffffff;
            /* Set background color to white */
            color: #333;
            /* Set text color to dark gray for readability */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for better visibility */
            font-size: 1rem;
        }


        input:focus {
            outline: none;
            box-shadow: 0 0 0 2px #ff6b6b;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn:hover {
            background: linear-gradient(135deg, #ff4757, #ff6b6b);
        }

        .error {
            color: #e74c3c;
            background-color: #fcebea;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            border: 3px solid #1c1f4a;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.25);
            z-index: 1000;
        }

        .popup.active {
            display: block;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .popup {
                width: 90%;
            }
        }
    </style>
</head>

<body>

    <!-- Overlay and Success Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="successPopup">
        <p><?php echo htmlspecialchars($success); ?></p>
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <h1>Login</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>

    <script>
        // Show popup if there's a success message
        window.onload = function () {
            const successMessage = "<?php echo $success; ?>";
            if (successMessage) {
                document.getElementById('successPopup').classList.add('active');
                document.getElementById('overlay').classList.add('active');
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 3000);
            }
        };
    </script>
</body>

</html>