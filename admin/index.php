<?php
include 'config.php';
// Fetch the total number of users and messages
$totalUsersSql = "SELECT COUNT(*) AS total_users FROM users";
$totalMessagesSql = "SELECT COUNT(*) AS total_messages FROM messages";

$totalUsersResult = $conn->query($totalUsersSql);
$totalMessagesResult = $conn->query($totalMessagesSql);

$totalUsers = $totalUsersResult->fetch_assoc()['total_users'];
$totalMessages = $totalMessagesResult->fetch_assoc()['total_messages'];
?>

<?php include 'header.php'; ?>

<h2>Overview</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?>!</p>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Messages</h5>
                    <p class="card-text"><?php echo $totalMessages; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<?php
// Close the database connection
$conn->close();
?>
