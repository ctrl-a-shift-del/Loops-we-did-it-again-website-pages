<?php
session_start();
include "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - Eventra</title>
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

        html, body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: url('background7.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--black);
            display: flex;
            flex-direction: column;
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
            padding: 4.2rem 0.5rem 2rem;
            flex: 1;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        h2.section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 0.75rem;
            text-align: center;
        }

        .about-content {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            font-size: 14px;
            line-height: 1.4;
            text-align: justify;
        }

        .about-content h3 {
            color: var(--black);
            margin: 1.5rem 0 0.5rem;
            font-size: 14px;
            font-weight: bold;
        }

        .about-content p {
            margin-bottom: 0.8rem;
            font-size: 14px;
        }

        .about-content ul {
            margin-left: 1.5rem;
            margin-bottom: 0.8rem;
            font-size: 14px;
        }

        .about-content li {
            margin-bottom: 0.3rem;
            font-size: 14px;
        }

        .about-content a {
            color: var(--black);
            font-size: 14px;
            text-decoration: none;
        }

        .about-content a:hover {
            font-weight: bold;
        }

        .mission-statement {
            font-style: italic;
            font-size: 14px;
            color: var(--black);
            text-align: center;
            margin: 1rem 0;
            padding: 1rem;
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 3px solid var(--black);
        }

        .highlight-box {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 3px solid var(--black);
            padding: 1rem;
            margin: 1rem 0;
        }

        @media (max-width: 600px) {
            .content-wrapper {
                padding: 5rem 1rem 2rem;
            }
            
            .about-content {
                padding: 1rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-left">
        <h1>Eventra</h1>
    </div>
    <button class="back-button" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i>
    </button>
</header>

<div class="content-wrapper">
    <h2 class="section-title">About Eventra</h2>

    <div class="about-content">
        <h3 style="text-align: center;">Transforming Event Management Through Technology</h3>

        <h3>Who We Are</h3>
        <p>Eventra is more than just an event platform - it's a comprehensive solution designed to revolutionize how events are created, managed, and experienced. Born from a vision to simplify event coordination, Eventra was independently developed to bridge the gap between event organizers and attendees through intuitive technology.</p>
        <p>What began as an academic project has evolved into a powerful event management system, now trusted by clubs, organizations, and institutions to streamline their event processes and enhance participant engagement.</p>

        <h3>What We Do</h3>
        <p>Eventra is a complete event management ecosystem that connects <strong>organizers</strong> with <strong>attendees</strong> through seamless digital experiences. We provide tools for event creation, registration management, attendance tracking, and participant communication - all within a user-friendly interface designed for both desktop and mobile use.</p>
        <p>Our platform eliminates the technical barriers that often complicate event planning, allowing organizers to focus on what truly matters - creating memorable experiences.</p>

        <h3>Our Mission</h3>
        <p class="mission-statement">"To empower event organizers with intuitive digital tools and connect communities through seamless event experiences that inspire participation and foster meaningful connections."</p>

        <h3>Our Story</h3>
        <p>The event management landscape was fragmented - organizers struggled with multiple tools for registration, communication, and attendance tracking, while attendees faced cumbersome sign-up processes and lack of centralized information.</p>
        <p>Recognizing these challenges, our team set out to build a unified solution that was:</p>
        <ul>
            <li><strong>Simple</strong> (intuitive interface for all users)</li>
            <li><strong>Scalable</strong> (handles events of all sizes)</li>
            <li><strong>Accessible</strong> (works across devices and technical skill levels)</li>
        </ul>
        <p>We developed Eventra from the ground up using PHP and MySQL, focusing on core event management needs while maintaining flexibility for future enhancements. What started as a classroom concept has become a fully-functional platform recognized for its clean design and practical approach to event coordination.</p>

        <h3>Why Eventra?</h3>
        <div class="highlight-box">
            <p><strong>For Organizers:</strong></p>
            <ul>
                <li>Comprehensive event creation and management tools</li>
                <li>Real-time registration tracking and attendance monitoring</li>
                <li>Customizable event pages with media uploads</li>
                
            </ul>
        </div>
        <div class="highlight-box">
            <p><strong>For Attendees:</strong></p>
            <ul>
                <li>Easy discovery and registration for events</li>
                <li>Personalized event recommendations</li>
                <li>Simple check-in process</li>
                <li>Centralized event history</li>
            </ul>
        </div>

        <h3>Core Values</h3>
        <ul>
            <li><strong>Innovation</strong>: We continuously improve our platform to meet evolving event needs</li>
            <li><strong>Reliability</strong>: Our systems are built for stability and performance</li>
            <li><strong>Accessibility</strong>: We design for all users, regardless of technical expertise</li>
            <li><strong>Community</strong>: We believe events should bring people together</li>
            <li><strong>Transparency</strong>: Clear policies and open communication guide our work</li>
        </ul>

        <h3>Built With</h3>
        <ul>
            <li><strong>Frontend</strong>: Responsive HTML, CSS, and JavaScript</li>
            <li><strong>Backend</strong>: PHP and MySQL database</li>
            <li><strong>Security</strong>: Password hashing, input validation, and session protection</li>
            <li><strong>User Experience</strong>: Intuitive interfaces designed for both organizers and attendees</li>
            <li><strong>Future Plans</strong>: Gamification using rewards and points, Mobile apps, payment integrations, and advanced analytics</li>
        </ul>

        <h3>What's Next</h3>
        <p>We're committed to evolving Eventra to better serve our users. Upcoming developments include:</p>
        <li>
            <li>Enhanced event promotion tools</li>
            <li>Integrated payment processing</li>
            <li>Advanced reporting and analytics</li>
            <li>Mobile applications for on-the-go access</li>
            <li>API integrations with other campus systems</li>
            <li>Gamification using points and rewards</li>
            <li>Digital event reminders and updates</li>
            <li>Data-driven insights into event performance</li>
        </ul>

        <h3>Join the Eventra Community</h3>
        <p>Whether you're organizing your first event or managing a full calendar, Eventra is here to simplify the process. <a href="contact.php">Contact us</a> to learn more or get started today.</p>
        <p>Follow our journey as we continue to redefine event management - one seamless experience at a time.</p>
    </div>
</div>

</body>
</html>