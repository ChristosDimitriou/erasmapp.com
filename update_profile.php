<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'erasmapp_db';

$conn = new mysqli($host, $user, $pass, $dbname, 3307);
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$fname = $_POST['fname'] ?? '';
$lname = $_POST['lname'] ?? '';
$email = $_POST['email'] ?? '';
$am = $_POST['am'] ?? '';
$userId = $_SESSION['user']['id'];

// Ασφάλεια: Θα πρέπει να κάνεις validation και sanitization εδώ

$sql = "UPDATE users SET fname = ?, lname = ?, email = ?,am=? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $fname, $lname, $email,$am, $userId);
if ($stmt->execute()) {
    // Ενημέρωση session με τα νέα δεδομένα
    $_SESSION['user']['fname'] = $fname;
    $_SESSION['user']['lname'] = $lname;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['am'] = $am;
    header("Location: profile.php?update=success");
    exit();
} else {
    echo "Σφάλμα κατά την ενημέρωση: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
