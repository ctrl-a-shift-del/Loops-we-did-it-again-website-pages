<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$clubname = $_SESSION['clubname'];

$admin = [];
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $admin = $result->fetch_assoc();
}
$stmt->close();

$event_count = 0;
$sql = "SELECT COUNT(*) as count FROM events WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $event_count = $row['count'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Profile - Eventra</title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
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
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            
        }

        .section-box {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: calc(100% - 1rem);
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

        .action-button {
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
        .delete-button{
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
        .delete-button:hover{
            transform: scale(0.97);
            color: red;
            background-color: white;
        }
        .action-button:hover {
            transform: scale(0.97);
            
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
        @media (max-width: 600px) {
            .section-box:last-of-type {
            margin-bottom: 0.5rem; /* reduce bottom margin only for last section box */
        }
        .content-wrapper {
            padding-bottom: 1rem; /* reduce padding bottom if any */
        }
}


        
    </style>
</head>
<body>
    <div class="slide-menu" id="menu">
        <button class="close-btn" onclick="toggleMenu()">×</button>
        <a href="admin_home.php">Home</a>
        <a href="create_event.php">Create Event</a>
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
            <h2>Club Résumé</h2>
            <p><strong>Club Name:</strong> <?= htmlspecialchars($admin['clubname']) ?></p>
            <p><strong>Blame goes to:</strong> <?= htmlspecialchars($admin['name']) ?></p>
            <p><strong>Mail Dump:</strong> <?= htmlspecialchars($admin['email']) ?></p>
            <p><strong>Events Created So Far:</strong> <?= $event_count ?></p>
        </div>

        <div class="section-box">
            <h2>Big Decisions</h2>
            <button class="action-button" onclick="window.location.href='adminchange_password.php'">Password  Drama</button><br>
            <button class="action-button" onclick="window.location.href='adminlogout.php'">Sign Out</button><br>
            <button class="delete-button" onclick="confirmDelete()">Nuke Account </button>
        </div>
        
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
            if (confirm("You're about to delete your account... Forever  :(")) {
                window.location.href = 'admindelete_account.php';
            }
        }
    </script>

    </script>

    <footer class="footer">
        <div style="font-weight: bold ;">&copy; 2025 Eventra</div>
        <div>
            <a href="privacy.php">Privacy</a>
            <a href="terms.php">Terms</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </div>
    </footer>

</body>
</html>
<?php $conn->close(); ?>

