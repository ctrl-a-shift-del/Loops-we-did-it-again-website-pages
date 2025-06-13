<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: created_events.php");
    exit;
}

$event_id = $_GET['id'];
$admin_id = $_SESSION['admin_id'];

// Handle mark as completed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_completed'])) {
    $status_sql = "UPDATE events SET status = 'completed' WHERE id = ? AND admin_id = ?";
    $status_stmt = $conn->prepare($status_sql);
    $status_stmt->bind_param("ii", $event_id, $admin_id);
    $status_stmt->execute();
    $status_stmt->close();
    
    header("Location: adminEvent_details.php?id=" . $event_id);
    exit;
}

// Handle export
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_registrations'])) {
    $verify_sql = "SELECT admin_id, event_name FROM events WHERE id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("i", $event_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $event_data = $verify_result->fetch_assoc();
        if ($event_data['admin_id'] == $admin_id) {
            $export_sql = "SELECT u.name, u.email 
                          FROM registration_details rd
                          JOIN users u ON rd.user_id = u.id
                          WHERE rd.event_id = ?
                          ORDER BY rd.id";
            $export_stmt = $conn->prepare($export_sql);
            $export_stmt->bind_param("i", $event_id);
            $export_stmt->execute();
            $result = $export_stmt->get_result();
            
            $output = fopen('php://output', 'w');
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $event_data['event_name']) . '_registrations.csv"');
            
            fputcsv($output, array('No.', 'Name', 'Email'));
            
            $counter = 1;
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, array(
                    $counter,
                    $row['name'],
                    $row['email']
                ));
                $counter++;
            }
            
            fclose($output);
            exit;
        }
    }
    header("Location: adminEvent_details.php?id=".$event_id);
    exit;
}

// Fetch event details
$event = [];
$is_creator = false;

$sql = "SELECT * FROM events WHERE id = ? AND admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $event_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
    $is_creator = true;
} else {
    $check_sql = "SELECT * FROM events WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $event_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $event = $check_result->fetch_assoc();
    } else {
        header("Location: created_events.php");
        exit;
    }
    $check_stmt->close();
}
$stmt->close();

$reg_count = $event['registered_count'];
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

        /* Action buttons container - vertical by default */
        .action-buttons-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .action-buttons-container form {
            width: 100%;
        }

        .action-button,
        .delete-button,
        .status-button,
        .export-button {
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
        }

        .action-button:hover,
        .delete-button:hover,
        .status-button:hover,
        .export-button:hover {
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

            /* Horizontal layout for action buttons on desktop */
            .action-buttons-container {
                flex-direction: row;
                justify-content: center;
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .action-buttons-container form {
                flex: 1;
                min-width: 200px;
                max-width: 300px;
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
        .delete-button.hidden{
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <h1>Eventra</h1>
        </div>
        <div class="header-right">
            <button class="back-button" onclick="window.location.href='admin_home.php'">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>
    </header>

    <div class="content-wrapper">
        <?php if (isset($error)): ?>
        <div class="error-message" style="color: red; padding: 10px; background: rgba(255,255,255,0.7); border-radius: 5px; margin-bottom: 10px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="section-box">
            <div class="event-container">
                <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
                
                <div class="event-content">
                    <div class="event-details">
                        <p><span class="info-label">Status:</span> 
                            <span style="color: black; font-weight: bold;">
                                <?php echo strtoupper($event['status']); ?>
                            </span>
                        </p>
                        <p><span class="info-label">Attendee's Headcount:</span> <?php echo $reg_count; ?></p>
                        <p><span class="info-label">Date:</span> 
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

        <?php if ($is_creator): ?>
        <div class="section-box">
            <h2>Event Control Panel</h2>
            <div class="action-buttons-container">
                <?php if ($event['status'] == 'upcoming'): ?>
                    <form method="POST">
                        <button type="submit" name="mark_completed" class="status-button">
                            Mark as Completed
                        </button>
                    </form>
                <?php endif; ?>
                
                <?php if ($reg_count > 0): ?>
                    <form method="POST">
                        <button type="submit" name="export_registrations" class="export-button">
                            Export RSVP's
                        </button>
                    </form>
                <?php endif; ?>
                
                <form action="edit_event.php" method="GET">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($event_id); ?>">
                    <button type="submit" class="action-button">Edit Event</button>
                </form>
                
                <form action="delete_event.php" method="POST" id="deleteForm">
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
                    <button type="button" class="delete-button" onclick="confirmDelete()">
                        Delete Event
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
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
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                // Hide button immediately
                document.querySelector('.delete-button').classList.add('hidden');
                
                // Submit form
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>