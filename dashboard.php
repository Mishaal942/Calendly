<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_res = $conn->query("SELECT * FROM users WHERE id='$user_id'");
$user = $user_res->fetch_assoc();

$events = [];
$upcoming = [];
$past = [];
$today = date('Y-m-d');

$res = $conn->query("SELECT * FROM bookings WHERE user_id='$user_id' ORDER BY date ASC");
while($row = $res->fetch_assoc()){
    $status_color = ($row['status']=='booked')?'green':(($row['status']=='cancelled')?'red':'orange');
    $events[] = [
        'id' => $row['id'],
        'title' => $row['visitor_name'].' ('.$row['time'].')',
        'start' => $row['date'].'T'.$row['time'],
        'color' => $status_color
    ];
    if($row['date'] >= $today) $upcoming[] = $row;
    else $past[] = $row;
}

// Stats
$availability_res = $conn->query("SELECT * FROM availability WHERE user_id='$user_id'");
$total_slots = $availability_res->num_rows;
$booked_res = $conn->query("SELECT * FROM bookings WHERE user_id='$user_id' AND status='booked'");
$booked_slots = $booked_res->num_rows;
$free_slots = $total_slots - $booked_slots;

// Booking link
$booking_link = "http://".$_SERVER['HTTP_HOST']."/book.php?user_id=".$user_id;
$events_json = json_encode($events);
?>
<!DOCTYPE html>
<html>
<head>
<title>Calendly-Style Dashboard</title>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* ===== Fonts & Body ===== */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    transition: all 0.3s ease;
    color: #2d3748;
    min-height: 100vh;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 90% 80%, rgba(118, 75, 162, 0.05) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.dark-mode {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: #e2e8f0;
}

.dark-mode::before {
    background: 
        radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 90% 80%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
}

.dark-mode .card {
    background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
}

.dark-mode #calendar,
.dark-mode table {
    background: #2d3748;
    color: #e2e8f0;
}

.dark-mode th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.dark-mode tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.05);
}

.dark-mode .notification {
    background: rgba(255, 193, 7, 0.15);
    border-left-color: #ffc107;
    color: #ffc107;
}

/* ===== Header ===== */
header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    position: relative;
    z-index: 100;
    backdrop-filter: blur(10px);
}

header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

header > div {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

header button {
    margin: 5px;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

header button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.5s, height 0.5s;
}

header button:hover::before {
    width: 300px;
    height: 300px;
}

header button:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

/* Tooltip */
header button[data-tooltip] {
    position: relative;
}

header button[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    top: -45px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    animation: tooltipFadeIn 0.3s ease;
}

@keyframes tooltipFadeIn {
    from { opacity: 0; transform: translateX(-50%) translateY(-5px); }
    to { opacity: 1; transform: translateX(-50%) translateY(0); }
}

/* ===== Container ===== */
.container {
    max-width: 1400px;
    margin: 30px auto;
    padding: 0 30px;
    position: relative;
    z-index: 1;
}

/* ===== Stats Cards ===== */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.card {
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: center;
    cursor: pointer;
    color: #fff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    transition: all 0.6s ease;
    transform: scale(0);
}

.card:hover::before {
    transform: scale(1);
}

.card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 50px rgba(102, 126, 234, 0.4);
}

.card h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.9;
}

.card p {
    font-size: 36px;
    font-weight: 700;
    margin: 15px 0 0;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

/* ===== Notifications ===== */
.notification {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
    border-left: 5px solid #ffc107;
    padding: 18px 24px;
    margin-bottom: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(255, 193, 7, 0.2);
    font-weight: 600;
    color: #856404;
    animation: slideInDown 0.5s ease;
}

@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ===== Calendar ===== */
#calendar {
    background: #fff;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    margin-bottom: 40px;
}

/* ===== Tables & Search ===== */
.search-bar {
    margin-bottom: 20px;
    padding: 14px 20px;
    width: 100%;
    max-width: 400px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #fff;
}

.search-bar:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

h2 {
    font-size: 24px;
    font-weight: 600;
    color: #2d3748;
    margin-top: 50px;
    margin-bottom: 20px;
}

.dark-mode h2 {
    color: #e2e8f0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    border-radius: 16px;
    overflow: hidden;
}

th, td {
    padding: 18px 20px;
    text-align: left;
}

th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
}

tr {
    transition: all 0.3s ease;
}

tr:nth-child(even) {
    background: #f8fafc;
}

tr:hover {
    background: #edf2f7;
    transform: scale(1.01);
}

