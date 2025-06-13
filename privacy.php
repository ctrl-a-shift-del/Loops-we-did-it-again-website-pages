<?php
session_start();
include "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy - Eventra</title>
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
            padding: 4rem 0.5rem 2rem;
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

        .privacy-content {
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

        .privacy-content h3 {
            color: var(--black);
            margin: 1.5rem 0 0.5rem;
            font-size: 14px;
            font-weight: bold;
        }

        .privacy-content p {
            margin-bottom: 0.8rem;
            font-size: 14px;
        }

        .privacy-content ul {
            margin-left: 1.5rem;
            margin-bottom: 0.8rem;
            font-size: 14px;
        }

        .privacy-content li {
            margin-bottom: 0.3rem;
            font-size: 14px;
        }

        .privacy-content a {
            color: var(--black);
            font-size: 14px;
            text-decoration: none;
        }

        .privacy-content a:hover {
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .content-wrapper {
                padding: 5rem 1rem 2rem;
            }
            
            .privacy-content {
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
    <h2 class="section-title">Privacy Policy</h2>

    <div class="privacy-content">
        <p><strong>Effective Date:</strong> June 13, 2025<br>
        <strong>Last Updated:</strong> June 13, 2025<br>
        <strong>Version:</strong> 1.0</p>

        <h3>Introduction</h3>
        <p>This Privacy Policy outlines how Eventra collects, uses, discloses, and protects the personal information of users who interact with our platform. Eventra is an event management system designed for users to create, manage, and register for events efficiently. By using the platform, you consent to the practices described in this policy.</p>

        <h3>Scope</h3>
        <p>This policy applies to all users accessing Eventra via desktop, mobile, or other supported devices. It covers all interactions such as account creation, event participation, and content submissions. It does not extend to third-party websites linked through our platform.</p>

        <h3>Information We Collect</h3>
        <p><strong>Information You Provide:</strong></p>
        <ul>
            <li><strong>Account Details:</strong> Full name, email, and password (hashed)</li>
            <li><strong>Event Data:</strong> Information about events you create, register for, or manage</li>
            <li><strong>Contact Submissions:</strong> Feedback, queries, and other communications sent manually.</li>
        </ul>
        <p><strong>Automatically Collected Data:</strong></p>
        <ul>
            <li>IP address, browser type, OS, and general device info</li>
            <li>Pages visited, time spent, interactions, and referral links</li>
            <li>Technical logs for system performance and troubleshooting</li>
        </ul>

        <h3>Cookies and Tracking</h3>
        <p>We use essential session-based cookies to maintain user sessions, improve security, and support platform functionality. These cookies do not store personal identifiers and are not used for ads. You may disable cookies via your browser, but this may limit platform features.</p>

        <h3>How We Use Your Data</h3>
        <p>We process collected data to:</p>
        <ul>
            <li>Create and manage user accounts</li>
            <li>Allow users to create, explore, and register for events</li>
            <li>Send relevant event updates and support responses</li>
            <li>Secure our systems and prevent abuse</li>
            <li>Maintain performance and improve the user experience</li>
        </ul>

        <h3>Data Sharing</h3>
        <p>We do not sell your data. Limited data may be shared with:</p>
        <ul>
            <li><strong>Event Organizers:</strong> For managing registrations</li>
            <li><strong>Service Providers:</strong> For database hosting and security</li>
            <li><strong>Legal Authorities:</strong> When required by applicable law</li>
        </ul>

        <h3>Data Storage & Retention</h3>
        <p>All user data is stored in our secured MySQL database, accessible only through authenticated backend processes. Account data is retained until deletion is requested. Event logs and activity records are stored for technical analysis and compliance for up to 12 months unless legally required for longer periods.</p>

        <h3>Your Rights</h3>
        <p>You have the right to:</p>
        <ul>
            <li>Access your stored data</li>
            <li>Request corrections or updates</li>
            <li>Request deletion of your account and data</li>
            <li>Withdraw previously given consent</li>
        </ul>
        <p>To exercise these rights, contact us using the link at the bottom of this page. We may verify your identity before processing your request.</p>

        <h3>Children's Privacy</h3>
        <p>Eventra is not designed for users under the age of 13. We do not knowingly collect data from children. If we discover such data was collected without parental consent, we will delete it promptly.</p>

        <h3>Security Measures</h3>
        <p>We implement safeguards such as:</p>
        <ul>
            <li>Secure database access</li>
            <li>Hashed password storage</li>
            <li>Limited backend access control</li>
            <li>Ongoing code and infrastructure checks</li>
        </ul>
        <p>Despite best efforts, no system is fully immune to risks. We recommend you use strong passwords and keep them confidential.</p>

        <h3>Updates to This Policy</h3>
        <p>We may revise this policy as needed. Any updates will be posted here with a revised "Last Updated" date. Continued use of the platform after changes implies your agreement to the new terms.</p>

        <h3>Contact Us</h3>
        <p>If you have questions or concerns about this Privacy Policy, please <a href="contact.php">contact us</a>.</p>
    </div>


</div>



</body>
</html>