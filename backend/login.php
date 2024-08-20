<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$is_invalid = false;
$is_email_missing = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require __DIR__ . "/config.php";  // Ensure this path is correct

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if the password is correct
        if (password_verify($password, $user["password_hash"])) {
            session_start();
            session_regenerate_id();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            // Redirect to the walkie-talkie page
            header("Location: ../public/walkie-talkie.php");
            exit;
        } else {
            $is_invalid = true;
        }
    } else {
        $is_email_missing = true;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h1>Login</h1>

    <?php if ($is_invalid): ?> 
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Invalid login credentials. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

    <?php if ($is_email_missing): ?> 
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'No account found with this email address.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

    <form method="post">
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
           
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
