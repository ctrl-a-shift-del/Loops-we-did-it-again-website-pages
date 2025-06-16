<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Function to check if event is in the past (date and time)
function isEventPast($event) {
    global $current_date, $current_time;
    if ($event['end_date'] < $current_date) {
        return true;
    } elseif ($event['end_date'] == $current_date && $event['end_time'] < $current_time) {
        return true;
    }
    return false;
}

// 4. My Registrations (events the user registered for - both upcoming and completed)
$my_registrations_query = "
    SELECT e.* 
    FROM events e
    JOIN registrations r ON e.id = r.event_id
    WHERE r.user_id = ? 
    ORDER BY r.registration_date DESC
";
$stmt = $conn->prepare($my_registrations_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$my_registrations = [];
while ($row = $result->fetch_assoc()) {
    $media_paths = json_decode($row['media_paths'] ?? '[]', true);
    $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
    $row['is_past'] = isEventPast($row);
    $my_registrations[] = $row;
}
$stmt->close();

// 1. Recommended Events (events from clubs the user has registered with before)
// First get clubs the user has registered with
$user_clubs_query = "
    SELECT DISTINCT e.clubname 
    FROM events e
    JOIN registrations r ON e.id = r.event_id
    WHERE r.user_id = ?
";
$stmt = $conn->prepare($user_clubs_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_clubs = [];
while ($row = $result->fetch_assoc()) {
    $user_clubs[] = $row['clubname'];
}
$stmt->close();

$recommended_events = [];
$club_list = "''"; // Initialize with empty string to avoid undefined variable error

// If user has registered with clubs before, get their upcoming events
if (!empty($user_clubs)) {
    $club_list = "'" . implode("','", $user_clubs) . "'";
    $club_based_query = "
        SELECT e.* 
        FROM events e
        WHERE e.clubname IN ($club_list)
        AND e.end_date >= ?
        AND e.status = 'upcoming'
        AND e.id NOT IN (
            SELECT r.event_id 
            FROM registrations r 
            WHERE r.user_id = ?
        )
        AND (e.end_date > ? OR (e.end_date = ? AND e.end_time >= ?))
        ORDER BY e.created_at DESC
        LIMIT 10
    ";
    $stmt = $conn->prepare($club_based_query);
    $stmt->bind_param("sisss", $current_date, $user_id, $current_date, $current_date, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $media_paths = json_decode($row['media_paths'] ?? '[]', true);
        $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
        $recommended_events[] = $row;
    }
    $stmt->close();
}

// Fill remaining slots with random upcoming events if needed
// Append other trending events (not from user's clubs) to recommended list
$other_events_query = "
    SELECT e.*, COUNT(r.id) AS registration_count
    FROM events e
    LEFT JOIN registrations r ON e.id = r.event_id
    WHERE e.clubname NOT IN ($club_list)
    AND e.status = 'upcoming'
    AND e.end_date >= ?
    AND (e.end_date > ? OR (e.end_date = ? AND e.end_time >= ?))
    AND e.id NOT IN (
        SELECT event_id 
        FROM registrations 
        WHERE user_id = ?
    )
    GROUP BY e.id
    ORDER BY registration_count DESC
";
$stmt = $conn->prepare($other_events_query);
$stmt->bind_param("ssssi", $current_date, $current_date, $current_date, $current_time, $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $media_paths = json_decode($row['media_paths'] ?? '[]', true);
    $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
    $recommended_events[] = $row;
}
$stmt->close();


// 2. Trending Events (events with most registrations)
$trending_query = "
    SELECT e.*, COUNT(r.id) as registration_count 
    FROM events e
    LEFT JOIN registrations r ON e.id = r.event_id
    WHERE e.end_date >= ?
    AND e.status = 'upcoming'
    AND e.id NOT IN (
        SELECT r.event_id 
        FROM registrations r 
        WHERE r.user_id = ?
    )
    AND (e.end_date > ? OR (e.end_date = ? AND e.end_time >= ?))
    GROUP BY e.id
    ORDER BY registration_count DESC
    LIMIT 10
";
$stmt = $conn->prepare($trending_query);
$stmt->bind_param("sisss", $current_date, $user_id, $current_date, $current_date, $current_time);
$stmt->execute();
$result = $stmt->get_result();
$trending_events = [];
while ($row = $result->fetch_assoc()) {
    $media_paths = json_decode($row['media_paths'] ?? '[]', true);
    $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
    $trending_events[] = $row;
}
$stmt->close();

// 3. New Events (recently created events)
$new_query = "
    SELECT * 
    FROM events 
    WHERE end_date >= ?
    AND status = 'upcoming'
    AND id NOT IN (
        SELECT event_id 
        FROM registrations 
        WHERE user_id = ?
    )
    AND (end_date > ? OR (end_date = ? AND end_time >= ?))
    ORDER BY created_at DESC 
    LIMIT 10
";
$stmt = $conn->prepare($new_query);
$stmt->bind_param("sisss", $current_date, $user_id, $current_date, $current_date, $current_time);
$stmt->execute();
$result = $stmt->get_result();
$new_events = [];
while ($row = $result->fetch_assoc()) {
    $media_paths = json_decode($row['media_paths'] ?? '[]', true);
    $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
    $new_events[] = $row;
}
$stmt->close();

// Format date and time functions
function formatDateRange($start_date, $end_date) {
    $start = strtotime($start_date);
    $end = strtotime($end_date);

    if ($start_date === $end_date) {
        return date('jS M Y', $start);
    }

    if (date('F Y', $start) === date('F Y', $end)) {
        return date('j', $start) . '–' . date('jS M Y', $end);
    }

    return date('jS M Y', $start) . ' – ' . date('jS M Y', $end);
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Home - Eventra</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

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
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            scroll-behavior: smooth;
            overscroll-behavior-y: none;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;          /* Firefox */
            -ms-overflow-style: none;       /* IE 10+ */
            overflow-y: scroll;
        }

        body::-webkit-scrollbar {
            display: none;
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
            min-height: 100vh;
        }

        /* Header styles */
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

        .header-left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--black);
            margin-bottom: 0rem;
            text-align: left;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        /* Menu button */
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
            transition: all 0.3s ease;
        }
        .menu-button:hover{
            transform: scale(0.97);
        }

        /* Slide menu styles */
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

        /* Close button */
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

        /* Backdrop */
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

        /* Main content */
        .content-wrapper {
            padding-top: 2.6rem;
            padding-bottom: 2rem;
            position: relative;
            z-index: 1;
            flex: 1;
        }

        /* Section titles */
        .section-title {
            color: var(--black);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 1rem 0.5rem 0.5rem;
            padding-left: 0.5rem;
        }

        /* Events container */
        .events-container {
            width: 100%;
            overflow-x: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            padding: 0rem 0.2rem;
            margin-bottom: 1rem;
            touch-action: pan-y;
            scrollbar-width: none;
        }

        .events-container::-webkit-scrollbar {
            display: none;
        }

        .events-scroll {
            display: flex;
            gap: 0.1rem;
            padding: 0 0rem;
        }

        /* Event card styles */
        .event-box {
            position: relative;
            width: 300px;
            height: 430px;
            scroll-snap-align: start;
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 0;
            transition: transform 0.3s ease;
            cursor: pointer;
            flex-shrink: 0;
            overflow: hidden;
            margin: 0 0.2rem;
        }

        @media (min-width: 768px) {
            .event-box {
                width: 300px;
            }
        }

        @media (min-width: 1024px) {
            .event-box {
                width: 300px;
            }
        }

        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-info {
            padding: 0.2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
            gap: 0rem;
        }

        .event-name {
            grid-column: 1;
            grid-row: 1;
            font-size: 13px;
            font-weight: 600;
            color: var(--black);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .event-date {
            grid-column: 2;
            grid-row: 1;
            font-size: 13px;
            color: var(--black);
            text-align: right;
        }

        .event-venue {
            grid-column: 1;
            grid-row: 2;
            font-size: 12px;
            color: var(--black);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .event-time {
            grid-column: 2;
            grid-row: 2;
            font-size: 12px;
            color: var(--black);
            text-align: right;
        }

        .event-box:hover {
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            color: var(--black);
            padding: 0.2rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            background-color:transparent;
            position: relative;
            bottom: 0;
            z-index: 100;
        }

        footer a {
            color: var(--black);
            text-decoration: none;
            margin-left: 1rem;
            font-size: 0.8rem;
        }

        footer a:hover {
            font-weight: bold;
        }

        /* No events message */
        .no-events {
            text-align: center;
            color: var(--black);
            padding: 2rem;
            font-size: 15px;
            width: 100%;
        }

        .completed-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            text-transform: uppercase;
        }
        
        .past-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            text-transform: uppercase;
        }
        
        .event-image-container {
            position: relative;
            width: 100%;
            height: 90%;
            overflow: hidden;
        }
        
        @media (max-width: 767px) {
            .events-scroll {
                padding: 0;
            }
            
            .event-box {
                margin: 0 0.2rem;
                height: 300px;
                width: 180px;
            }
            
            .event-image-container {
                height: 89%;
            }
            .event-name {
                grid-column: 1;
                grid-row: 1;
                font-size: 10px;
                font-weight: 600;
                color: var(--black);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .event-date {
                grid-column: 2;
                grid-row: 1;
                font-size: 10px;
                color: var(--black);
                text-align: right;
            }

            .event-venue {
                grid-column: 1;
                grid-row: 2;
                font-size: 10px;
                color: var(--black);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .event-time {
                grid-column: 2;
                grid-row: 2;
                font-size: 10px;
                color: var(--black);
                text-align: right;
            }
            
            .section-title {
                font-size: 1rem;
                margin: 0.8rem 0.5rem 0.3rem;
            }
            
            .completed-overlay, .past-overlay {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<!-- Slide-out menu -->
<div class="slide-menu" id="menu">
    <button class="close-btn" onclick="toggleMenu()">×</button>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="profile.php">Profile</a>
</div>

<!-- Backdrop for menu -->
<div class="backdrop" id="backdrop" onclick="toggleMenu()"></div>

<!-- Header -->
<header>
    <div class="header-left">
        <h1>Eventra</h1>
    </div>
    <div class="header-right">
        <button class="menu-button" onclick="toggleMenu()">☰</button>
    </div>
</header>

<!-- Main content area -->
<div class="content-wrapper">

    <!-- Recommended Events Section -->
    <h2 class="section-title">Tailored For You</h2>
    <div class="events-container" id="recommendedContainer">
        <div class="events-scroll">
            <?php if (empty($recommended_events)): ?>
                <div class="no-events">You've Officially Confused the Algorithm</div>
            <?php else: ?>
                <?php foreach ($recommended_events as $event): ?>
                    <div class="event-box" onclick="window.location.href='event_details.php?id=<?= $event['id'] ?>'">
                        <div class="event-image-container">
                            <?php if (!empty($event['image_url'])): ?>
                                <img src="<?= $event['image_url'] ?>" alt="<?= $event['event_name'] ?>" class="event-image">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;background-color:#ddd;">
                                    <span style="color:var(--black);">No Image Available</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="event-info">
                            <div class="event-name"><?= htmlspecialchars($event['event_name']) ?></div>
                            <div class="event-date">
                                <?= formatDateRange($event['start_date'], $event['end_date']) ?>
                            </div>
                            <div class="event-venue"><?= htmlspecialchars($event['venue']) ?></div>
                            <div class="event-time">
                                <?= formatTime($event['start_time']) ?> - <?= formatTime($event['end_time']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Trending Events Section -->
    <h2 class="section-title">What The Cool Kids Are Clicking</h2>
    <div class="events-container" id="trendingContainer">
        <div class="events-scroll">
            <?php if (empty($trending_events)): ?>
                <div class="no-events">Nothing's Hot Right Now.. Not Even Tea</div>
            <?php else: ?>
                <?php foreach ($trending_events as $event): ?>
                    <div class="event-box" onclick="window.location.href='event_details.php?id=<?= $event['id'] ?>'">
                        <div class="event-image-container">
                            <?php if (!empty($event['image_url'])): ?>
                                <img src="<?= $event['image_url'] ?>" alt="<?= $event['event_name'] ?>" class="event-image">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;background-color:#ddd;">
                                    <span style="color:var(--black);">No Image Available</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="event-info">
                            <div class="event-name"><?= htmlspecialchars($event['event_name']) ?></div>
                            <div class="event-date">
                                <?= formatDateRange($event['start_date'], $event['end_date']) ?>
                            </div>
                            <div class="event-venue"><?= htmlspecialchars($event['venue']) ?></div>
                            <div class="event-time">
                                <?= formatTime($event['start_time']) ?> - <?= formatTime($event['end_time']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- New Events Section -->
    <h2 class="section-title">Newly Dropped</h2>
    <div class="events-container" id="newContainer">
        <div class="events-scroll">
            <?php if (empty($new_events)): ?>
                <div class="no-events">No New Drama Found</div>
            <?php else: ?>
                <?php foreach ($new_events as $event): ?>
                    <div class="event-box" onclick="window.location.href='event_details.php?id=<?= $event['id'] ?>'">
                        <div class="event-image-container">
                            <?php if (!empty($event['image_url'])): ?>
                                <img src="<?= $event['image_url'] ?>" alt="<?= $event['event_name'] ?>" class="event-image">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;background-color:#ddd;">
                                    <span style="color:var(--black);">No Image Available</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="event-info">
                            <div class="event-name"><?= htmlspecialchars($event['event_name']) ?></div>
                            <div class="event-date">
                                <?= formatDateRange($event['start_date'], $event['end_date']) ?>
                            </div>
                            <div class="event-venue"><?= htmlspecialchars($event['venue']) ?></div>
                            <div class="event-time">
                                <?= formatTime($event['start_time']) ?> - <?= formatTime($event['end_time']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- My Registrations Section -->
    <h2 class="section-title">Your Registrations</h2>
    <div class="events-container" id="registrationsContainer">
        <div class="events-scroll">
            <?php if (empty($my_registrations)): ?>
                <div class="no-events">You Haven't Shown Up Anywhere</div>
            <?php else: ?>
                <?php foreach ($my_registrations as $event): ?>
                    <div class="event-box" onclick="window.location.href='event_details.php?id=<?= $event['id'] ?>'">
                        <div class="event-image-container">
                            <?php if (!empty($event['image_url'])): ?>
                                <img src="<?= $event['image_url'] ?>" alt="<?= $event['event_name'] ?>" class="event-image">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;background-color:#ddd;">
                                    <span style="color:var(--black);">No Image Available</span>
                                </div>
                            <?php endif; ?>
    
                            <?php if ($event['status'] === 'completed'): ?>
                                <div class="completed-overlay">
                                    <span>COMPLETED</span>
                                </div>
                            <?php elseif ($event['is_past']): ?>
                                <div class="past-overlay">
                                    <span>COMPLETED</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="event-info">
                            <div class="event-name"><?= htmlspecialchars($event['event_name']) ?></div>
                            <div class="event-date">
                                <?= formatDateRange($event['start_date'], $event['end_date']) ?>
                            </div>
                            <div class="event-venue"><?= htmlspecialchars($event['venue']) ?></div>
                            <div class="event-time">
                                <?= formatTime($event['start_time']) ?> - <?= formatTime($event['end_time']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div>© 2025 Eventra</div>
    <div>
        
        <a href="privacy.php">Privacy</a>
        <a href="terms.php">Terms</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </div>
</footer>

<script>
    // Toggle slide menu function
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

    // Auto-scroll products every 2 seconds for each container
    function setupAutoScroll(containerId) {
        const container = document.getElementById(containerId);
        const productBoxes = container.querySelectorAll('.event-box');
        if (productBoxes.length === 0) return;
        
        const scrollAmount = productBoxes[0].offsetWidth + 5; // Width + gap
        let currentIndex = 0;
        
        function scrollToNextSet() {
            if (container.scrollLeft + container.clientWidth >= container.scrollWidth - scrollAmount) {
                container.scrollTo({ left: 0, behavior: 'smooth' });
                currentIndex = 0;
            } else {
                currentIndex++;
                container.scrollTo({
                    left: currentIndex * scrollAmount,
                    behavior: 'smooth'
                });
            }
        }

        let scrollInterval = setInterval(scrollToNextSet, 2000);

        container.addEventListener('mouseenter', () => clearInterval(scrollInterval));
        container.addEventListener('mouseleave', () => {
            scrollInterval = setInterval(scrollToNextSet, 2000);
        });

        container.addEventListener('touchstart', () => clearInterval(scrollInterval));
        container.addEventListener('touchend', () => {
            scrollInterval = setInterval(scrollToNextSet, 2000);
        });
    }

    setTimeout(() => setupAutoScroll('recommendedContainer'), 8);
    setTimeout(() => setupAutoScroll('trendingContainer'), 1600);
    setTimeout(() => setupAutoScroll('newContainer'), 900);
    setTimeout(() => setupAutoScroll('registrationsContainer'), 1200);

    // 🔥 Add swipe/drag scrolling for mobile
    document.querySelectorAll('.events-container').forEach(container => {
        let isDown = false;
        let startX;
        let scrollLeft;

        container.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('mouseleave', () => {
            isDown = false;
        });

        container.addEventListener('mouseup', () => {
            isDown = false;
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });

        container.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('touchend', () => {
            isDown = false;
        });

        container.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });
    });
</script>



</body>
</html>
<?php $conn->close(); ?>