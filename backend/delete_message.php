<?php
session_start();
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$message_id = $data['message_id'];
$user_id = $data['user_id'];

$query = "INSERT INTO deleted_messages (message_id, user_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $message_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete the message."]);
}

$stmt->close();
?>
