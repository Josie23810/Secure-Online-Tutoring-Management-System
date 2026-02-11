<?php
require "../includes/auth_check.php";  // Corrected relative path from dashboards/ to root/includes
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <h2>Student Dashboard</h2>

    <!-- Correct links relative to this file (from dashboards/ to root/sessions/) -->
    <a href="../sessions/book_sessions.php">Book Tutor Session</a><br>  <!-- Changed to plural if that's the file name -->
    <a href="../sessions/my_sessions.php">My Sessions</a><br>
    <a href="../auth/logout.php">Logout</a>
</body>
</html>