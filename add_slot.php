<?php
include 'db.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $day = $_POST['day'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $stmt = $conn->prepare("INSERT INTO availability (day,time_start,time_end) VALUES (?,?,?)");
    $stmt->bind_param("sss",$day,$time_start,$time_end);
    $stmt->execute();
    header("Location: availability.php");
}
?>
