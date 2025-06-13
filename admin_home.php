<?php
session_start();
require 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$clubname = $_SESSION['clubname'];

// Get current date for filtering upcoming events
$current_date = date('Y-m-d');

// Get events for each category
// 1. My Events
$my_events_query = "SELECT * FROM events WHERE admin_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($my_events_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$my_events = [];
while ($row = $result->fetch_assoc()) {
    $media_paths = json_decode($row['media_paths'] ?? '[]', true);
    $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
    $my_events[] = $row;
}
$stmt->close();

// 2. Recommended Events
$recommended_query = "SELECT * FROM events WHERE end_date >= ? ORDER BY RAND() LIMIT 10";
$stmt = $conn->prepare($recommended_query);
$stmt->bind_param("s", $current_date);
$stmt->execute();
$result = $stmt->get_result();
$recommended_events = [];
while ($row = $result->fetch_assoc()) {
    $media_paths = json_decode($row['media_paths'] ?? '[]', true);
    $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
    $recommended_events[] = $row;
}
$stmt->close();

// 3. Trending Events
$trending_query = "
    SELECT e.*, COUNT(r.id) as registration_count 
    FROM events e
    LEFT JOIN registrations r ON e.id = r.event_id
    WHERE e.end_date >= ? AND e.status != 'completed'
    GROUP BY e.id
    ORDER BY registration_count DESC
";
$stmt = $conn->prepare($trending_query);
$stmt->bind_param("s", $current_date);
$stmt->execute();
$result = $stmt->get_result();
$trending_events = [];
while ($row = $result->fetch_assoc()) {
    $media_paths = json_decode($row['media_paths'] ?? '[]', true);
    $row['image_url'] = !empty($media_paths) ? $media_paths[0] : null;
    $trending_events[] = $row;
}
$stmt->close();

// 4. New Events
$new_query = "SELECT * FROM events WHERE end_date >= ? AND status != 'completed' ORDER BY created_at DESC";
$stmt = $conn->prepare($new_query);
$stmt->bind_param("s", $current_date);
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
    <title>Admin Home - Eventra</title>
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
            overscroll-behavior-y: none; /* Prevent weird bounce */
            -webkit-overflow-scrolling: touch;
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

        .event-image-container {
            width: 100%;
            height: 90%;
            overflow: hidden;
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
        }
    </style>
</head>
<body>

<!-- Slide-out menu -->
<div class="slide-menu" id="menu">
    <button class="close-btn" onclick="toggleMenu()">×</button>
    <a href="create_event.php">Create Event</a>
    <a href="clubprofile.php">Club Profile</a>
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
    
    <!-- Trending Events Section -->
    <h2 class="section-title">Trending Now</h2>
    <div class="events-container">
        <div class="events-scroll">
            <?php if (empty($trending_events)): ?>
                <div class="no-events">Nothing hot right now... Not even tea.</div>
            <?php else: ?>
                <?php foreach ($trending_events as $event): ?>
                    <div class="event-box" onclick="window.location.href='adminevent_details.php?id=<?= $event['id'] ?>'">
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
    <h2 class="section-title">New One's Here</h2>
    <div class="events-container">
        <div class="events-scroll">
            <?php if (empty($new_events)): ?>
                <div class="no-events">If there are new events, you'd see them. Spoiler: There's none.</div>
            <?php else: ?>
                <?php foreach ($new_events as $event): ?>
                    <div class="event-box" onclick="window.location.href='adminevent_details.php?id=<?= $event['id'] ?>'">
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

    <!-- My Events Section -->
    <h2 class="section-title">The Club's Events</h2>
    <div class="events-container">
        <div class="events-scroll">
            <?php if (empty($my_events)): ?>
                <div class="no-events">An event manager with no events? Bold move!</div>
            <?php else: ?>
                <?php foreach ($my_events as $event): ?>
                    <div class="event-box" onclick="window.location.href='adminevent_details.php?id=<?= $event['id'] ?>'">
                        

                        <div class="event-image-container" style="position: relative;">
                            <?php if (!empty($event['image_url'])): ?>
                                <img src="<?= $event['image_url'] ?>" alt="<?= $event['event_name'] ?>" class="event-image">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;background-color:#ddd;">
                                    <span style="color:var(--black);">No Image Available</span>
                                </div>
                            <?php endif; ?>
                            <?php
                                $is_completed = $event['status'] === 'completed' || strtotime($event['end_date']) < strtotime(date('Y-m-d'));
                                if ($is_completed):
                            ?>
                                <div style="position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.5);z-index:2;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:1.2rem;">
                                    COMPLETED
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
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="privacy.php">Privacy</a>
        <a href="terms.php">Terms</a>
    </div>
