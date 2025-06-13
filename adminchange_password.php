<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $error = "New passwords don't match";
    } else {
        $sql = "SELECT password FROM admins WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($current_password, $admin['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE admins SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_password, $admin_id);

                if ($update_stmt->execute()) {
                    $success = "Password changed successfully!";
                } else {
                    $error = "Failed to update password";
                }
                $update_stmt->close();
            } else {
                $error = "Current password is incorrect";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Eventra</title>
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

        body {
            font-family: 'Poppins', sans-serif;
            background: url('background7.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--black);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.5rem;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header-left h1 {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--black);
        }

        .back-button {
            background: var(--black);
            position: fixed;
            top: 0.5rem;
            right: 0.5rem;
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .back-button:hover{
            transform: scale(0.97);
        }

        

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 10px 20px;
            width: 100%;
            flex: 1;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(100px);
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.25);
            padding: 1.5rem;
            border-radius: 8px;
            width: 100%;
            max-width: 450px;
        }

        .form-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            color: black;
            margin-bottom: 15px;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(90px);
        }

        .form-button {
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

        .form-button:hover {
            transform: scale(0.97);
        }

        .error, .success {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .error {
            color: black;
        }

        .success {
            color: black;
        }

        .footer {
            width: 100%;
            color: black;
            padding: 0.2rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
        }

        .footer a {
            color: black;
            margin-left: 1rem;
            text-decoration: none;
        }

        .footer a:hover {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <h1>Eventra</h1>
        </div>
        <a href="clubprofile.php" class="back-button"> <i class="fas fa-arrow-left"></i></a>
    </header>

    <div class="main-content">
        <div class="form-container">
            <div class="form-title">Time for a Password Makeover</div>

            <?php if (!empty($error)): ?>
                <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="password" class="input-field" name="current_password" placeholder="Current password for proof" required>
                <input type="password" class="input-field" name="new_password" placeholder="New one? Something unforgettable" required>
                <input type="password" class="input-field" name="confirm_password" placeholder="Repeat the new pass. You know the drill" required>
                <button type="submit" class="form-button">Update Password</button>
            </form>
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

    <?php if (!empty($success)): ?>
        <script>
            setTimeout(function() {
                window.location.href = "clubprofile.php";
            }, 1200);
        </script>
    <?php endif; ?>
</body>
</html>
<?php $conn->close(); ?>