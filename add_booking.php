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
    $user_id = $_SESSION['user_id'];
    $visitor_name = $conn->real_escape_string($_POST['visitor_name']);
    $visitor_email = $conn->real_escape_string($_POST['visitor_email']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Optional: check if slot is available
    $check = $conn->query("SELECT * FROM bookings WHERE user_id='$user_id' AND date='$date' AND time='$time'");
    if($check->num_rows > 0){
        $_SESSION['error'] = "This time slot is already booked!";
        header("Location: dashboard.php");
        exit;
    }

    // Insert booking into database with default status 'pending'
    $insert = $conn->query("INSERT INTO bookings (user_id, visitor_name, visitor_email, date, time, status) VALUES ('$user_id', '$visitor_name', '$visitor_email', '$date', '$time', 'pending')");

    if($insert){
        $_SESSION['success'] = "Booking added successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Error adding booking: ".$conn->error;
        header("Location: dashboard.php");
        exit;
    }
} else {
    // If someone directly opens add_booking.php
    header("Location: dashboard.php");
    exit;
}
?>
