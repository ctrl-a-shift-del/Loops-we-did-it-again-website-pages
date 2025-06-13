<?php
session_start();
include "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - Eventra</title>
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
            text-align: center;
            padding: 5rem 1rem 2rem;
            flex: 1;
            max-width: 600px;
            margin: 0 auto;
            font-size: 0.9rem;
            line-height: 1.7;
        }

        h2.section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 1rem;
            text-align: center;
        }

        .contact-info {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            font-size: 14px;
        }

        .contact-info p {
            margin: 1rem 0;
            font-size: 14px;
        }

        .contact-info a {
            color: var(--black);
            font-size: 14px;
            text-decoration: none;
        }

        .contact-info a:hover {
            font-weight: bold;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
            font-size: 14px;
        }

        .social-links a {
            color: var(--black);
            font-size: 1.5rem;
            transition: transform 0.3s;
            font-size: 14px;
        }

        .social-links a:hover {
            transform: scale(1.1);
        }

        @media (max-width: 600px) {
            .content-wrapper {
                padding: 5rem 1rem 2rem;
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
    <h2 class="section-title">Get in Touch</h2>

    <div class="contact-info">
        <p>Have questions, suggestions, or need support? We'd love to hear from you!</p>
        
        <p><i class="fas fa-envelope"></i> <a href="eventra@gmail.com">Email us </a></p>
        <p><i class="fab fa-linkedin"></i> <a href="https://www.linkedin.com/in/shailendrachandrasekaran/"> Meet the creator</a></p>
        
        <p><i class="fas fa-map-marker-alt"></i> Visit us at:<br>
        Coimbatore - 641044<br>
        TamilNadu, India</p>

        <div class="social-links">
            <a href="#" title="LinkedIn"></a>
        </div>
    </div>
</div>

</body>
</html>