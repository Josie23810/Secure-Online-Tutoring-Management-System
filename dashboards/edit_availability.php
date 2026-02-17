<?php
require "../includes/auth_check.php";
require "../includes/db.php";

$tutor_id = $_SESSION['user_id'];
$message = "";

// Fetch existing availability
$availability = [];
$stmt = $conn->prepare("SELECT * FROM tutor_availability WHERE tutor_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $availability[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Check if slot already exists for the day
    $stmt = $conn->prepare("SELECT id FROM tutor_availability WHERE tutor_id = ? AND day_of_week = ?");
    $stmt->bind_param("is", $tutor_id, $day);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE tutor_availability SET start_time=?, end_time=? WHERE tutor_id=? AND day_of_week=?");
        $stmt->bind_param("ssis", $start_time, $end_time, $tutor_id, $day);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO tutor_availability (tutor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $tutor_id, $day, $start_time, $end_time);
    }
    if ($stmt->execute()) {
        $message = "Availability updated!";
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM tutor_availability WHERE tutor_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
        $stmt->bind_param("i", $tutor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $availability = [];
        while ($row = $result->fetch_assoc()) {
            $availability[] = $row;
        }
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Availability - SOTMS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c7be5, #1a5fd0);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 40px 30px;
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #2c7be5;
            margin-bottom: 20px;
        }
        .slot {
            background: #f8f9fa;
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
            text-align: left;
        }
        select, input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 12px 0;
            margin: 15px 0;
            background-color: #2c7be5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #1a5fd0;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Availability</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <h2>Current Slots</h2>
        <?php if ($availability): ?>
            <?php foreach ($availability as $slot): ?>
                <div class="slot">
                    <strong><?php echo htmlspecialchars($slot['day_of_week']); ?>:</strong> <?php echo htmlspecialchars($slot['start_time']); ?> - <?php echo htmlspecialchars($slot['end_time']); ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No availability set.</p>
        <?php endif; ?>
        <h2>Add/Edit Slot</h2>
        <form method="POST">
            <select name="day" required>
                <option value="">Select Day</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <input type="time" name="start_time" required>
            <input type="time" name="end_time" required>
            <button type="submit">Save Slot</button>
        </form>
        <a href="tutor.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>