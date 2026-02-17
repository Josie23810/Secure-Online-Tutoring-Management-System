<?php
require "../includes/auth_check.php";
require "../includes/db.php";

$search = $_GET['search'] ?? '';
$tutors = [];

// Fetch tutors with profiles, classes, and availability
$query = "SELECT u.id AS tutor_id, tp.name, tp.qualifications, tp.study_materials, tp.bio, tp.hourly_rate,
                 GROUP_CONCAT(DISTINCT tc.subject SEPARATOR ', ') AS subjects,
                 GROUP_CONCAT(DISTINCT CONCAT(ta.day_of_week, ': ', ta.start_time, '-', ta.end_time) SEPARATOR '; ') AS availability
          FROM users u
          LEFT JOIN tutor_profiles tp ON u.id = tp.tutor_id
          LEFT JOIN tutor_classes tc ON u.id = tc.tutor_id
          LEFT JOIN tutor_availability ta ON u.id = ta.tutor_id
          WHERE u.role = 'tutor'";

if ($search) {
    $query .= " AND tc.subject LIKE ?";
}
$query .= " GROUP BY u.id";

$stmt = $conn->prepare($query);
if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $tutors[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tutors - SOTMS</title>
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
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #2c7be5;
            margin-bottom: 20px;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        input {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 15px;
            background-color: #2c7be5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .tutor-card {
            background: #f8f9fa;
            margin: 15px 0;
            padding: 15px;
            border-radius: 6px;
            text-align: left;
            border-left: 5px solid #2c7be5;
        }
        .tutor-card strong {
            color: #2c7be5;
        }
        .book-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        .book-btn:hover {
            background-color: #218838;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Tutors</h1>
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search by subject (e.g., Mathematics)" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <?php if ($tutors): ?>
            <?php foreach ($tutors as $tutor): ?>
                <div class="tutor-card">
                    <strong>Name:</strong> <?php echo htmlspecialchars($tutor['name'] ?? 'Not set'); ?><br>
                    <strong>Qualifications:</strong> <?php echo htmlspecialchars($tutor['qualifications'] ?? 'Not set'); ?><br>
                    <strong>Study Materials:</strong> <?php echo htmlspecialchars($tutor['study_materials'] ?? 'Not set'); ?><br>
                    <strong>Bio:</strong> <?php echo htmlspecialchars($tutor['bio'] ?? 'Not set'); ?><br>
                    <strong>Hourly Rate:</strong> $<?php echo htmlspecialchars($tutor['hourly_rate'] ?? 'N/A'); ?><br>
                    <strong>Subjects:</strong> <?php echo htmlspecialchars($tutor['subjects'] ?? 'None'); ?><br>
                    <strong>Availability:</strong> <?php echo htmlspecialchars($tutor['availability'] ?? 'Not set'); ?><br>
                    <a href="../sessions/book_sessions.php?tutor_id=<?php echo $tutor['tutor_id']; ?>" class="book-btn">Book Session</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tutors found.</p>
        <?php endif; ?>
        <a href="students.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>