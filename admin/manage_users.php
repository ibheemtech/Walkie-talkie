<?php include 'header.php'; ?>
<?php
require __DIR__ . "/config.php";

// Handle deletion of a user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "User deleted successfully!";
    } else {
        $error = "Error deleting user!";
    }
}
?>

<h2>Manage Users</h2>
<a href="add_user.php" class="btn btn-success mb-3">Add New User</a>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['username']}</td>
                        <td>{$row['email']}</td>
                        <td>
                            <a href='edit_user.php?id={$row['id']}' class='btn btn-sm btn-warning'>Update</a>
                            <a href='manage_users.php?delete={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No users found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
