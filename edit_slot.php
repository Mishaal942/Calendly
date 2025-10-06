<?php
include 'db.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $id = $_POST['id'];
    $day = $_POST['day'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $stmt = $conn->prepare("UPDATE availability SET day=?, time_start=?, time_end=? WHERE id=?");
    $stmt->bind_param("sssi",$day,$time_start,$time_end,$id);
    $stmt->execute();
    header("Location: availability.php");
}
?>
