<?php
require "../includes/auth_check.php";  // Ensures only admins can access
require "../includes/db.php";

$type = $_GET['type'] ?? '';

if ($type == 'users') {
    // Fetch user data
    $result = $conn->query("SELECT u.id, u.name, u.email, u.role, sp.interests, tp.qualifications FROM users u LEFT JOIN student_profiles sp ON u.id = sp.student_id LEFT JOIN tutor_profiles tp ON u.id = tp.tutor_id");
    if (!$result) {
        die("Database error: " . $conn->error);
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="users_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Role', 'Profile Info']);

    while ($row = $result->fetch_assoc()) {
        $profile = $row['role'] == 'student' ? ($row['interests'] ?? 'N/A') : ($row['qualifications'] ?? 'N/A');
        fputcsv($output, [$row['id'], $row['name'], $row['email'], $row['role'], $profile]);
    }
    fclose($output);
    exit;
} elseif ($type == 'sessions') {
    // Fetch session data
    $result = $conn->query("SELECT s.id, s.start_time, s.status, s.subject, u_student.name AS student_name, u_tutor.name AS tutor_name FROM sessions s JOIN users u_student ON s.student_id = u_student.id JOIN users u_tutor ON s.tutor_id = u_tutor.id");
    if (!$result) {
        die("Database error: " . $conn->error);
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="sessions_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Start Time', 'Status', 'Subject', 'Student', 'Tutor']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['start_time'], $row['status'], $row['subject'], $row['student_name'], $row['tutor_name']]);
    }
    fclose($output);
    exit;
} else {
    die("Invalid report type.");
}
?>