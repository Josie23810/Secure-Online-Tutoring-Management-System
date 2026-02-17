<?php
require "../includes/auth_check.php";
require "../includes/db.php";

$tutor_id = $_SESSION['user_id'];
$message = "";

// Fetch existing classes
$classes = [];
$stmt = $conn->prepare("SELECT * FROM tutor_classes WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO tutor_classes (tutor_id, subject, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $tutor_id, $subject, $description);
    if ($stmt->execute()) {
        $message = "Class added!";
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM tutor_classes WHERE tutor_id = ?");
        $stmt->bind_param("i", $tutor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $classes = [];
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
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
    <title>Manage Classes - SOTMS</title>
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
        .class-item {
            background: #f8f9fa;
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
            text-align: left;
        }
        input, textarea {
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
        <h1>Manage Classes</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <h2>Current Classes</h2>
        <?php if ($classes): ?>
            <?php foreach ($classes as $class): ?>
                <div class="class-item">
                    <strong><?php echo htmlspecialchars($class['subject']); ?>:</strong> <?php echo htmlspecialchars($class['description']); ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No classes added.</p>
        <?php endif; ?>
        <h2>Add New Class</h2>
        <form method="POST">
            <input type="text" name="subject" placeholder="Subject (e.g., Mathematics)" required>
            <textarea name="description" placeholder="Description" rows="3" required></textarea>
            <button type="submit">Add Class</button>
        </form>
        <a href="tutor.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>