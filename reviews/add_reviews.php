<?php
require "../includes/auth_check.php";
require "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $session = $_POST['session_id'];
    $rating  = $_POST['rating'];
    $comment = $_POST['comment'];
    $user    = $_SESSION['user_id'];

    $stmt = $conn->prepare(
        "INSERT INTO reviews (session_id, reviewer_id, rating, comment)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("iiis", $session, $user, $rating, $comment);
    $stmt->execute();

    echo "Review submitted";
}
?>
