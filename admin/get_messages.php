<?php
require __DIR__ . "/config.php";

// Fetch messages from the database
$sql = "SELECT id, file_path, sender_id, sender_username, created_at FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);

$messages = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $row['id'],
            'file_path' => $row['file_path'],
            'sender_id' => $row['sender_id'],
            'sender_username' => $row['sender_username'],
            'timestamp' => $row['created_at'],
        ];
    }
}

// Output JSON
echo json_encode($messages);

$conn->close();
?>
