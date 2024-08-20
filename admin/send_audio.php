   
<?php include 'header.php'; ?>

<div class="container my-4">
    <div class="my-4">
        <button id="recordButton" class="btn btn-primary">Record</button>
        <button id="stopButton" class="btn btn-warning" disabled>Stop</button>
        <button id="cancelButton" class="btn btn-danger" disabled>Cancel</button>
        <p id="duration">Recording Duration: <span id="timer">0</span> seconds</p>
    </div>

    <div class="mb-3 mt-4">
        <label for="senderName" class="form-label">Sender Name</label>
        <input type="text" id="senderName" class="form-control" placeholder="Enter your name" required>
    </div>

    <button id="sendButton" class="btn btn-success mt-2" disabled>Send</button>
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
        const audioBlob = new Blob(audioChunks, { type: 'audio/mp3' });
        const formData = new FormData();
        formData.append('audio', audioBlob);
        formData.append('sender_name', document.getElementById('senderName').value);

        fetch('../backend/send_audio.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json()).then(result => {
            if (result.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Message Sent',
                    text: 'Your message has been sent successfully.',
                });
                document.getElementById('senderName').value = '';
                document.getElementById('sendButton').disabled = true;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Send Error',
                    text: result.message,
                });
            }
        }).catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'There was a problem sending the message. Please try again.',
            });
        });
    };

    document.getElementById('cancelButton').onclick = () => {
        if (mediaRecorder && mediaRecorder.state === "recording") {
            mediaRecorder.stop();
            clearInterval(interval);
        }
        audioChunks = [];
        document.getElementById('stopButton').disabled = true;
        document.getElementById('sendButton').disabled = true;
        document.getElementById('cancelButton').disabled = true;
        document.getElementById('timer').innerText = '0';
    };
</script>

<?php include 'footer.php'; ?>
