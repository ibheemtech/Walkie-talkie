<?php
// Include the database connection
require __DIR__ . "/config.php";

// Initialize variables for email and username
$email = isset($_GET['email']) ? $_GET['email'] : '';
$username = isset($_GET['username']) ? $_GET['username'] : '';

$response = [];

// Check if email is provided and validate its availability
if (!empty($email)) {
    $email_sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $email_result = $stmt->get_result();
    $response['email_available'] = $email_result->num_rows === 0;
}

// Check if username is provided and validate its availability
if (!empty($username)) {
    $username_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($username_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $username_result = $stmt->get_result();
    $response['username_available'] = $username_result->num_rows === 0;
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Output the response in JSON format
header("Content-Type: application/json");
echo json_encode($response);
?>
