<?php
include 'config.php';
  // This will log to your PHP error log

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing POST request");
    $file = $_FILES['audio']['tmp_name'];
    $sender_id = $_POST['sender_id'] ?? null;
    $sender_username = $_POST['sender_username'] ?? null;

    if ($file && $sender_id && $sender_username) {
        $file_name = uniqid() . '.mp3';
        $file_path = '../uploads/' . $file_name;
        
        if (move_uploaded_file($file, $file_path)) {
            $stmt = $conn->prepare("INSERT INTO messages (file_path, sender_id, sender_username) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sis", $file_path, $sender_id, $sender_username);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Database insert failed."]);
                }
                $stmt->close();
            } else {
                error_log("Failed to prepare statement: " . $conn->error);
                echo json_encode(["status" => "error", "message" => "Failed to prepare statement."]);
            }
        } else {
            error_log("Failed to move uploaded file");
            echo json_encode(["status" => "error", "message" => "File move failed."]);
        }
    } else {
        error_log("Missing file or POST data");
        echo json_encode(["status" => "error", "message" => "Missing data."]);
    }
} else {
    error_log("Invalid request method");
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>