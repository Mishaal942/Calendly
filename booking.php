<?php
include 'db.php';
if(isset($_POST['id'], $_POST['new_date'], $_POST['new_time'])){
    $id = intval($_POST['id']);
    $date = $_POST['new_date'];
    $time = $_POST['new_time'];

    // Update booking
    $stmt = $conn->prepare("UPDATE bookings SET date=?, time=? WHERE id=?");
    $stmt->bind_param("ssi", $date, $time, $id);
    if($stmt->execute()){
        echo "success";
    } else {
        http_response_code(500);
        echo "Failed to update booking";
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid data";
}
?>
