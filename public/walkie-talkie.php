<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location:../index.php");
    exit();
}
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walkie-Talkie</title>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
        <div class="my-4">
            <button id="recordButton" class="btn btn-primary">Record</button>
            <button id="stopButton" class="btn btn-warning" disabled>Stop</button>
            <button id="sendButton" class="btn btn-success" disabled>Send</button>
            <button id="cancelButton" class="btn btn-danger" disabled>Cancel</button>
            <p id="duration">Recording Duration: <span id="timer">0</span> seconds</p>
        </div>

        <button id="logoutButton" class="btn btn-danger mt-4">Log Out</button>

        <h3 class="mt-4">Messages:</h3>
        <ul id="messageList" class="list-group"></ul>
    </div>

    <script>
        let mediaRecorder;
        let audioChunks = [];
        let timer = 0;
        let interval;

        document.getElementById('recordButton').onclick = () => {
            navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.start();
                audioChunks = [];

                mediaRecorder.addEventListener('dataavailable', event => {
                    audioChunks.push(event.data);
                });

                interval = setInterval(() => {
                    timer++;
                    document.getElementById('timer').innerText = timer;
                }, 1000);

                document.getElementById('stopButton').disabled = false;
                document.getElementById('sendButton').disabled = true;
                document.getElementById('cancelButton').disabled = false;

                Swal.fire({
                    icon: 'info',
                    title: 'Recording started',
                    text: 'Recording your message...',
                });
            }).catch(error => {
                console.error("Error accessing microphone:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Microphone Error',
                    text: 'Could not access your microphone. Please check your settings.',
                });
            });
        };

        document.getElementById('stopButton').onclick = () => {
            if (mediaRecorder && mediaRecorder.state === "recording") {
                mediaRecorder.stop();
                clearInterval(interval);
                document.getElementById('stopButton').disabled = true;
                document.getElementById('sendButton').disabled = false;

                Swal.fire({
                    icon: 'info',
                    title: 'Recording stopped',
                    text: 'You can now send your message.',
                });
            }
        };

        document.getElementById('sendButton').onclick = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
            const formData = new FormData();
            formData.append('audio', audioBlob);
            formData.append('sender_id', <?php echo $user_id; ?>);
            formData.append('sender_username', '<?php echo htmlspecialchars($username); ?>');

            fetch('../backend/send_audio.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      Swal.fire({
                          icon: 'success',
                          title: 'Message Sent',
                          text: 'Your message has been sent successfully!',
                      });

                      // Trigger vibration if supported
                      if ('vibrate' in navigator) {
                          navigator.vibrate(400); // Vibrate for 400ms
                      }

                      setTimeout(() => {
                          location.reload(); // Reload the page after sending the message
                      }, 500); // Wait 1 second to ensure the alert has been shown
                  } else {
                      Swal.fire({
                          icon: 'error',
                          title: 'Send Error',
                          text: 'Failed to send the message. Please try again.',
                      });
                  }
              }).catch(error => {
                  console.error("Error sending audio:", error);
                  Swal.fire({
                      icon: 'error',
                      title: 'Connection Error',
                      text: 'Failed to send the message. Please check your connection.',
                  });
              });
        };

        document.getElementById('cancelButton').onclick = () => {
    if (mediaRecorder && mediaRecorder.state === "recording") {
        mediaRecorder.stop();
        clearInterval(interval);
        document.getElementById('stopButton').disabled = true;
        document.getElementById('sendButton').disabled = true;
        document.getElementById('cancelButton').disabled = true;

        Swal.fire({
            icon: 'info',
            title: 'Recording cancelled',
            text: 'The recording has been cancelled.',
        }).then(() => {
            // Reload the page after a delay
            setTimeout(() => {
                location.reload(); // Reload the page
            }, 200); // Delay of 1500ms (1.5 seconds)
        });
    }
};

 function loadMessages() {
    fetch('../backend/get_messages.php')
        .then(response => response.json())
        .then(messages => {
            const messageList = document.getElementById('messageList');
            messageList.innerHTML = '';
            let hasNewMessages = false;

            messages.forEach(message => {
                if (!message.deleted_for_user) {
                    hasNewMessages = true;

                    const li = document.createElement('li');
                    li.classList.add('list-group-item');

                    // Display sender's name
                    const sender = document.createElement('span');
                    sender.innerText = message.sender_username + ': ';
                    li.appendChild(sender);

                    // Create an audio element for the MP3
                    const audioPlayer = document.createElement('audio');
                    audioPlayer.controls = true;
                    audioPlayer.src = message.file_path;  // Make sure this is pointing to the correct MP3 file path
                    li.appendChild(audioPlayer);

                    // Display the date and time
                    const timestamp = document.createElement('span');
                    timestamp.classList.add('ml-2', 'text-muted');
                    timestamp.innerText = new Date(message.created_at).toLocaleString();
                    li.appendChild(timestamp);

                    // Add a delete button
                    const deleteButton = document.createElement('button');
                    deleteButton.innerText = 'Delete';
                    deleteButton.classList.add('btn', 'btn-danger', 'ml-2');
                    deleteButton.onclick = () => deleteMessage(message.id, li);

                    li.appendChild(deleteButton);
                    messageList.appendChild(li);
                }
            });

            // Trigger vibration if there are new messages
            if (hasNewMessages && 'vibrate' in navigator) {
                navigator.vibrate(400); // Vibrate for 500ms
            }
        })
        .catch(error => {
            console.error("Error loading messages:", error);
            Swal.fire({
                icon: 'error',
                title: 'Load Error',
                text: 'Failed to load messages. Please try again.',
            });
        });
}

        function deleteMessage(messageId, listItem) {
            fetch('../backend/delete_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message_id: messageId,
                    user_id: <?php echo $user_id; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    listItem.remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Deleted',
                        text: 'The message has been deleted for you.',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Error',
                        text: 'Failed to delete the message. Please try again.',
                    });
                }
            })
            .catch(error => {
                console.error("Error deleting message:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Failed to delete the message. Please check your connection.',
                });
            });
        }

        document.getElementById('logoutButton').onclick = () => {
            Swal.fire({
                title: 'Are you sure you want to log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../backend/logout.php';
                }
            });
        };

        loadMessages();  // Initial load of messages
    </script>
</body>
</html>
