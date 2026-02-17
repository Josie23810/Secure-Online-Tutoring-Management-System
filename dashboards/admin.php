<?php
require "../includes/auth_check.php";  // Ensures only admins can access
require "../includes/db.php";

$message = "";

// Handle delete actions (e.g., via GET params for simplicity)
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "User deleted.";
    } else {
        $message = "Error deleting user.";
    }
}

// Fetch all users with profiles and session counts
$users = [];
$query = "SELECT u.id, u.name, u.email, u.role,
                 sp.name AS student_name, sp.interests, sp.goals,
                 tp.name AS tutor_name, tp.qualifications, tp.hourly_rate,
                 COUNT(s.id) AS session_count
          FROM users u
          LEFT JOIN student_profiles sp ON u.id = sp.student_id AND u.role = 'student'
          LEFT JOIN tutor_profiles tp ON u.id = tp.tutor_id AND u.role = 'tutor'
          LEFT JOIN sessions s ON (u.id = s.student_id OR u.id = s.tutor_id)
          GROUP BY u.id
          ORDER BY u.role, u.name";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Fetch data for charts
// User roles for pie chart
$roleData = [];
$result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
while ($row = $result->fetch_assoc()) {
    $roleData[$row['role']] = $row['count'];
}

// Sessions by status for bar chart
$statusData = [];
$result = $conn->query("SELECT status, COUNT(*) as count FROM sessions GROUP BY status");
while ($row = $result->fetch_assoc()) {
    $statusData[$row['status']] = $row['count'];
}

// Sessions over time (by month) for line chart
$timeData = [];
$result = $conn->query("SELECT DATE_FORMAT(start_time, '%Y-%m') as month, COUNT(*) as count FROM sessions GROUP BY month ORDER BY month");
while ($row = $result->fetch_assoc()) {
    $timeData[$row['month']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SOTMS</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  <!-- Chart.js library -->
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
        .dashboard-container {
            background: #fff;
            padding: 40px 30px;
            width: 100%;
            max-width: 1400px;  /* Increased for better chart spacing */
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #2c7be5;
            margin-bottom: 20px;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .section {
            margin: 30px 0;  /* Increased spacing for better separation */
            text-align: left;
        }
        .section h2 {
            color: #2c7be5;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 2px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }
        .edit-btn {
            background-color: #ffc107;
            color: black;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .report-btn {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        .report-btn:hover {
            background-color: #218838;
        }
        .chart-container {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px auto;  /* Centered with auto margins */
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 600px;  /* Presentable size limit */
            width: 100%;
        }
        canvas {
            max-width: 100%;
            height: 400px;  /* Increased for better visibility */
        }
        .charts-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }
        .logout-btn {
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>SOTMS Admin Dashboard</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>All Users</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Profile Info</th>
                    <th>Sessions</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <?php if ($user['role'] == 'student'): ?>
                                Interests: <?php echo htmlspecialchars($user['interests'] ?? 'N/A'); ?><br>
                                Goals: <?php echo htmlspecialchars($user['goals'] ?? 'N/A'); ?>
                            <?php elseif ($user['role'] == 'tutor'): ?>
                                Qualifications: <?php echo htmlspecialchars($user['qualifications'] ?? 'N/A'); ?><br>
                                Rate: $<?php echo htmlspecialchars($user['hourly_rate'] ?? 'N/A'); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $user['session_count']; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">Edit</a>
                            <a href="?delete_user=<?php echo $user['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="section">
            <h2>Visual Reports</h2>
            <div class="charts-row">
                <div class="chart-container">
                    <h3>User Roles Distribution</h3>
                    <canvas id="roleChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Sessions by Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Sessions Over Time</h3>
                    <canvas id="timeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Downloadable Reports</h2>
            <a href="generate_report.php?type=users" class="report-btn">Download Users Report (CSV)</a>
            <a href="generate_report.php?type=sessions" class="report-btn">Download Sessions Report (CSV)</a>
        </div>

        <a href="../auth/logout.php" class="report-btn logout-btn">Logout</a>
    </div>

    <script>
        // Pie Chart for User Roles
        const roleCtx = document.getElementById('roleChart').getContext('2d');
        new Chart(roleCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($roleData)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($roleData)); ?>,
                    backgroundColor: ['#2c7be5', '#1a5fd0', '#ffc107'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'User Roles Distribution' }
                }
            }
        });

        // Bar Chart for Sessions by Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($statusData)); ?>,
                datasets: [{
                    label: 'Number of Sessions',
                    data: <?php echo json_encode(array_values($statusData)); ?>,
                    backgroundColor: '#2c7be5',
                    borderColor: '#1a5fd0',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { title: { display: true, text: 'Sessions by Status' } }
            }
        });

        // Line Chart for Sessions Over Time
        const timeCtx = document.getElementById('timeChart').getContext('2d');
        new Chart(timeCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($timeData)); ?>,
                datasets: [{
                    label: 'Sessions',
                    data: <?php echo json_encode(array_values($timeData)); ?>,
                    borderColor: '#2c7be5',
                    backgroundColor: 'rgba(44, 123, 229, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { title: { display: true, text: 'Sessions Over Time (Monthly)' } }
            }
        });
    </script>
</body>
</html>