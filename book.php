<?php
include 'db.php';
if(!isset($_GET['user_id'])){ echo "Invalid link"; exit; }
$user_id = $_GET['user_id'];

// Fetch availability
$today = date('Y-m-d');
$res = $conn->query("SELECT * FROM availability WHERE user_id='$user_id' AND date>='$today' ORDER BY date ASC");

$success = '';
$booking_id = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $visitor_name = $_POST['visitor_name'];
    $visitor_email = $_POST['visitor_email'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $conn->query("INSERT INTO bookings (user_id,visitor_name,visitor_email,date,time) VALUES ('$user_id','$visitor_name','$visitor_email','$date','$time')");
    $booking_id = $conn->insert_id;
    $success="Appointment booked!";
    // Redirect visitor to manage page
    header("Location: visitor_manage.php?booking_id=$booking_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Book Appointment</title>
<style>
body{font-family:sans-serif;background:#e0f7fa;margin:0;padding:0;}
.container{max-width:600px;margin:50px auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,.1);}
input,select,button{width:100%;padding:10px;margin:10px 0;border-radius:5px;border:1px solid #ccc;}
button{background:#4facfe;color:#fff;border:none;cursor:pointer;}
button:hover{background:#00c6ff;}
.success{color:green;}
</style>
</head>
<body>
<div class="container">
<h2>Book Appointment</h2>
<?php if($success) echo "<p class='success'>$success</p>"; ?>
<form method="post">
<input type="text" name="visitor_name" placeholder="Your Name" required>
<input type="email" name="visitor_email" placeholder="Your Email" required>
<select name="date" required>
<option value="">Select Date</option>
<?php while($row=$res->fetch_assoc()){ ?>
<option value="<?php echo $row['date']; ?>"><?php echo $row['date']." (".$row['start_time']."-".$row['end_time'].")"; ?></option>
<?php } ?>
</select>
<input type="time" name="time" required>
<button type="submit">Book Appointment</button>
</form>
</div>
</body>
</html>
