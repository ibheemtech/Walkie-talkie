<?php 
include 'config.php'; // Ensure this path is correct
include 'header.php'; 

$admin_username = $_SESSION['admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $email = $_POST['email'];
    
    // Start constructing the SQL query
    $sql = "UPDATE admins SET username = ?, email = ?";
    $params = [$new_username, $email];
    $types = "ss";
    
    // Check if the password field is not empty
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $password;
        $types .= "s";
    }
    
    // Complete the SQL query
    $sql .= " WHERE username = ?";
    $params[] = $admin_username;
    $types .= "s";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['admin'] = $new_username; // Update session with the new username
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile!";
    }
}

// Retrieve the current admin's details
$sql = "SELECT * FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<h2>Admin Profile</h2>
<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<form method="POST" action="">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

<?php include 'footer.php'; ?>
