![68747470733a2f2f692e696d6775722e636f6d2f6e5a50513949662e706e67](https://github.com/user-attachments/assets/6024983b-1d71-4d06-9ff7-9316f09994ab)


# Event Manager for Clubs and Societies

- **By team**: Loops! we did it again :)

## Overview

This project aims to build a website to streamline event management for clubs and societies while encouraging member participation through a gamified leaderboard. The platform enables clubs to create and manage events, members to RSVP and receive reminders, and tracks participation to foster a fun competitive environment.

## [Visit our website](https://www.eventra.wuaze.com) 

---

## Table of Contents

- [Website Walkthrough](#walkthrough)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Usage](#usage)
- [Contributors](#contributors)

---

## walkthrough

[![Watch the Walkthrough](thumbnail.jpg)](https://youtube.com/shorts/jDuLo2dRMvk?feature=share)

---

## Features

- **Event Creation and Management**: Clubs can create events with details such as name, date, time, description, and location.
- **RSVP System**: Students can RSVP for events, confirming their attendance.
- **Export Registrations**: Clubs can download attendee details in CSV format.    
- **Leaderboard**: Tracks and ranks student participation based on event attendance.
- **Responsive Design**: Mobile-friendly interface to ensure accessibility on all devices.
- **Gamification (Future Enhancement)**: Badges and achievements for active participants.

---

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL (for storing events, RSVP data, and leaderboard stats)
- **Styling**: Material UI, Google Fonts, Google Icons
- **APIs**: REST API for frontend-backend communication

---

## Installation

This project uses *PHP* and *MySQL*, and is designed to run locally using *XAMPP*. Follow the steps below to set it up.

---

### Prerequisites

- [Download XAMPP](https://www.apachefriends.org/index.html) and install it on your system.
- Any code editor of your choice, Preferably Visual Studio Code. (For editing files)
- Make sure *Apache* and *MySQL* modules are running via the XAMPP Control Panel. (Open XAMPP and click "start" for Apache and MySQL)

---

### Folder Setup

1. Go to the htdocs directory inside your XAMPP installation: Locate where XAMPP has been installed and navigate to htdocs folder.
   *(Example: C:/xampp/htdocs/ on Windows)*

2. An "index.php" file already exists in htdocs/ by default. Rename or remove it to avoid conflicts. Note: index.php is the entry point/page for the website, which is already created and present in repo, replace the default with it by following the  steps below. 

3. Create a new folder named eventra inside the htdocs folder.

4. Download all .php files from this GitHub repository and place them inside the eventra/ folder. (Clone or download zip file of repo) 

---

### Database Setup 

1. Open: http://localhost/phpMyAdmin
2. Click "New" on the left hand side to create a new database. Name it "eventra"
3. Click "Import" on the top-centre of the screen.
4. Select the "eventra.sql" file (downloaded via zip/clone) and upload it.
5. Congratulations! The database has been successfully imported, hassle-free!

---

### Launching the Website Locally

1. Open your browser.

2. Visit: http://localhost/eventra

3. The index page of the Eventra website should load and ENJOY!

---

### Optional: Hosting the Website Publicly

If you want to make your website publicly accessible:
	1.	Use a free hosting provider
	2.	Upload all PHP files to the hosting server (usually in an htdocs or htdocs-like directory).
	3.	Update db_connect.php with your hosting provider's s MySQL credentials (hostname, username, password, and database name)
	4.	Access your site via the public URL provided by the host. 
 
 (For more help, feel free to contact us below!)

---

## Usage

1. Clubs can log in to create and manage events. 
2. Members can view available events and RSVP.
3. Members can also check the leaderboard for rankings based on event participation.

---

## Contributors

We thank the following individuals for their contributions to this project:

- **[MUGILAN Y]** - [GitHub Profile](https://github.com/Mugilan1309)
- **[SHAILENDRA C]** - [GitHub Profile](https://github.com/ctrl-a-shift-del)
- **[VIJEY R S]** - [GitHub Profile](https://github.com/Vijey005)

---

## Contact

For inquiries or feedback, please contact Repository owner:

- **Email**: [shail.college.mail@gmail.com](mailto:shail.college.mail@gmail.com)
- **LinkedIn**: [shailendra C](https://www.linkedin.com/in/shailendrachandrasekaran/)

---

## license

This project is not licensed for use. No rights are granted for copying, modifying, distributing, or using this code for commercial or non-commercial purposes. All such actions are strictly prohibited.
