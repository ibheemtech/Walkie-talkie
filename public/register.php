<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
</head>
<body>
    <h1>Register</h1>
    <form action="../backend/register.php" method="post" id="signup" novalidate>
        <div>
            <label for="username">Username</label>
            <input type="test" name="username" id="username" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
        </div>
        <div>
            <label for="password_confirmation">Repeat Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
        </div>
        <button type="submit">Submit</button>
    </form>

 <script>
document.addEventListener('DOMContentLoaded', function () {
    const validation = new JustValidate("#signup");

    validation
        .addField("#username", [
            {
                rule: 'required',
                errorMessage: 'Username is required',
            },
            {
                validator: (value) => () => {
                    return fetch("../backend/validation.php?username=" + encodeURIComponent(value))
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (json) {
                            return json.username_available;
                        });
                },
                errorMessage: "Username already taken",
            }
        ])
        .addField("#email", [
            {
                rule: "required",
                errorMessage: 'Email is required',
            },
            {
                rule: "email",
                errorMessage: 'Please enter a valid email',
            },
            {
                validator: (value) => () => {
                    return fetch("../backend/validation.php?email=" + encodeURIComponent(value))
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (json) {
                            return json.email_available;
                        });
                },
                errorMessage: "Email already taken",
            }
        ])
        .addField("#password", [
            {
                rule: "required",
                errorMessage: 'Password is required',
            },
            {
                rule: "password",
                errorMessage: 'Password must be at least 8 characters long and contain a letter and a number',
            }
        ])
        .addField("#password_confirmation", [
            {
                validator: (value, fields) => {
                    return value === fields["#password"].elem.value;
                },
                errorMessage: "Passwords must match",
            }
        ])
        .onSuccess((event) => {
            event.preventDefault();

            // SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to submit the form?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    document.getElementById("signup").submit();
                }
            });
        });
});
</script>

   
      
</body>
</html>
