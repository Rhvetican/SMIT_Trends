<?php
session_start(); // Start session

// Retrieve form data
$username = htmlspecialchars($_POST['firstname'], ENT_QUOTES, 'UTF-8');
$password = $_POST['password']; // Raw password input

// Check if both fields are empty
if (empty($username) || empty($password)) {
    echo "Both fields are required.";
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'appdatabase');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
} else {
    // Prepare the SQL statement to check the username and password
    $stmt = $conn->prepare("SELECT password FROM registration WHERE username = ?");
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();
    
    // Store the result
    $stmt->store_result();

    // Check if any record exists
    if ($stmt->num_rows > 0) {
        // Bind the result to a variable
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a session and redirect to home.html
            $_SESSION['username'] = $username; // Store username in session
            header("Location: home.html"); // Redirect to home.html
            exit(); // Ensure no further code is executed after the redirect
        } else {
            // Password is incorrect
            echo "Incorrect password.";
        }
    } else {
        // Username not found
        echo "Username not found.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
    