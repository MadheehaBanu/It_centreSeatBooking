<?php
// Start session if not already started
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UniSpace</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <img src="images/background.png" alt="IT Workspace">
        </div>
        <div class="right-panel">
            <div class="login-box">
                <img src="images/logo-main.png" alt="Logo" class="logo">
                <h2>Reserve your IT workspace easily</h2>
                <h3>Nice to see you again</h3>
                <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    switch($_GET['error']) {
                        case 'emptyfields':
                            echo "Please fill in all fields";
                            break;
                        case 'wrongcredentials':
                            echo "Invalid email or password";
                            break;
                        default:
                            echo "An error occurred. Please try again.";
                    }
                    ?>
                </div>
                <?php endif; ?>
                <form action="includes/login.php" method="post">
                    <label for="email">Login</label>
                    <input type="text" id="email" name="email" placeholder="Email or phone number" required>
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                        <span class="password-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="options">
                        <label><input type="checkbox" name="remember"> Remember me</label>
                        <a href="#">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn-signin">Sign in</button>
                    <p class="signup-link">Dont have an account? <a href="signup.php">Sign up now</a></p>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const passwordInput = this.parentElement.querySelector('input');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                } else {
                    passwordInput.type = 'password';
                }
            });
        });
    </script>
</body>
</html>