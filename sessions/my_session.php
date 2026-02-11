<?php
// Start session and check if user is logged in
require "../includes/auth_check.php";
require "../includes/db_connect.php"; // Make sure you have this file to connect to your DB

// Assuming you store the student ID in session after login
$student_id = $_SESSION['student_id'];

// Fetch sessions booked by this student
$sql = "SELECT * FROM sessions WHERE student_id = ? ORDER BY session_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Sessions</title>
</head>
<body>
    <h2>My Booked Sessions</h2>

    <a href="../student_dashboard.php">Back to Dashboard</a><br><br>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Session ID</th>
                <th>Tutor Name</th>
                <th>Subject</th>
                <th>Date & Time</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['tutor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['session_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You have no booked sessions yet.</p>
    <?php endif; ?>

    <br>
    <a href="../auth/logout.php">Logout</a>
</body>
</html>
