<?php
session_start();
include 'db.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// Check if form is submitted
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $booking_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];
    $visitor_name = $conn->real_escape_string($_POST['visitor_name']);
    $visitor_email = $conn->real_escape_string($_POST['visitor_email']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $conn->real_escape_string($_POST['status']);

    // Optional: check if slot is already booked by another booking
    $check = $conn->query("SELECT * FROM bookings WHERE user_id='$user_id' AND date='$date' AND time='$time' AND id != '$booking_id'");
    if($check->num_rows > 0){
        $_SESSION['error'] = "This time slot is already booked!";
        header("Location: dashboard.php");
        exit;
    }

    // Update booking in database
    $update = $conn->query("UPDATE bookings SET visitor_name='$visitor_name', visitor_email='$visitor_email', date='$date', time='$time', status='$status' WHERE id='$booking_id' AND user_id='$user_id'");

    if($update){
        $_SESSION['success'] = "Booking updated successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Error updating booking: ".$conn->error;
        header("Location: dashboard.php");
        exit;
    }
} else {
    // If someone directly opens edit_booking.php
    header("Location: dashboard.php");
    exit;
}
?>
