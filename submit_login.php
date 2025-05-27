<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'erasmapp_db';

$conn = new mysqli($host, $user, $pass, $dbname, 3307);
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Αποθήκευση δεδομένων χρήστη στη session ως πίνακας
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'fname' => $user['fname'],
            'lname' => $user['lname'],
            'email' => $user['email'],
            'am' => $user['am'],
            'role' => $user['role']
        ];
        header("Location: index.php");
        exit();
    } else {
        echo "❌ Λάθος κωδικός.";
    }
} else {
    echo "❌ Δεν υπάρχει χρήστης με αυτό το όνομα.";
}

$stmt->close();
$conn->close();
?>
