<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $admin_id = $_SESSION['admin_id'];

    // Verify event belongs to admin
    $verify_sql = "SELECT id FROM events WHERE id = ? AND admin_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $event_id, $admin_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();

    if ($verify_result->num_rows > 0) {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Delete registrations
            $delete_reg_sql = "DELETE FROM registrations WHERE event_id = ?";
            $delete_reg_stmt = $conn->prepare($delete_reg_sql);
            $delete_reg_stmt->bind_param("i", $event_id);
            $delete_reg_stmt->execute();
            $delete_reg_stmt->close();
            
            // Delete registration details
            $delete_details_sql = "DELETE FROM registration_details WHERE event_id = ?";
            $delete_details_stmt = $conn->prepare($delete_details_sql);
            $delete_details_stmt->bind_param("i", $event_id);
            $delete_details_stmt->execute();
            $delete_details_stmt->close();
            
            // Delete the event
            $delete_event_sql = "DELETE FROM events WHERE id = ?";
            $delete_event_stmt = $conn->prepare($delete_event_sql);
            $delete_event_stmt->bind_param("i", $event_id);
            $delete_event_stmt->execute();
            $delete_event_stmt->close();
            
            $conn->commit();
            header("Location: admin_home.php?delete_success=1");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            // Handle error - redirect with error message
            header("Location: adminEvent_details.php?id=$event_id&error=" . urlencode("Error deleting event: " . $e->getMessage()));
            exit;
        }
    } else {
        header("Location: adminEvent_details.php?id=$event_id&error=" . urlencode("You don't have permission to delete this event"));
        exit;
    }
} else {
    header("Location: created_events.php");
    exit;
}
?>