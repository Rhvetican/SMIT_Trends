<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        header("Location: signup.html?error=Passwords do not match");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header("Location: signup.html?error=Email already exists");
            exit();
        }

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password]);

        // Show success message and redirect
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sign Up Success - SMIT TRENDS</title>
            <link rel="stylesheet" href="style.css">
            <script type="text/javascript" src="../action.js" defer></script>
        </head>
        <body>
            <div class="auth-container">
                <div class="auth-box">
                    <h1>Sign Up Successful!</h1>
                    <div class="success-message">
                        <p>Your account has been created successfully.</p>
                        <p>Redirecting to sign in page in 5 seconds...</p>
                    </div>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "signin.html";
                }, 5000);
            </script>
        </body>
        </html>';
        exit();
    } catch(PDOException $e) {
        header("Location: signup.html?error=Database error: " . $e->getMessage());
        exit();
    }
}
?> 