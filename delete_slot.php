<?php
include 'db.php';
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM availability WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}
header("Location: availability.php");
?>
