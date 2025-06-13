<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['id'];

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Register for event
        $conn->begin_transaction();
        try {
            // Insert registration
            $sql = "INSERT INTO registrations (user_id, event_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $event_id);
            $stmt->execute();
            $stmt->close();
            
            // Update registration count
            $update_sql = "UPDATE events SET registered_count = registered_count + 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $event_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Store registration details
            $user_sql = "SELECT name, email FROM users WHERE id = ?";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $user_data = $user_result->fetch_assoc();
            $user_stmt->close();
            
            $detail_sql = "INSERT INTO registration_details (event_id, user_id, user_name, user_email) VALUES (?, ?, ?, ?)";
            $detail_stmt = $conn->prepare($detail_sql);
            $detail_stmt->bind_param("iiss", $event_id, $user_id, $user_data['name'], $user_data['email']);
            $detail_stmt->execute();
            $detail_stmt->close();
            
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error registering for event: " . $e->getMessage();
        }
    }
    // Refresh the page to show updated status
    header("Location: event_details.php?id=$event_id");
    exit;
}

// Fetch event details
$event = [];
$sql = "SELECT e.*, a.clubname 
        FROM events e
        JOIN admins a ON e.admin_id = a.id
        WHERE e.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: home.php");
    exit;
}

$event = $result->fetch_assoc();
$stmt->close();

// Check if user is registered
$is_registered = false;
$sql = "SELECT id FROM registrations WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();
$is_registered = $result->num_rows > 0;
$stmt->close();

// Parse media paths
$media_paths = !empty($event['media_paths']) ? json_decode($event['media_paths'], true) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - Eventra</title>
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

        .back-button {
            position: absolute;
            top: 0.5rem;
            right: 0.6rem;
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
        .back-button:hover {
            transform: scale(0.97);
        }

        .content-wrapper {
            padding-top: 4rem;
            padding-left: 0.2rem;
            padding-right: 0.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .section-box {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 0.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 1000px;
            max-width: calc(100% - 1rem);
        }

        .event-container {
            display: flex;
            flex-direction: column;
        }

        .event-content {
            display: flex;
            flex-direction: column;
        }

        .event-details {
            flex: 1;
        }

        .event-media {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
        }

        .event-poster {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
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

        .info-label {
            font-weight: bold;
            color: black;
        }

        .register-button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background-color: var(--black);
            color: var(--white);
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
            transition: transform 0.2s;
            width: 100%;
            max-width: 200px;
            margin-bottom: 1rem;
        }

        .register-button:hover {
            transform: scale(0.97);
        }

        .media-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .media-item {
            width: 100%;
            max-width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
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
        
        .registration-status {
            font-weight: bold;
            color: black;
            margin-bottom: 1rem;
        }
        
        /* Desktop layout */
        @media (min-width: 992px) {
            .event-content {
                flex-direction: row;
                gap: 2rem;
            }
            
            .event-details {
                flex: 2;
            }
            
            .event-media {
                flex: 1;
                margin-top: 0;
                align-self: flex-start;
                position: sticky;
                top: 5rem;
            }
            
            .event-poster {
                width: 300px;
                height: 430px;
            }
        }
        
        @media (max-width: 600px) {
            .section-box:last-of-type {
                margin-bottom: 0.5rem;
            }
            .content-wrapper {
                padding-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <h1>Eventra</h1>
        </div>
        <div class="header-right">
            <button class="back-button" onclick="window.location.href='home.php'">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="section-box">
            <div class="event-container">
                <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
                
                <div class="event-content">
                    <div class="event-details">
                        <?php if ($is_registered): ?>
                            <div class="registration-status">STATUS: REGISTERED</div>
                        <?php else: ?>
                            <form method="POST" onsubmit="return confirmRegistration()">
                                <button type="submit" name="register" class="register-button">
                                    Count Me In!
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <p><span class="info-label">Event Host:</span> <?php echo htmlspecialchars($event['clubname']); ?></p>
                        <p><span class="info-label">When It's Happening:</span> 
                            <?php echo date('F j, Y', strtotime($event['start_date'])); ?>
                            <?php if ($event['start_date'] != $event['end_date']): ?>
                                to <?php echo date('F j, Y', strtotime($event['end_date'])); ?>
                            <?php endif; ?>
                        </p>
                        <p><span class="info-label">Showtime:</span> 
                            <?php echo date('h:i A', strtotime($event['start_time'])); ?> to <?php echo date('h:i A', strtotime($event['end_time'])); ?>
                        </p>
                        <p><span class="info-label">The Scene:</span> <?php echo nl2br(htmlspecialchars($event['venue'])); ?></p>
                        <p><span class="info-label">The Plot:</span><br><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        
                        <?php if (!empty($media_paths) && count($media_paths) > 1): ?>
                            <p><span class="info-label">Media Gallery:</span></p>
                            <div class="media-gallery">
                                <?php foreach ($media_paths as $index => $media): ?>
                                    <?php if ($index > 0): ?>
                                        <img src="<?php echo htmlspecialchars($media); ?>" alt="Event media" class="media-item">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($media_paths)): ?>
                        <div class="event-media">
                            <img src="<?php echo htmlspecialchars($media_paths[0]); ?>" alt="Event poster" class="event-poster">
                        </div>
                    <?php endif; ?>
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
        function confirmRegistration() {
            return confirm("Your registering for this! Don't even think of ghosting");
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>