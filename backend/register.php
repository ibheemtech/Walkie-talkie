<?php
// Include the database connection
require __DIR__ . "/config.php";

// Validate the form data
if (empty($_POST["username"])) {
    die("Username is required");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("A valid email is required");
}

if (strlen($_POST["password"]) < 8) {
    die('Password must be at least 8 characters long');
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die('Password must contain at least one letter');
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die('Password must contain at least one number');
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// Hash the password
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Prepare the SQL statement to prevent SQL injection
$sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $conn->error);
}

// Bind the parameters and execute the statement
$stmt->bind_param("sss", $_POST["username"], $_POST["email"], $password_hash);

if ($stmt->execute()) {
    // Start session management
    session_start();

    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id();

    // Store user data in session variables
    $_SESSION["user_id"] = $stmt->insert_id;
    $_SESSION["username"] = $_POST["username"];

    // Redirect to a success page
    header("Location:../public/walkie-talkie.php");
    exit;
} else {
    if ($conn->errno === 1062) {
        die("Email or username already taken");
    } else {
        die("Database error: " . $conn->error . " (" . $conn->errno . ")");
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
