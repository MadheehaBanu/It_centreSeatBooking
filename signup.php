<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - UniSpace</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <img src="images/background.png" alt="IT Workspace">
        </div>
        <div class="right-panel">
            <div class="login-box signup-box">
                <img src="images/logo-main.png" alt="Logo" class="logo">
                <h2>Reserve your IT workspace easily</h2>
                <form action="includes/signup.php" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" placeholder="" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" placeholder="" required>
                        </div>
                    </div>
                    
                    <label for="registration_number">Registration Number</label>
                    <input type="text" id="registration_number" name="registration_number" placeholder="" required>
                    
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="" required>
                    
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" placeholder="" required>
                    
                    <label for="password">Create Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="" required>
                        <span class="password-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </span>
                    </div>
                    
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="" required>
                        <span class="password-toggle confirm-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </span>
                    </div>
                    
                    <button type="submit" class="btn-signin">Sign Up</button>
                    <p class="signup-link">Already have an account? <a href="index.php">Sign in</a></p>
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