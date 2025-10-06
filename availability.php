<?php
include 'db.php';

// Fetch all slots
$slots_res = $conn->query("SELECT * FROM availability ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), time_start");
$slots = [];
while($row = $slots_res->fetch_assoc()){
    $slots[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Availability</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow-x: hidden;
}

/* Animated Background */
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.container {
    max-width: 900px;
    width: 90%;
    margin: 40px auto;
    padding: 40px;
    background: rgba(255, 255, 255, 0.98);
    border-radius: 24px;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.2),
        0 0 0 1px rgba(255, 255, 255, 0.5);
    position: relative;
    z-index: 5;
    animation: fadeInUp 0.8s ease-out;
    backdrop-filter: blur(10px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.container::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #667eea);
    border-radius: 24px;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
    background-size: 200% 200%;
    animation: gradientShift 4s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.container:hover::before {
    opacity: 0.6;
}

h1 {
    text-align: center;
    font-size: 2.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 35px;
    font-weight: 700;
    letter-spacing: -1px;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

button {
    padding: 14px 28px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    position: relative;
    overflow: hidden;
}

button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

button:hover::before {
    width: 400px;
    height: 400px;
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

button:active {
    transform: translateY(-1px);
}

.back-btn {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    box-shadow: none;
    border: 2px solid #667eea;
}

.back-btn:hover {
    background: rgba(102, 126, 234, 0.2);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}

th, td {
    padding: 18px 20px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
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

tr:hover {
    background: #f7fafc;
    transform: scale(1.01);
}

tr:last-child td {
    border-bottom: none;
}

td button {
    padding: 8px 16px;
    font-size: 13px;
    margin: 3px;
}

td button:first-child {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
}

td button:first-child:hover {
    box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
}

td a button {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
}

td a button:hover {
    box-shadow: 0 6px 20px rgba(245, 101, 101, 0.4);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
    font-size: 18px;
}

.empty-state::before {
    content: '📅';
    display: block;
    font-size: 60px;
    margin-bottom: 20px;
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
    padding: 35px;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
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
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e2e8f0;
}

.modal-header h3 {
    margin: 0;
    font-size: 22px;
    color: #2d3748;
    font-weight: 700;
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

.modal label {
    display: block;
    margin-top: 18px;
    margin-bottom: 8px;
    font-weight: 600;
    color: #4a5568;
    font-size: 14px;
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
    font-family: 'Poppins', sans-serif;
}

.modal input:focus, .modal select:focus {
    outline: none;
    border-color: #667eea;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.modal button {
    width: 100%;
    margin-top: 25px;
    padding: 16px 20px;
    font-size: 16px;
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    .container {
        padding: 30px 20px;
        margin: 20px auto;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    .header-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    button {
        width: 100%;
    }
    
    table {
        font-size: 14px;
    }
    
    th, td {
        padding: 12px 10px;
    }
    
    td button {
        display: block;
        width: 100%;
        margin: 5px 0;
    }
    
    .modal-content {
        width: 95%;
        padding: 25px 20px;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.6rem;
    }
    
    .modal-header h3 {
        font-size: 18px;
    }
    
    table {
        font-size: 12px;
    }
    
    th, td {
        padding: 10px 8px;
    }
}
</style>
<script>
function openModal(id){document.getElementById(id).style.display='block';}
function closeModal(id){document.getElementById(id).style.display='none';}
</script>
</head>
<body>

<div class="container">
<h1>Manage Availability</h1>

<div class="header-actions">
<button class="back-btn" onclick="window.location.href='dashboard.php'">← Back to Dashboard</button>
<button onclick="openModal('addModal')">+ Add New Slot</button>
</div>

<?php if(count($slots) > 0){ ?>
<table>
<tr><th>Day</th><th>Start Time</th><th>End Time</th><th>Actions</th></tr>
<?php foreach($slots as $slot){ ?>
<tr>
<td><strong><?php echo $slot['day']; ?></strong></td>
<td><?php echo date('g:i A', strtotime($slot['time_start'])); ?></td>
<td><?php echo date('g:i A', strtotime($slot['time_end'])); ?></td>
<td>
<button onclick="openModal('editModal-<?php echo $slot['id']; ?>')">Edit</button>
<a href="delete_slot.php?id=<?php echo $slot['id']; ?>" onclick="return confirm('Delete this slot?');"><button>Delete</button></a>
</td>
</tr>

<!-- Edit Modal -->
<div id="editModal-<?php echo $slot['id']; ?>" class="modal">
<div class="modal-content">
<div class="modal-header">
<h3>Edit Slot</h3>
<span class="modal-close" onclick="closeModal('editModal-<?php echo $slot['id']; ?>')">&times;</span>
</div>
<form method="POST" action="edit_slot.php">
<input type="hidden" name="id" value="<?php echo $slot['id']; ?>">
<label>Day</label>
<select name="day" required>
<option value="Monday" <?php if($slot['day']=='Monday') echo 'selected'; ?>>Monday</option>
<option value="Tuesday" <?php if($slot['day']=='Tuesday') echo 'selected'; ?>>Tuesday</option>
<option value="Wednesday" <?php if($slot['day']=='Wednesday') echo 'selected'; ?>>Wednesday</option>
<option value="Thursday" <?php if($slot['day']=='Thursday') echo 'selected'; ?>>Thursday</option>
<option value="Friday" <?php if($slot['day']=='Friday') echo 'selected'; ?>>Friday</option>
<option value="Saturday" <?php if($slot['day']=='Saturday') echo 'selected'; ?>>Saturday</option>
<option value="Sunday" <?php if($slot['day']=='Sunday') echo 'selected'; ?>>Sunday</option>
</select>
<label>Start Time</label>
<input type="time" name="time_start" value="<?php echo $slot['time_start']; ?>" required>
<label>End Time</label>
<input type="time" name="time_end" value="<?php echo $slot['time_end']; ?>" required>
<button type="submit">Update Slot</button>
</form>
</div>
</div>

<?php } ?>
</table>
<?php } else { ?>
<div class="empty-state">
<p>No availability slots added yet.<br>Click "Add New Slot" to get started!</p>
</div>
<?php } ?>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h3>Add New Slot</h3>
<span class="modal-close" onclick="closeModal('addModal')">&times;</span>
</div>
<form method="POST" action="add_slot.php">
<label>Day</label>
<select name="day" required>
<option value="Monday">Monday</option>
<option value="Tuesday">Tuesday</option>
<option value="Wednesday">Wednesday</option>
<option value="Thursday">Thursday</option>
<option value="Friday">Friday</option>
<option value="Saturday">Saturday</option>
<option value="Sunday">Sunday</option>
</select>
<label>Start Time</label>
<input type="time" name="time_start" required>
<label>End Time</label>
<input type="time" name="time_end" required>
<button type="submit">Add Slot</button>
</form>
</div>
</div>

</body>
</html>
