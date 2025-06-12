<?php
// Start the session
session_start();

// Destroy the session to log the user out
session_unset();   // Removes all session variables
session_destroy(); // Destroys the session

// Redirect to login page (or home page)
header("Location: login.php");
exit();
?>