button.action-btn {
    padding: 8px 16px;
    margin: 3px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 13px;
}

button.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.status-booked {
    color: #48bb78;
    font-weight: 700;
    padding: 6px 12px;
    background: rgba(72, 187, 120, 0.1);
    border-radius: 8px;
    display: inline-block;
}

.status-cancelled {
    color: #f56565;
    font-weight: 700;
    padding: 6px 12px;
    background: rgba(245, 101, 101, 0.1);
    border-radius: 8px;
    display: inline-block;
}

.status-pending {
    color: #ed8936;
    font-weight: 700;
    padding: 6px 12px;
    background: rgba(237, 137, 54, 0.1);
    border-radius: 8px;
    display: inline-block;
}

/* ===== Modals ===== */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background: #fff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 20px;
    width: 90%;
    max-width: 550px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.4s ease;
    position: relative;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 15px;
    margin-bottom: 25px;
}

.modal-header h2 {
    margin: 0;
    font-size: 22px;
    color: #2d3748;
}

.modal-close {
    cursor: pointer;
    font-size: 28px;
    font-weight: bold;
    color: #718096;
    transition: all 0.3s ease;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.modal-close:hover {
    color: #f56565;
    background: rgba(245, 101, 101, 0.1);
    transform: rotate(90deg);
}

.dark-mode .modal-content {
    background: #2d3748;
    color: #e2e8f0;
}

.dark-mode .modal-header {
    border-bottom-color: #4a5568;
}

.dark-mode .modal-header h2 {
    color: #e2e8f0;
}

.dark-mode .modal-close {
    color: #cbd5e0;
}

.modal label {
    display: block;
    margin-top: 15px;
    margin-bottom: 8px;
    font-weight: 600;
    color: #4a5568;
    font-size: 14px;
}

.dark-mode .modal label {
    color: #cbd5e0;
}

.modal input, .modal select {
    width: 100%;
    padding: 14px 18px;
    margin: 8px 0;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f7fafc;
}

.modal input:focus, .modal select:focus {
    outline: none;
    border-color: #667eea;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.dark-mode .modal input,
.dark-mode .modal select {
    background: #4a5568;
    border-color: #4a5568;
    color: #e2e8f0;
}

.modal button {
    width: 100%;
    padding: 16px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 16px;
    margin-top: 20px;
}

.modal button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    header {
        padding: 20px;
    }
    
    header h1 {
        font-size: 22px;
        width: 100%;
        margin-bottom: 15px;
    }
    
    header > div {
        width: 100%;
        justify-content: center;
    }
    
    header button {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .container {
        padding: 0 15px;
        margin: 20px auto;
    }
    
    .cards {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    table {
        font-size: 14px;
    }
    
    th, td {
        padding: 12px 10px;
    }
    
    .modal-content {
        width: 95%;
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .card p {
        font-size: 28px;
    }
    
    h2 {
        font-size: 20px;
    }
    
    button.action-btn {
        padding: 6px 12px;
        font-size: 12px;
        display: block;
        width: 100%;
        margin: 5px 0;
    }
}
</style>

<script>
function copyLink(){navigator.clipboard.writeText("<?php echo $booking_link; ?>");alert("Booking link copied ✅");}
function filterTable(){var input=document.getElementById("searchInput").value.toLowerCase();var rows=document.querySelectorAll("#upcomingTable tbody tr");rows.forEach(row=>{row.style.display=(row.innerText.toLowerCase().includes(input))? "": "none";});}
function toggleDarkMode(){document.body.classList.toggle('dark-mode');}
function openModal(id){document.getElementById(id).style.display='block';}
function closeModal(id){document.getElementById(id).style.display='none';}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl=document.getElementById('calendar');
    var calendar=new FullCalendar.Calendar(calendarEl,{
        initialView:'dayGridMonth',
        editable:true,
        eventDisplay:'block',
        headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,timeGridDay'},
        events: <?php echo $events_json; ?>,
        eventClick:function(info){
            if(confirm("Do you want to edit this booking?")){
                window.location.href='edit_booking.php?id='+info.event.id;
            }
        },
        eventDrop:function(info){
            if(confirm('Do you want to reschedule this booking?')){
                var id=info.event.id;
                var date=info.event.startStr.split('T')[0];
                var time=info.event.startStr.split('T')[1].substring(0,5);
                $.ajax({
                    url:'update_booking.php',
                    method:'POST',
                    data:{id:id,new_date:date,new_time:time},
                    success:function(){alert('Booking updated ✅');},
                    error:function(){alert('Error updating booking!'); info.revert();}
                });
            } else { info.revert(); }
        }
    });
    calendar.render();
});
</script>
</head>
<body>
<header>
<h1>Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>
<div>
<button onclick="window.location.href='availability.php'">Manage Availability</button>
<button onclick="copyLink()" data-tooltip="Copy your booking link">Copy Booking Link</button>
<button onclick="toggleDarkMode()">Toggle Dark/Light</button>
<button onclick="openModal('addBookingModal')">+ Add Booking</button>
<button onclick="window.location.href='logout.php'">Log Out</button>
</div>
</header>

<div class="container">

<div class="notification">
You have <?php echo count($upcoming); ?> upcoming meeting(s)!
</div>

<div class="cards">
<div class="card"><h3>Total Slots</h3><p><?php echo $total_slots; ?></p></div>
<div class="card"><h3>Booked Slots</h3><p><?php echo $booked_slots; ?></p></div>
<div class="card"><h3>Free Slots</h3><p><?php echo $free_slots; ?></p></div>
</div>

<div id='calendar'></div>

<h2>Upcoming Bookings</h2>
<input type="text" id="searchInput" class="search-bar" onkeyup="filterTable()" placeholder="Search bookings...">
<?php if(count($upcoming)>0){ ?>
<table id="upcomingTable">
<tr><th>Name</th><th>Email</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th></tr>
<?php foreach($upcoming as $row){ ?>
<tr>
<td><?php echo htmlspecialchars($row['visitor_name']); ?></td>
<td><?php echo htmlspecialchars($row['visitor_email']); ?></td>
<td><?php echo $row['date']; ?></td>
<td><?php echo $row['time']; ?></td>
<td class="status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>
<td>
<button class="action-btn" onclick="openModal('editBookingModal-<?php echo $row['id']; ?>')">Edit</button>
<button class="action-btn" onclick="if(confirm('Delete booking?')){window.location.href='delete_booking.php?id=<?php echo $row['id']; ?>';}">Delete</button>
</td>
</tr>

<!-- Edit Booking Modal -->
<div id="editBookingModal-<?php echo $row['id']; ?>" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>Edit Booking</h2>
<span class="modal-close" onclick="closeModal('editBookingModal-<?php echo $row['id']; ?>')">&times;</span>
</div>
<form method="POST" action="edit_booking.php">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<label>Name</label>
<input type="text" name="visitor_name" value="<?php echo htmlspecialchars($row['visitor_name']); ?>" required>
<label>Email</label>
<input type="email" name="visitor_email" value="<?php echo htmlspecialchars($row['visitor_email']); ?>" required>
<label>Date</label>
<input type="date" name="date" value="<?php echo $row['date']; ?>" required>
<label>Time</label>
<input type="time" name="time" value="<?php echo $row['time']; ?>" required>
<label>Status</label>
<select name="status">
<option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
<option value="booked" <?php if($row['status']=='booked') echo 'selected'; ?>>Booked</option>
<option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
</select>
<button type="submit">Update Booking</button>
</form>
</div>
</div>
<?php } ?>
</table>
<?php } else { echo "<p>No upcoming bookings.</p>"; } ?>

<!-- Add Booking Modal -->
<div id="addBookingModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>Add Booking</h2>
<span class="modal-close" onclick="closeModal('addBookingModal')">&times;</span>
</div>
<form method="POST" action="add_booking.php">
<label>Name</label>
<input type="text" name="visitor_name" required>
<label>Email</label>
<input type="email" name="visitor_email" required>
<label>Date</label>
<input type="date" name="date" required>
<label>Time</label>
<input type="time" name="time" required>
<button type="submit">Add Booking</button>
</form>
</div>
</div>

<h2>Past Bookings</h2>
<?php if(count($past)>0){ ?>
<table>
<tr><th>Name</th><th>Email</th><th>Date</th><th>Time</th><th>Status</th></tr>
<?php foreach($past as $row){ ?>
<tr>
<td><?php echo htmlspecialchars($row['visitor_name']); ?></td>
<td><?php echo htmlspecialchars($row['visitor_email']); ?></td>
<td><?php echo $row['date']; ?></td>
<td><?php echo $row['time']; ?></td>
<td class="status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>
</tr>
<?php } ?>
</table>
<?php } else { echo "<p>No past bookings.</p>"; } ?>

</div>
</body>
</html>
