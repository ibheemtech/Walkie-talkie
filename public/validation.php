<?php
$mysqli = require __DIR__ . "/../backend/config.php";

// Initialize variables for username and email
$username = isset($_GET['username']) ? $mysqli->real_escape_string($_GET['username']) : '';
$email = isset($_GET['email']) ? $mysqli->real_escape_string($_GET['email']) : '';

$response = [];

// Check if username is provided and validate it
if (!empty($username)) {
    $username_sql = sprintf("SELECT * FROM users WHERE username = '%s'", $username);
    $username_result = $mysqli->query($username_sql);
    $response['username_available'] = $username_result->num_rows === 0;
}

// Check if email is provided and validate it
if (!empty($email)) {
    $email_sql = sprintf("SELECT * FROM users WHERE email = '%s'", $email);
    $email_result = $mysqli->query($email_sql);
    $response['email_available'] = $email_result->num_rows === 0;
}

header("Content-Type: application/json");
echo json_encode($response);
?>
