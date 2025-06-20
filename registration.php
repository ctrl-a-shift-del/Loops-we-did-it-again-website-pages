<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's registrations
$registrations = [];
$sql = "SELECT e.id, e.event_name, e.start_date, e.end_date, e.start_time, e.end_time, e.venue, r.status, r.registration_date
        FROM events e
        JOIN registrations r ON e.id = r.event_id
        WHERE r.user_id = ?
        ORDER BY e.start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $registrations[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Registrations - Eventra</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #1e1e1e;
            color: #333333;
        }

        /* Sidebar styling */
        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #212121; /* Same as before */
            color: #fff;
            padding: 20px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar h1 {
            font-size: 30px;
            margin-bottom: 40px;
            font-weight: bold;
            text-align: center;
            color: #00e5ff;
            cursor: pointer;
            transition: color 0.3s ease, transform 0.6s ease-in-out;
            opacity: 0;
            transform: scale(0.8);
        }

        .sidebar h1:hover {
            color: #80d5ff;
        }

        @keyframes scaleUp {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .sidebar h1 {
            animation: scaleUp 0.6s ease-out forwards;
        }

        .sidebar button {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            background-color: #424242;
            border: none;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s ease, box-shadow 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Sidebar button hover colors */
        .sidebar button:nth-child(1):hover {
            background-color: #4285F4; /* Blue */
        }

        .sidebar button:nth-child(2):hover {
            background-color: #EA4335; /* Red */
        }

        .sidebar button:nth-child(3):hover {
            background-color: #FBBC05; /* Yellow */
        }

        .sidebar button:nth-child(4):hover {
            background-color: #34A853; /* Green */
        }

        .sidebar .material-icons {
            font-size: 20px;
        }

        /* Main content container */
        .main-content {
            flex: 1;
            margin-left: 10px;
            margin-right: 10px;
            padding: 0px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            overflow-x: auto;
            height: 100vh;
            background-color: #1e1e1e; /* Darker background */
            color: #e0e0e0;
        }

        /* Registration container for horizontal scrolling */
        .registration-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
            padding: 10px 0;
            width: 100%;
            overflow-x: auto;
            box-sizing: border-box;
        }

        /* Registration box styling */
        .registration-box {
            width: 500px;
            height: 100%;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            min-width: 350px;
            color: #333333;
            transition: all 0.3s ease;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .registration-box:hover {
            background-color: #f1f1f1;
            transform: translateY(-5px);
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Background box for text */
        .registration-box .text-background {
            background-color: rgba(0, 0, 0, 0.5); /* Dark background for text */
            padding: 15px;
            border-radius: 8px;
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            color: white;
        }

        .registration-box .text-background .registration-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .registration-box .text-background .registration-details {
            font-size: 14px;
        }
        .status-attended {
            color: #4CAF50;
        }
        .status-cancelled {
            color: #f44336;
        }
        .status-registered {
            color: #2196F3;
        }

        /* Ensure horizontal scrolling works smoothly on mouse scroll */
        .main-content {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .main-content::-webkit-scrollbar {
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 160px;
            }

            .registration-box {
                width: 100%;
            }
        }
    </style>

</head>
<body>
    <div class="sidebar">
        <h1 onclick="window.location.href='home.php'"><span style="color: #4285F4;">E</span><span style="color: #EA4335;">v</span><span style="color: #FBBC05;">è</span><span style="color: #34A853;">n</span><span style="color: #EA4335;">t</span><span style="color: #FBBC05;">r</span><span style="color: #4285F4;">a</span></h1>
        <button onclick="window.location.href='registration.php'"><span class="material-icons">event_note</span>My Registrations</button>
        <button onclick="window.location.href='leaderboard.php'"><span class="material-icons">leaderboard</span>Leaderboard</button>
        <button onclick="window.location.href='profile.php'"><span class="material-icons">person</span>Profile</button>
    </div>

    <div class="main-content">
        <div class="registration-container">
            <?php if (empty($registrations)): ?>
                <div style="color: white; padding: 20px; text-align: center;">
                    You haven't registered for any events yet.
                </div>
            <?php else: ?>
                <?php foreach ($registrations as $registration): ?>
                    <?php 
                    $status_class = '';
                    if ($registration['status'] == 'attended') {
                        $status_class = 'status-attended';
                    } elseif ($registration['status'] == 'cancelled') {
                        $status_class = 'status-cancelled';
                    }
                    
                    $event_date = date('d/m/Y', strtotime($registration['start_date']));
                    if ($registration['start_date'] != $registration['end_date']) {
                        $event_date .= ' to ' . date('d/m/Y', strtotime($registration['end_date']));
                    }
                    
                    $event_time = date('h:i A', strtotime($registration['start_time'])) . ' to ' . 
                                  date('h:i A', strtotime($registration['end_time']));
                    ?>
                    <div class="registration-box" onclick="window.location.href='event_details.php?id=<?php echo $registration['id']; ?>'">
                        <div class="text-background">
                            <span class="material-icons">event</span>
                            <div class="registration-title"><?php echo htmlspecialchars($registration['event_name']); ?></div>
                            <div class="registration-details">Date: <?php echo $event_date; ?></div>
                            <div class="registration-details">Time: <?php echo $event_time; ?></div>
                            <div class="registration-details">Venue: <?php echo htmlspecialchars($registration['venue']); ?></div>
                            <div class="registration-details <?php echo $status_class; ?>">Status: <?php echo ucfirst($registration['status']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>