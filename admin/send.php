<?php
// Enable error reporting
include 'config.php';
// Prepare admin user data
$admin_username = 'admin';
$admin_password = password_hash('admin', PASSWORD_BCRYPT); // Hash the password
$admin_email = 'admin_email@example.com';

// Check if admin user already exists
$sql_check = "SELECT COUNT(*) AS count FROM admins WHERE username = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $admin_username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row = $result_check->fetch_assoc();

if ($row['count'] == 0) {
    // Prepare SQL to insert the admin user
    $sql_insert = "INSERT INTO admins (username, password, email) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    
    if ($stmt_insert) {
        $stmt_insert->bind_param("sss", $admin_username, $admin_password, $admin_email);
        
        if ($stmt_insert->execute()) {
            echo "Admin user created successfully.";
        } else {
            echo "Error creating admin user: " . $stmt_insert->error;
        }
        
        $stmt_insert->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "Admin user already exists.";
}

// Close the connection
$conn->close();
?>
