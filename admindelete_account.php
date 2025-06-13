<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Delete admin account and all associated events
$conn->begin_transaction();

try {
    // First delete events (due to foreign key constraint)
    $delete_events = "DELETE FROM events WHERE admin_id = ?";
    $stmt = $conn->prepare($delete_events);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->close();

    // Then delete admin
    $delete_admin = "DELETE FROM admins WHERE id = ?";
    $stmt = $conn->prepare($delete_admin);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    
    session_destroy();
    header("Location: adminlogin.php?account_deleted=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: clubprofile.php?error=delete_failed");
    exit;
}
?>