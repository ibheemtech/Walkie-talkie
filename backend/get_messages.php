<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

$query = "
    SELECT m.id, m.file_path, m.sender_username, m.created_at,
    (SELECT COUNT(*) FROM deleted_messages WHERE message_id = m.id AND user_id = ?) AS deleted_for_user
    FROM messages m
    LEFT JOIN deleted_messages d ON m.id = d.message_id AND d.user_id = ?
    WHERE d.id IS NULL";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
$stmt->close();
?>
