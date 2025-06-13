<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: profile.php?error=csrf_verification_failed");
    exit;
}

$user_id = $_SESSION['user_id'];

// Delete user account and all associated data
$conn->begin_transaction();

try {
    // First delete registrations
    $delete_registrations = "DELETE FROM registrations WHERE user_id = ?";
    $stmt = $conn->prepare($delete_registrations);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Then delete user
    $delete_user = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_user);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    
    session_destroy();
    header("Location: index.php?account_deleted=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: profile.php?error=delete_failed");
    exit;
}
?>