</footer>

<script>

    // Auto-slide functionality for event rows
    function setupAutoSlide() {
        const containers = document.querySelectorAll('.events-container');
        
        containers.forEach(container => {
            // Only set up auto-slide if there are events
            if (container.querySelector('.event-box')) {
                // Random initial delay between 3-8 seconds
                const initialDelay = Math.random() * 5000 + 3000;                
                setTimeout(() => {
                    startAutoSlide(container);
                }, initialDelay);
            }
        });
    }
    
    function startAutoSlide(container) {
        const scrollContent = container.querySelector('.events-scroll');
        const cards = container.querySelectorAll('.event-box');
        const cardWidth = cards[0].offsetWidth + 16; // Include margin
        const visibleCards = Math.floor(container.offsetWidth / cardWidth);
        const totalCards = cards.length;
        
        // Only slide if there are more cards than visible
        if (totalCards > visibleCards) {
            const currentScroll = container.scrollLeft;
            const maxScroll = scrollContent.scrollWidth - container.offsetWidth;
            
            // Calculate next scroll position
            let nextScroll;
            if (currentScroll >= maxScroll - 10) { // If at or near end
                nextScroll = 0; // Return to start
            } else {
                // Scroll by approximately one screen of cards
                nextScroll = Math.min(currentScroll + (visibleCards * cardWidth), maxScroll);
            }
            
            // Smooth scroll to next position
            container.scrollTo({
                left: nextScroll,
                behavior: 'smooth'
            });
            
            // Set random delay for next slide (4-8 seconds)
            const nextDelay = 4000;
            
            setTimeout(() => {
                startAutoSlide(container);
            }, nextDelay);
        }
    }
    
    // Initialize auto-slide when page loads
    window.addEventListener('load', () => {
        setupAutoSlide();
        
        // Also keep the existing centerCards functionality for desktop
        if (window.innerWidth >= 768) {
            centerCards();
        }
    });

    // ... (keep all your existing JavaScript code) ...


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

    // Mobile swipe functionality for all event containers
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
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });

        // Touch events for mobile
        container.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('touchend', () => {
            isDown = false;
        });

        container.addEventListener('touchmove', (e) => {
            if(!isDown) return;
            const x = e.touches[0].pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });
    });

    // Auto-scroll to center the nearest card on desktop
    function centerCards() {
        if (window.innerWidth >= 768) {
            document.querySelectorAll('.events-container').forEach(container => {
                const cards = container.querySelectorAll('.event-box');
                if (cards.length === 0) return;
                
                const cardWidth = cards[0].offsetWidth + 16;
                const scrollPos = container.scrollLeft;
                const centerPos = scrollPos + (container.offsetWidth / 2);
                
                let closestCard = null;
                let closestDistance = Infinity;
                
                cards.forEach(card => {
                    const cardPos = card.offsetLeft + (card.offsetWidth / 2);
                    const distance = Math.abs(cardPos - centerPos);
                    
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closestCard = card;
                    }
                });
                
                if (closestCard) {
                    const targetScroll = closestCard.offsetLeft - (container.offsetWidth / 2) + (closestCard.offsetWidth / 2);
                    container.scrollTo({
                        left: targetScroll,
                        behavior: 'smooth'
                    });
                }
            });
        }
    }

    // Add scroll end listener for desktop
    document.querySelectorAll('.events-container').forEach(container => {
        container.addEventListener('scroll', () => {
            clearTimeout(container.scrollEndTimer);
            container.scrollEndTimer = setTimeout(centerCards, 100);
        });
    });

    // Initialize centered scroll on load
    window.addEventListener('load', () => {
        if (window.innerWidth >= 768) {
            centerCards();
        }
    });
</script>

</body>
</html>
<?php $conn->close(); ?>