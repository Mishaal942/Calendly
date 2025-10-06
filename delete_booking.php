<?php
session_start();
include 'db.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// Check if ID is provided
if(isset($_GET['id'])){
    $booking_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Delete booking from database
    $delete = $conn->query("DELETE FROM bookings WHERE id='$booking_id' AND user_id='$user_id'");

    if($delete){
        $_SESSION['success'] = "Booking deleted successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Error deleting booking: ".$conn->error;
        header("Location: dashboard.php");
        exit;
    }
} else {
    // If no ID provided
    $_SESSION['error'] = "Invalid booking ID!";
    header("Location: dashboard.php");
    exit;
}
?>
