<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Determine the filter (default to 'yearly')
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'yearly';

// Fetch leaderboard data based on filter
$leaderboard = [];
$sql = "SELECT 
            u.id, 
            u.name, 
            COUNT(r.id) as events_participated
        FROM 
            users u
        LEFT JOIN registrations r ON u.id = r.user_id AND (r.status = 'attended' OR r.status = 'registered')
        LEFT JOIN 
            events e ON r.event_id = e.id";

// Add date condition based on filter
switch ($filter) {
    case 'weekly':
        $sql .= " WHERE YEARWEEK(e.start_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $sql .= " WHERE YEAR(e.start_date) = YEAR(CURDATE()) AND MONTH(e.start_date) = MONTH(CURDATE())";
        break;
    case 'yearly':
    default:
        $sql .= " WHERE YEAR(e.start_date) = YEAR(CURDATE())";
        break;
}

$sql .= " GROUP BY u.id
          ORDER BY events_participated DESC
          LIMIT 100";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $leaderboard[] = $row;
}

// Fetch user's position
$user_position = 0;
$sql = "SELECT position FROM (
          SELECT 
              u.id, 
              ROW_NUMBER() OVER (ORDER BY COUNT(r.id) DESC) as position
          FROM 
              users u
          LEFT JOIN registrations r ON u.id = r.user_id AND (r.status = 'attended' OR r.status = 'registered')
          LEFT JOIN 
              events e ON r.event_id = e.id";

// Add date condition based on filter
switch ($filter) {
    case 'weekly':
        $sql .= " WHERE YEARWEEK(e.start_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $sql .= " WHERE YEAR(e.start_date) = YEAR(CURDATE()) AND MONTH(e.start_date) = MONTH(CURDATE())";
        break;
    case 'yearly':
    default:
        $sql .= " WHERE YEAR(e.start_date) = YEAR(CURDATE())";
        break;
}

$sql .= " GROUP BY u.id
        ) as ranked_users
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $user_position = $result->fetch_assoc()['position'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Eventra</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --black: #000000;
            --white: #ffffff;
            --primary: #00e5ff;
            --secondary: #383636;
            --highlight: rgba(0, 229, 255, 0.2);
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
            flex: 1;
        }

        .section-box {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 1000px;
            max-width: calc(100% - 1rem);
        }

        .section-box h2 {
            font-size: 20px;
            color: var(--black);
            margin-bottom: 1rem;
            text-align: center;
        }

        .leaderboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .filter-dropdown {
            position: relative;
            display: inline-block;
        }

        .filter-btn {
            background: transparent;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 400;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
        }

        .filter-btn:hover {
            font-weight: bold;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: url('backround7.jpg') no-repeat center center fixed;
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            min-width: 90px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            font-size: 14px;
        }

        .dropdown-content a {
            color: var(--black);
            padding: 0.5rem 1rem;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            font-weight: bold;
        }

        .filter-dropdown:hover .dropdown-content {
            display: block;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .leaderboard-table th {
            background-color: rgba(0, 0, 0, 0.1);
            padding: 0.75rem;
            text-align: left;
            font-size: 15px;
            color: var(--black);
        }

        .leaderboard-table td {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 15px;
        }

        .leaderboard-table tr:last-child td {
            border-bottom: none;
        }

        .leaderboard-table tr:hover {
            font-weight: bold;
        }

        .highlight {
            font-weight: bold;
        }

        .rank {
            font-weight: bold;
            color: black;
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

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .modal-content h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--black);
        }

        .modal-content p {
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .close-btn {
            background-color: var(--black);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
            cursor: pointer;
            font-weight: bold;
        }

        .close-btn:hover {
            transform: scale(0.97);
        }

        @media (max-width: 768px) {
            .leaderboard-table th, 
            .leaderboard-table td {
                padding: 0.5rem;
                font-size: 12px;
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
            <div class="leaderboard-header">
                <h2>Overachievers' Lounge</h2>
                <div class="filter-dropdown">
                    <button class="filter-btn">
                        <i class="fas fa-filter"></i>
                        <?php echo ucfirst($filter); ?>
                    </button>
                    <div class="dropdown-content">
                        <a href="?filter=weekly">Weekly</a>
                        <a href="?filter=monthly">Monthly</a>
                        <a href="?filter=yearly">Yearly</a>
                    </div>
                </div>
            </div>
            
            <!-- Leaderboard Table -->
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>People Who Showed Up</th>
                        <th>Event Addiction Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leaderboard)): ?>
                        <?php foreach ($leaderboard as $index => $participant): ?>
                            <tr class="<?php echo $participant['id'] == $user_id ? 'highlight' : ''; ?>">
                                <td class="rank"><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($participant['name']); ?></td>
                                <td><?php echo $participant['events_participated']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No participants found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

</body>
</html>
<?php $conn->close(); ?>