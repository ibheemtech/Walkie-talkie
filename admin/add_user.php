<?php
include 'header.php';
require __DIR__ . "/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    // Validate the form data
    if (empty($username)) {
        echo "<script>Swal.fire('Error', 'Username is required', 'error');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>Swal.fire('Error', 'A valid email is required', 'error');</script>";
    } elseif (strlen($password) < 8) {
        echo "<script>Swal.fire('Error', 'Password must be at least 8 characters long', 'error');</script>";
    } elseif (!preg_match("/[a-z]/i", $password)) {
        echo "<script>Swal.fire('Error', 'Password must contain at least one letter', 'error');</script>";
    } elseif (!preg_match("/[0-9]/", $password)) {
        echo "<script>Swal.fire('Error', 'Password must contain at least one number', 'error');</script>";
    } elseif ($password !== $password_confirmation) {
        echo "<script>Swal.fire('Error', 'Passwords must match', 'error');</script>";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement to prevent SQL injection
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("SQL error: " . $conn->error);
        }

        // Bind the parameters and execute the statement
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            echo "<script>Swal.fire('Success', 'User added successfully!', 'success').then(() => { window.location.href = 'manage_users.php'; });</script>";
        } else {
            if ($conn->errno === 1062) {
                echo "<script>Swal.fire('Error', 'Email or username already taken', 'error');</script>";
            } else {
                echo "<script>Swal.fire('Error', 'Database error: " . $stmt->error . "', 'error');</script>";
            }
        }

        // Close the statement
        $stmt->close();
    }
}
?>

<h2>Add New User</h2>

<form method="POST">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn btn-primary">Add User</button>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include 'footer.php'; ?>
