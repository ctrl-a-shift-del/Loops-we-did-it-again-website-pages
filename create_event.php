<?php
session_start();
require 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$clubname = $_SESSION['clubname'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event-name']);
    $start_date = trim($_POST['start-date']);
    $end_date = trim($_POST['end-date']);
    $start_time = trim($_POST['start-time']);
    $end_time = trim($_POST['end-time']);
    $venue = trim($_POST['venue']);
    $description = trim($_POST['description']);

    // Handle file upload
    $media_paths = [];
    if (!empty($_FILES['media']['name'][0])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['media']['name'][$key]);
            $file_path = $upload_dir . uniqid() . '_' . $file_name;
            
            if (move_uploaded_file($tmp_name, $file_path)) {
                $media_paths[] = $file_path;
            }
        }
    }

    $media_paths_str = !empty($media_paths) ? json_encode($media_paths) : NULL;

    $sql = "INSERT INTO events (admin_id, clubname, event_name, start_date, end_date, start_time, end_time, venue, description, media_paths) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssss", $admin_id, $clubname, $event_name, $start_date, $end_date, $start_time, $end_time, $venue, $description, $media_paths_str);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Event created successfully!";
        header("Location: admin_home.php");
        exit;
    } else {
        $error = "Failed to create event. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Eventra</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
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
            margin: 0;
            padding: 0;
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
            overflow-anchor: none; /* prevents scroll jumps */
            overscroll-behavior: contain; /* avoids "bounce" glitches */

            
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
        }
        .menu-button:hover{
            transform: scale(0.97);
        }

        .slide-menu {
            position: fixed;
            top: 0;
            right: -260px;
            width: 240px;
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
        .close-btn:hover{
            transform: scale(0.97);
        }

        .content-wrapper {
            padding-top: 4rem;
            padding-left: 1rem;
            padding-right: 1rem;
            padding-bottom: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .section-box {
            background-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 550px;
        }

        .section-box h2 {
            font-size: 20px;
            color: var(--black);
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        label {
            display: block;
            margin: 8px 0 5px 2px;
            font-size: 15px;
            font-weight: 600;
            color: var(--black);
        }


        .input-field {
            width: 100%;
            padding: 0.5rem;
            margin: 5px 0;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(90px);
            color: var(--black);
            min-height: 40px;
            line-height: 1.4;
            appearance: none;
        }
        .input-field[type="date"],
        .input-field[type="time"] {
            appearance: none;
            -webkit-appearance: none;
            color: var(--black);
        }
        .input-field::placeholder {
           color: #444;
            opacity: 1; /* for Firefox */
        }

        .input-field:focus {
            outline: 2px solid var(--black);
        }

        textarea.input-field {
            min-height: 100px;
            resize: vertical;
        }

        .date-time-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .date-time-group > div {
            flex: 1;
        }

        .action-button {
            padding: 12px;
            margin: 0.5rem 0;
            border: none;
            border-radius: 8px;
            background-color: var(--black);
            color: var(--white);
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .action-button:hover {
            transform: scale(0.97);
        }

        .media-button {
            background-color: rgba(255, 255, 255, 0.7);
            color: var(--black);
            border: 2px dashed var(--black);
        }


        .file-info {
            margin-top: 5px;
            font-size: 14px;
            color: var(--black);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .file-info i {
            color: #4CAF50;
        }

        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .preview-item {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-item .remove-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .date-time-group {
                flex-direction: column;
            }
            
            .section-box {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .section-box {
                padding: 15px;
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
            <button class="menu-button" onclick="toggleMenu()">☰</button>
        </div>
    </header>

    <div class="slide-menu" id="menu">
        <button class="close-btn" onclick="toggleMenu()">×</button>
        <a href="admin_home.php">Home</a>
        <a href="clubprofile.php">Club Profile</a>
    </div>
    <div class="backdrop" id="backdrop" onclick="toggleMenu()"></div>

    <div class="content-wrapper">
        <div class="section-box">
            <h2>Cook Up an Event</h2>
            <?php if (!empty($error)): ?>
                <div style="color: #ff3333; text-align: center; margin-bottom: 15px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="create_event.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="event-name">What's It Called?</label>
                    <input type="text" class="input-field" name="event-name" placeholder="Be creative, or don't" required>
                </div>

                <div class="date-time-group">
                    <div>
                        <label for="start-date">When Is Starts?</label>
                        <input type="date" class="input-field" name="start-date" required>
                    </div>
                    <div>
                        <label for="end-date">When It Ends?</label>
                        <input type="date" class="input-field" name="end-date" required>
                    </div>
                </div>

                <div class="date-time-group">
                    <div>
                        <label for="start-time">Showtime</label>
                        <input type="time" class="input-field" name="start-time" required>
                    </div>
                    <div>
                        <label for="end-time">Wrap-up Time</label>
                        <input type="time" class="input-field" name="end-time" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="venue">Where's the Party?</label>
                    <textarea class="input-field" name="venue" placeholder="No, 'idk' is not a venue" required></textarea>
                </div>

                <div class="form-group">
                    <label for="description">Tell Us more about the Event</label>
                    <textarea class="input-field" name="description" placeholder="Sales Pitch Goes Here..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="media">Got a Banner? (Upload a 9:16 vertical image)</label>
                    <button type="button" class="action-button media-button" onclick="document.getElementById('media').click();">
                        <i class="fas fa-image"></i> Upload Poster
                    </button>
                    <input type="file" id="media" name="media[]" style="display:none;" multiple onchange="showFileInfo()">
                    <div id="fileInfo" class="file-info"></div>
                    <div id="previewContainer" class="preview-container"></div>
                </div>

                <button type="submit" class="action-button">Launch It Now!</button>
            </form>
        </div>
    </div>

    <script>
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

        function showFileInfo() {
            const fileInput = document.getElementById('media');
            const fileInfo = document.getElementById('fileInfo');
            const previewContainer = document.getElementById('previewContainer');
            
            if (fileInput.files.length > 0) {
                let fileNames = [];
                previewContainer.innerHTML = '';
                
                for (let i = 0; i < fileInput.files.length; i++) {
                    fileNames.push(fileInput.files[i].name);
                    
                    // Create preview for images
                    if (fileInput.files[i].type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';
                            previewItem.innerHTML = `
                                <img src="${e.target.result}" alt="Preview">
                                <button class="remove-btn" onclick="removeFile(${i})">×</button>
                            `;
                            previewContainer.appendChild(previewItem);
                        }
                        reader.readAsDataURL(fileInput.files[i]);
                    }
                }
                
                fileInfo.innerHTML = ` ${fileInput.files.length} file selected`;
            } else {
                fileInfo.innerHTML = '';
                previewContainer.innerHTML = '';
            }
        }

        function removeFile(index) {
            const fileInput = document.getElementById('media');
            const files = Array.from(fileInput.files);
            files.splice(index, 1);
            
            // Create new DataTransfer to update files
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
            
            // Update the display
            showFileInfo();
        }

        // Show success message if redirected from successful creation
        window.onload = function() {
            <?php if (isset($_SESSION['success_message'])): ?>
                alert("<?php echo $_SESSION['success_message']; ?>");
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
        };

        document.addEventListener('DOMContentLoaded', () => {
            const dateInputs = document.querySelectorAll('input[type="date"]');
            const timeInputs = document.querySelectorAll('input[type="time"]');

            dateInputs.forEach(input => {
                input.placeholder = "DD-MM-YYYY";
            });

            timeInputs.forEach(input => {
                input.placeholder = "--:--";
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>    