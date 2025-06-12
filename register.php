<?php
session_start();
include('db_connection.php');  // Ensure this file contains your database connection logic

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input with basic sanitization
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim(mysqli_real_escape_string($conn, $_POST['password']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $role = 'customer';  // Default role for new users

    // Input validation
    if (empty($username) || empty($password) || empty($email)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if the username already exists
        $check_query = "SELECT * FROM users WHERE username = '$username'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            // Store the plain-text password (not secure)
            $insert_query = "INSERT INTO users (username, password, email, role) VALUES ('$username', '$password', '$email', '$role')";
            if (mysqli_query($conn, $insert_query)) {
                // Redirect to login with a success status
                header("Location: login.php?status=registered");
                exit();
            } else {
                $error = "Error during registration: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FoodieVibe</title>
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
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.3);
            text-align: center;
            color: #333;
            animation: fadeIn 1s ease-in-out;
        }

        .register-container h1 {
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
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h1>Register</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Register" class="btn">
            </div>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>

</html>