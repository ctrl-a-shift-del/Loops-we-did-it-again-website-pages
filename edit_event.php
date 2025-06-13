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

// Check if event ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_home.php");
    exit;
}

$event_id = $_GET['id'];

// Fetch existing event data
$sql = "SELECT * FROM events WHERE id = ? AND admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $event_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: admin_home.php");
    exit;
}

$event = $result->fetch_assoc();
$stmt->close();

// Parse media paths
$media_paths = !empty($event['media_paths']) ? json_decode($event['media_paths'], true) : [];
$existing_media = $media_paths;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event-name']);
    $start_date = trim($_POST['start-date']);
    $end_date = trim($_POST['end-date']);
    $start_time = trim($_POST['start-time']);
    $end_time = trim($_POST['end-time']);
    $venue = trim($_POST['venue']);
    $description = trim($_POST['description']);

    // Handle file upload for new media (single file only)
    $new_media_path = '';
    if (!empty($_FILES['media']['name'])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES['media']['name']);
        $file_path = $upload_dir . uniqid() . '_' . $file_name;
        
        if (move_uploaded_file($_FILES['media']['tmp_name'], $file_path)) {
            $new_media_path = $file_path;
        }
    }

    // Determine final media paths
    $final_media_paths = [];
    
    // If a new file was uploaded, use it (replacing any existing)
    if (!empty($new_media_path)) {
        $final_media_paths = [$new_media_path];
    } 
    // Otherwise, keep existing media if not removed
    elseif (isset($_POST['keep_media']) && !empty($_POST['keep_media'])) {
        $final_media_paths = [$_POST['keep_media'][0]]; // Only keep one
    }
    
    $media_paths_str = !empty($final_media_paths) ? json_encode($final_media_paths) : NULL;

    // Update event in database
    $sql = "UPDATE events SET 
            event_name = ?, 
            start_date = ?, 
            end_date = ?, 
            start_time = ?, 
            end_time = ?, 
            venue = ?, 
            description = ?, 
            media_paths = ?
            WHERE id = ? AND admin_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssii", $event_name, $start_date, $end_date, $start_time, $end_time, $venue, $description, $media_paths_str, $event_id, $admin_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Event updated successfully!";
        header("Location: adminEvent_details.php?id=" . $event_id);
        exit;
    } else {
        $error = "Failed to update event. Please try again.";
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
    <title>Edit Event - Eventra</title>
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
            overflow-anchor: none;
            overscroll-behavior: contain;
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
            text-decoration: none;
        }
        .back-button:hover {
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
            opacity: 1;
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
            <a href="admin_home.php" class="back-button"><i class="fas fa-arrow-left"></i></a>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="section-box">
            <h2>Last Minute Fix</h2>
            <?php if (!empty($error)): ?>
                <div style="color: #ff3333; text-align: center; margin-bottom: 15px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="edit_event.php?id=<?php echo $event_id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="event-name">What's It Called?</label>
                    <input type="text" class="input-field" name="event-name" placeholder="Event Name" 
                           value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                </div>

                <div class="date-time-group">
                    <div>
                        <label for="start-date">Start Date</label>
                        <input type="date" class="input-field" name="start-date" 
                               value="<?php echo $event['start_date']; ?>" required>
                    </div>
                    <div>
                        <label for="end-date">End Date</label>
                        <input type="date" class="input-field" name="end-date" 
                               value="<?php echo $event['end_date']; ?>" required>
                    </div>
                </div>

                <div class="date-time-group">
                    <div>
                        <label for="start-time">Showtime</label>
                        <input type="time" class="input-field" name="start-time" 
                               value="<?php echo $event['start_time']; ?>" required>
                    </div>
                    <div>
                        <label for="end-time">Wrap-up Time</label>
                        <input type="time" class="input-field" name="end-time" 
                               value="<?php echo $event['end_time']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="venue">Where's the Party?</label>
                    <textarea class="input-field" name="venue" placeholder="Venue" required><?php 
                        echo htmlspecialchars($event['venue']); 
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label for="description">What's This About?</label>
                    <textarea class="input-field" name="description" placeholder="Be brief, be bold" required><?php 
                        echo htmlspecialchars($event['description']); 
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label for="media">Banner Please! (Upload a 9:16 vertical image)</label>
                    <button type="button" class="action-button media-button" onclick="document.getElementById('media').click();">
                        <i class="fas fa-image"></i> <?php echo empty($existing_media) ? 'Upload Poster' : 'Replace Poster'; ?>
                    </button>
                    <input type="file" id="media" name="media" style="display:none;" accept="image/*" onchange="showFileInfo()">
                    <div id="fileInfo" class="file-info"></div>
                    
                    <div id="existingMedia" class="preview-container">
                        <?php if (!empty($existing_media)): ?>
                            <?php foreach ($existing_media as $index => $media): ?>
                                <div class="preview-item">
                                    <img src="<?php echo htmlspecialchars($media); ?>" alt="Existing media">
                                    <input type="hidden" name="keep_media[]" value="<?php echo htmlspecialchars($media); ?>">
                                    <button type="button" class="remove-btn" onclick="removeExistingMedia(this)">×</button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div id="previewContainer" class="preview-container"></div>
                </div>

                <button type="submit" class="action-button">Go Live!</button>
            </form>
        </div>
    </div>

    <script>
        function showFileInfo() {
            const fileInput = document.getElementById('media');
            const fileInfo = document.getElementById('fileInfo');
            const previewContainer = document.getElementById('previewContainer');
            
            previewContainer.innerHTML = '';
            
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                fileInfo.innerHTML = `Selected: ${file.name}`;
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button class="remove-btn" onclick="removeFile()">×</button>
                        `;
                        previewContainer.appendChild(previewItem);
                    }
                    reader.readAsDataURL(file);
                }
            } else {
                fileInfo.innerHTML = '';
            }
        }

        function removeFile() {
            const fileInput = document.getElementById('media');
            fileInput.value = '';
            document.getElementById('fileInfo').innerHTML = '';
            document.getElementById('previewContainer').innerHTML = '';
        }

        function removeExistingMedia(button) {
            const previewItem = button.parentElement;
            previewItem.remove();
        }

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