<?php
session_start();

// 1. Σύνδεση με MySQL
$host = 'localhost';
$user = 'root';
$pass = "";
$dbname = 'erasmapp_db';

// Αν η θύρα σου είναι 3307, την ορίζεις εδώ
$conn = new mysqli($host, $user, $pass, $dbname, 3307);
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

// 2. Παίρνουμε τα δεδομένα από τη φόρμα
$fname     = $_POST['fname'] ?? '';
$lname     = $_POST['lname'] ?? '';
$am        = $_POST['am'] ?? '';
$phone     = $_POST['phone'] ?? '';
$email     = $_POST['email'] ?? '';
$username  = $_POST['username'] ?? '';
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm-password'] ?? '';

// 3. Έλεγχος ότι οι κωδικοί ταιριάζουν
if ($password !== $confirm) {
    die("Οι κωδικοί δεν ταιριάζουν!");
}

// 4. Έλεγχος αν το username υπάρχει ήδη
$checkSql = "SELECT id FROM users WHERE username = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $username);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    die("Το όνομα χρήστη υπάρχει ήδη. Παρακαλώ επέλεξε άλλο.");
}
$checkStmt->close();

// 5. Hashing του κωδικού για ασφάλεια
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 6. Εισαγωγή στη βάση
$sql = "INSERT INTO users (fname, lname, am, phone, email, username, password)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $fname, $lname, $am, $phone, $email, $username, $hashedPassword);

if ($stmt->execute()) {
    // Αποθήκευση στοιχείων χρήστη στη session μετά την επιτυχή εγγραφή
    $_SESSION['user'] = [
        'fname' => $fname,
        'lname' => $lname,
        'am' => $am,
        'username' => $username,
        'email' => $email,
        'phone' => $phone
    ];

    header("Location: index.php");
    exit();
} else {
    echo "Σφάλμα: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
