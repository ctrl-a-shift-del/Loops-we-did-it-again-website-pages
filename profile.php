<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];

$user = [];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
}
$stmt->close();

// Get number of events participated
$events_participated = 0;
$sql = "SELECT COUNT(*) as count FROM registrations WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $events_participated = $row['count'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Eventra</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            margin: 0;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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

        .menu-button {
            background: var(--black);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-button:hover{
            transform: scale(0.97);
        }

        .slide-menu {
            position: fixed;
            top: 0;
            right: -260px;
            width: 250px;
            height: 100%;
            background: url('backround7.jpg') no-repeat center center fixed;
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 4rem 1rem 1rem;
            transition: right 0.3s ease;
            z-index: 1002;
        }

        .slide-menu.open {
            right: 0;
        }

        .slide-menu a {
            display: block;
            margin: 0.5rem 0;
            padding: 0.5rem;
            background-color: var(--black);
            color: white;
            font-weight: 600;
            font-size: 15px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
        }

        .slide-menu a:hover {
            transform: scale(0.97);
        }

        .close-btn {
            position: absolute;
            top: 0.7rem;
            right: 0.6rem;
            background: var(--black);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            transform: scale(0.97);
        }

        .backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.3);
            z-index: 1001;
            display: none;
        }

        .backdrop.show {
            display: block;
        }

        .content-wrapper {
            padding-top: 4rem;
            padding-left: 1rem;
            padding-right: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .section-box {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 450px;
        }

        .section-box h2 {
            font-size: 20px;
            color: var(--black);
            margin-bottom: 1rem;
        }

        .section-box p {
            font-size: 15px;
            color: var(--black);
            margin: 0.5rem 0;
        }

        .action-button, .delete-button {
            padding: 10px 16px;
            margin: 0.5rem 0;
            border: none;
            border-radius: 8px;
            background-color: var(--black);
            color: var(--white);
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
            transition: transform 0.2s;
            width: 100%;
        }

        .action-button:hover {
            transform: scale(0.97);
        }

        .delete-button:hover {
            transform: scale(0.97);
            color: red;
            background-color: white;
        }

        .footer {
            margin-top: auto;
            width: 100%;
            color: black;
            padding: 0.2rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            background: transparent;
        }

        .footer a {
            color: black;
            margin-left: 1rem;
            text-decoration: none;
        }

        .footer a:hover {
            font-weight: bold;
        }

        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .badge-item {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 8px 12px;
            border-radius: 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .badge-icon {
            color: gold;
        }
        @media (max-width: 480px) {
            .section-box {
            width: 100%;
            max-width: 95%;
            }
        }

    </style>
</head>
<body>
    <div class="main-container">
        <div class="slide-menu" id="menu">
            <button class="close-btn" onclick="toggleMenu()">×</button>
            <a href="home.php">Home</a>
            <a href="leaderboard.php">Leaderboard</a>
        </div>
        <div class="backdrop" id="backdrop" onclick="toggleMenu()"></div>

        <header>
            <div class="header-left">
                <h1>Eventra</h1>
            </div>
            <div class="header-right">
                <button class="menu-button" onclick="toggleMenu()">☰</button>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="section-box">
                <h2>Your Résumé</h2>
                <p><strong>What People Call You:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Where We'll Spam You:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Times You Actually Showed Effort:</strong> <?php echo $events_participated; ?></p>
                
            </div>

            <div class="section-box">
                <h2>Big Decisions</h2>
                <button class="action-button" onclick="window.location.href='change_password.php'">Change Password</button><br>
                <button class="action-button" onclick="window.location.href='logout.php'">Log Out</button><br>
                <button class="delete-button" onclick="confirmDelete()">Goodbye Button</button>
            </div>
        </div>

        <footer class="footer">
            <div style="font-weight: bold;">&copy; 2025 Eventra</div>
            <div>
                <a href="privacy.php">Privacy</a>
                <a href="terms.php">Terms</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
            </div>
        </footer>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('menu');
            const backdrop = document.getElementById('backdrop');
            const isOpen = menu.classList.contains('open');
            if (isOpen) {
                menu.classList.remove('open');
                backdrop.classList.remove('show');
            } else {
                menu.classList.add('open');
                backdrop.classList.add('show');
            }
        }

        function confirmDelete() {
            if (confirm("You're about to delete your account... Forever  :( ")) {
                // Create a form to submit a POST request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_account.php';
            
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = 'csrf_token';
                csrfToken.value = '<?php echo $_SESSION['csrf_token']; ?>';
                form.appendChild(csrfToken);
            
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>