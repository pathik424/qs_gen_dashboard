<?php 

/* Template Name: Login */ 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to GEN</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="login-container">
    <h2>Login to GEN</h2>

    <form id="loginForm" action="" method="post">

        <!-- Error message displayed above the username field -->
        <div id="error-message" class="error">
            <?php
            // Display any error message stored in the session
            session_start();
            if (isset($_SESSION['login_error'])) {
                echo '<p class="error">' . $_SESSION['login_error'] . '</p>';
                unset($_SESSION['login_error']); // Clear the error after displaying it
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
                $username = sanitize_user($_POST['username']);
                $password = $_POST['password'];

                $creds = array(
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => isset($_POST['rememberMe'])
                );

                $user = wp_signon($creds, false);

                if (is_wp_error($user)) {
                    // Store the error message in the session and reload the page
                    $_SESSION['login_error'] = 'Username and Password Doesn\'t Match';
                    wp_redirect(home_url('/login'));
                    exit;
                } else {
                    // Redirect to the dashboard on successful login
                    wp_safe_redirect(home_url('/my-dashboard'));
                    echo '<script>window.location.href="' . home_url('/my-dashboard') . '";</script>';
                    exit;
                }
            }
            ?>
        </div>

        <!-- Username field -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <!-- Password field -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <!-- Remember Me checkbox -->
        <input type="checkbox" id="rememberMe" name="rememberMe">
        <label for="rememberMe">Log me in automatically</label>

        <!-- Submit button -->
        <button type="submit">Login</button>
        <?php echo do_shortcode('[linkedinbtn]'); ?>

        <!-- Registration link -->
        <p>Don't have an account? <a href="register">Register here</a></p>
    </form>
</div>
</body>
</html>



    <style>

        /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.login-container {
    background-color: #ffffff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
}

h2 {
    margin-bottom: 1rem;
    text-align: center;
    color: #333;
}

label {
    font-size: 0.9rem;
    color: #555;
    margin-top: 0.5rem;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 0.5rem;
    margin-top: 0.3rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.error {
    color: red;
    font-size: 0.85rem;
    margin-bottom: 1rem;

}

button {
    width: 100%;
    padding: 0.7rem;
    background-color: #ffcc00;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
}

button:hover {
    background-color: #e6b800;
}

p {
    text-align: center;
    font-size: 0.9rem;
    margin-top: 1rem;
}

a {
    color: #0073e6;
    text-decoration: none;
}

    </style>



