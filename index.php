<?php
session_start();
require 'db_connect.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT id, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php");
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Email not found";
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eventra</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        :root {
          --black: #000000;
          --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: white;
            background: url('background7.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 10px;
            width: 100%;
            min-height: calc(100vh - 50px); /* minus footer height */
            box-sizing: border-box;
            /*flex: 1;
            padding: 10px;
            justify-content: center;*/
        }

        h1 {
            font-size: 60px;
            font-weight: 900;
            text-align: center;
            z-index: 10;
            
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }

        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 10px;
            

        }

        .login-container {
            background: url('backround7.jpg') no-repeat center center fixed;
            background-color: rgba(255, 255, 255, 0.3) ;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(100px);
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.25);
            padding: 1rem;
            border-radius: 8px;
            width: 100%;
            max-width: 450px;
            margin-bottom: 40px;
        }


        .login-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            color: black;
            margin-bottom: 10px;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            box-sizing: border-box;
            background: url('backround7.jpg') no-repeat center center fixed;
            background-color: rgba(255, 255, 255, 0.1) ;
            backdrop-filter: blur(90px);
            
        }

        .login-button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: black;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .login-button:hover {
                transform: scale(0.97); /*1.025*/
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 15px;
            color: black;
        }

        .signup-link a {
            color: black;
            text-decoration: none;
        }

        .signup-link a:hover {
            font-weight: bold;
        }

        .forgot-password-link {
            color: black;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .forgot-password-link:hover {
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            /*background: url('backround3.jpg') no-repeat center center fixed;
            background-color: rgba(255, 255, 255, 0.3) ;
            backdrop-filter: blur(10px);*/
            color: black;
            padding: 0.2rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            
            /*box-shadow: 0 0px 10px rgba(0, 0, 0, 0.25);*/
        }

        .footer a {
            color: black;
            margin-left: 1rem;
            text-decoration: none;
        }

        .footer a:hover {
            font-weight: bold;
        }


        .password-container {
            position: relative;
            width: 100%;
        }

        #togglePassword {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            transition: color 0.3s;
        }

        #togglePassword:hover {
            color: black;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            h1 {
                font-family: 'Poppins', sans-serif;
                font-size: 50px;
                font-weight: 900;
                margin-top: 0px;
                margin-bottom: 0px;
            }
            body{
                background: url('background7.jpg') no-repeat center center fixed;
                background-size: cover;
                overflow-y: auto;

            }
            
            .login-container {
                padding: 25px 20px;
                max-width: 390px;
                margin-top: 60px;
            }

            .login-title {
                font-size: 22px;
            }

            .input-field, 
            .login-button {
                padding: 10px 12px;
                font-size: 15px;
            }

            .signup-link {
                font-size: 15px;
            }
            .forgot-password-link {
                font-size: 13px;
            }
            .footer{
                padding: 0.2rem 0.5rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 40px;
                font-weight: 900;
            }
            body{
                background: url('background7.jpg') no-repeat center center fixed;
                background-size: cover;
                overflow-y: auto;

            }

            .login-container {
                padding: 20px 15px;
            }

            .login-title {
                font-size: 20px;
            }

        }

        @media (max-width: 360px) {
            h1 {
                font-size: 32px;
                font-weight: 900;
            }
            body{
                background: url('background7.jpg') no-repeat center center fixed;
                background-size: cover;
                overflow-y: auto;

            }

            .signup-link {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1 style = "color: black;">Evèntra</h1>
        <div class="login-wrapper">
            <div class="login-container">
                <?php if (!empty($error)): ?>
                    <div style="color: black; text-align: center; margin-bottom: 15px; font-size: 14px;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <div class="login-title">Member Login</div>
                    <form method="POST" action="">
                    <input type="email" class="input-field" name="email" placeholder="Your sacred email address please" required>
                    <div class="password-container">
                        <input type="password" class="input-field" name="password" id="password" placeholder="Ex's name? Sorry, meant your password" required>
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </div>
                <div style="text-align: right;">
                    <a href="#" class="forgot-password-link" onclick="showPopup()">Forgot Password?</a>
                </div>            
                    <button type="submit" class="login-button"> Let me in!</button>
                    </form>
                <div class="signup-link">
                <p>New here? <a href="signup.php">Sign up and have fun!</a></p>
                <p style = "margin-top: 7px;">Club admin? <a href="adminlogin.php">This way, Your Highness!</a></pstyle>
                </div>
            </div>
        </div>
        
    </div>

    <footer class="footer">
        <div style="font-weight: bold ;">&copy; 2025 Eventra</div>
        <div>
            <a href="privacy.php">Privacy</a>
            <a href="terms.php">Terms</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
         </div>
    </footer>



    <script>
        const toggle = document.getElementById("togglePassword");
        const password = document.getElementById("password");

        toggle.addEventListener("click", function () {
            // Toggle the type
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
        
            // Toggle the icon
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });

        function showPopup() {
            alert("Feature under construction. Your memory too, apparently :(");
        }
    </script>
</body>
</html>