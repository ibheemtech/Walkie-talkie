<?php include 'header.php'; ?>
<?php
require __DIR__ . "/config.php";

// Get the user's current details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, username, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("User not found");
    }
}

// Update the user's details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Validate the form data
    if (empty($username)) {
        die("Username is required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("A valid email is required");
    }

    // If a new password is provided, hash it
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 8) {
            die('Password must be at least 8 characters long');
        }

        if (!preg_match("/[a-z]/i", $_POST['password'])) {
            die('Password must contain at least one letter');
        }

        if (!preg_match("/[0-9]/", $_POST['password'])) {
            die('Password must contain at least one number');
        }

        if ($_POST['password'] !== $_POST['password_confirmation']) {
            die("Passwords must match");
        }

        // Hash the new password
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Update username, email, and password
        $sql = "UPDATE users SET username = ?, email = ?, password_hash = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $email, $password_hash, $id);
    } else {
        // Update only username and email
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $email, $id);
    }

    if ($stmt->execute()) {
        $success = "User updated successfully!";
    } else {
        $error = "Error updating user: " . $stmt->error;
    }
}
?>

<h2>Edit User</h2>
<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">New Password (leave blank to keep the current password)</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
    </div>
    <button type="submit" class="btn btn-primary">Update User</button>
</form>

<?php include 'footer.php'; ?>
