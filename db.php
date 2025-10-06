<?php
$servername = "localhost";
$username = "uppbmi0whibtc";
$password = "bjgew6ykgu1v";
$dbname = "dbkzwra97qbd5t";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
