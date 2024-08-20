<?php include 'header.php'; ?>
<?php
require __DIR__ . "/config.php";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>Swal.fire('Deleted!', 'The message has been deleted.', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error!', 'There was an error deleting the message.', 'error');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

    <div class="container">
        <h2 class="mt-4">Manage Messages</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Message</th>
                    <th>Time Sent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="messageTableBody">
                <!-- Messages will be loaded here -->
            </tbody>
        </table>
    </div>

    <script>
        function loadMessages() {
    fetch('get_messages.php')
        .then(response => response.json())
        .then(messages => {
            const messageTableBody = document.getElementById('messageTableBody');
            messageTableBody.innerHTML = '';

            messages.forEach(message => {
                const row = `
                    <tr>
                        <td>${message.sender_username}</td>
                        <td>
                            <audio controls>
                                <source src="${message.file_path}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </td>
                        <td>${message.timestamp}</td>
                        <td>
                            <a href="manage_messages.php?delete=${message.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this message permanently?')">Delete</a>
                        </td>
                    </tr>
                `;
                messageTableBody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(error => console.error('Error fetching messages:', error));
}

loadMessages();

    </script>
</body>
</html>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include 'footer.php'; ?>
