<?php
session_start();
include "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms & Conditions - Eventra</title>
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

        .terms-content {
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

        .terms-content h3 {
            color: var(--black);
            margin: 1.5rem 0 0.5rem;
            font-size: 14px;
            font-weight: bold;
        }

        .terms-content p {
            margin-bottom: 0.8rem;
            font-size: 14px;
        }

        .terms-content ul {
            margin-left: 1.5rem;
            margin-bottom: 0.8rem;
            font-size: 14px;
        }

        .terms-content li {
            margin-bottom: 0.3rem;
            font-size: 14px;
        }

        .terms-content a {
            color: var(--black);
            font-size: 14px;
            text-decoration: none;
            
        }

        .terms-content a:hover {
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .content-wrapper {
                padding: 5rem 1rem 2rem;
            }
            
            .terms-content {
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
    <h2 class="section-title">Terms and Conditions</h2>

    <div class="terms-content">
        <p><strong>Effective Date:</strong> June 13, 2025<br>
        <strong>Last Updated:</strong> June 13, 2025<br>
        <strong>Version:</strong> 1.0</p>

        <h3>Introduction</h3>
        <p>Welcome to <strong>Eventra</strong>, a student-initiated event management platform designed to facilitate event creation, registration, and attendance tracking. These Terms and Conditions govern your access to and use of the website located at <a href="#">eventra.great-site.net</a> ("Website" or "Platform").</p>
        <p>By accessing, browsing, registering, or using our Platform in any way, you confirm that you have read, understood, and agree to be bound by these Terms. If you do not agree to any of these Terms, please do not access or use the Platform.</p>

        <h3>User Eligibility</h3>
        <p>You may use Eventra only if you are:</p>
        <ul>
            <li>At least 18 years of age, or under the supervision of a legal guardian.</li>
            <li>Capable of forming a legally binding contract under applicable laws.</li>
            <li>A registered user with valid account credentials (for organizers and attendees).</li>
            <li>Acting in compliance with all applicable laws and these Terms.</li>
        </ul>
        <p>We reserve the right to refuse service, terminate accounts, remove content, or cancel events at our sole discretion.</p>

        <h3>Platform Overview</h3>
        <p>Eventra provides:</p>
        <ul>
            <li><strong>Event Organizers</strong> with tools to create and manage events</li>
            <li><strong>Attendees</strong> with the ability to discover, register for, and track event participation</li>
        </ul>
        <p>The Platform is offered as an <strong>academic demonstration</strong> of event management functionality. While it emulates standard event management systems, some limitations may apply, including restricted server performance and basic feature implementation.</p>

        <h3>User Accounts</h3>
        <h4>Registration</h4>
        <p>To use certain features, you must create an account by providing accurate and complete information. You are responsible for maintaining the confidentiality of your login credentials and all activities under your account.</p>
        <h4>Account Termination</h4>
        <p>You may delete your account at any time via your account settings. We may suspend or terminate your account if:</p>
        <ul>
            <li>You violate these Terms.</li>
            <li>You engage in illegal, abusive, or fraudulent activity.</li>
            <li>Your account is inactive for an extended period.</li>
        </ul>
        <p>Data retention and deletion policies are governed by our <a href="privacy.php">Privacy Policy</a>.</p>

        <h3>User Conduct</h3>
        <p>By using the Platform, you agree to:</p>
        <ul>
            <li>Provide accurate event and personal information.</li>
            <li>Comply with all local, national, and international laws.</li>
            <li>Not use the Platform for fraudulent, harmful, or malicious purposes.</li>
            <li>Not create events that promote illegal or prohibited activities.</li>
            <li>Not post misleading, obscene, defamatory, or abusive content.</li>
        </ul>
        <p>We reserve the right to review, moderate, or remove any content that violates our policies or applicable laws.</p>

        <h3>Event Management</h3>
        <h4>For Organizers</h4>
        <ul>
            <li>Events must comply with all applicable laws and institutional policies.</li>
            <li>Accurate event details including date, time, venue, and description must be provided.</li>
            <li>Organizers are responsible for managing event logistics and attendee expectations.</li>
            <li>Any changes to published events should be promptly communicated to registered attendees.</li>
        </ul>
        <h4>For Attendees</h4>
        <ul>
            <li>Registration for events may be binding depending on organizer policies.</li>
            <li>Attendees must provide valid contact information when registering.</li>
            <li>Attendance at events is at the attendee's own risk.</li>
            <li>Cancellation policies vary by event and are set by the organizer.</li>
        </ul>

        <h3>Intellectual Property</h3>
        <p>All content on the Platform including but not limited to text, graphics, logos, images, videos, code, and UI design is owned by Eventra or licensed to us. Users may not copy, reproduce, distribute, or create derivative works without prior written consent.</p>
        <p>Event content and materials uploaded by organizers remain their property, but by uploading them, you grant us a non-exclusive, royalty-free, worldwide license to display such content on the Platform for promotional and operational purposes.</p>

        <h3>Limitation of Liability</h3>
        <p>To the fullest extent permitted by law:</p>
        <ul>
            <li>Eventra, its creators, or hosting providers are <strong>not liable</strong> for any direct, indirect, incidental, or consequential damages arising from the use or inability to use the Platform.</li>
            <li>We do not guarantee uninterrupted service, data security, or defect-free functionality.</li>
            <li>All event participation is conducted at your own risk.</li>
        </ul>
        <p>Eventra is an <strong>educational project</strong>, and no commercial warranties or representations are made regarding event outcomes or platform performance.</p>

        <h3>Dispute Resolution</h3>
        <p>In case of any disputes between organizers and attendees:</p>
        <ul>
            <li>We encourage amicable resolution through direct communication.</li>
            <li>We may, at our discretion, provide guidance but do <strong>not serve as an arbitrator</strong> or hold legal responsibility for resolving user disputes.</li>
        </ul>
        <p>Legal proceedings, if any, must comply with the applicable laws of India and be subject to jurisdiction in <strong>Coimbatore, Tamil Nadu</strong>.</p>

        <h3>Indemnification</h3>
        <p>You agree to indemnify and hold harmless Eventra, its team members, advisors, and affiliates from and against any claims, damages, obligations, losses, liabilities, costs, or expenses (including attorney's fees) arising from:</p>
        <ul>
            <li>Your use of the Platform.</li>
            <li>Your violation of these Terms.</li>
            <li>Your infringement of third-party rights.</li>
            <li>Your organization or participation in any events.</li>
        </ul>

        <h3>Modifications to Terms</h3>
        <p>We reserve the right to modify or update these Terms at any time. Changes will be reflected with a revised "Last Updated" date at the top. Continued use of the Platform following any changes constitutes your acceptance of the updated Terms.</p>

        <h3>Termination of Platform</h3>
        <p>Eventra is an academic project with no guaranteed longevity. We reserve the right to discontinue the Platform temporarily or permanently without prior notice, particularly if hosting limitations are reached or if the project scope changes.</p>

        <h3>Contact Information</h3>
        <p>For inquiries, support, or reporting abuse, please <a href="contact.php">contact us</a> or email us at <a href="mailto:eventra@gmail.com">eventra@gmail.com</a>.</p>

        <h3>Miscellaneous</h3>
        <ul>
            <li>These Terms and the Privacy Policy collectively constitute the complete agreement between you and Eventra.</li>
            <li>If any provision is held invalid, the remaining provisions will continue to be enforceable.</li>
            <li>Our failure to enforce any right or provision shall not be deemed a waiver.</li>
        </ul>

        <p><strong>Thank you for using Eventra.</strong><br>
        We are committed to providing a reliable platform for event management and community engagement.</p>
    </div>
</div>

</body>
</html>