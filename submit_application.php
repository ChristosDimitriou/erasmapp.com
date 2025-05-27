<?php
session_start();

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user'])) {
    die("Πρέπει να είστε συνδεδεμένος για να υποβάλετε αίτηση.");
}

// Σύνδεση με βάση δεδομένων
try {
    $pdo = new PDO("mysql:host=localhost;port=3307;dbname=erasmapp_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης με βάση: " . $e->getMessage());
}

// Ανάγνωση δεδομένων φόρμας
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$studentId = $_POST['studentId'] ?? '';
$percentage = $_POST['percentage'] ?? '';
$average = $_POST['average'] ?? '';
$englishLevel = $_POST['englishLevel'] ?? '';
$otherLanguages = $_POST['otherLanguages'] ?? '';
$uni1 = $_POST['uni1'] ?? '';
$uni2 = $_POST['uni2'] ?? '';
$uni3 = $_POST['uni3'] ?? '';
$terms = isset($_POST['terms']) ? 1 : 0;

// Έλεγχος αρχείων
function uploadFile($fileKey, $uploadDir = "uploads/") {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] != 0) return null;
    $fileName = basename($_FILES[$fileKey]['name']);
    $targetPath = $uploadDir . uniqid() . "_" . $fileName;
    if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
        return null;
    }
    return $targetPath;
}

// Δημιουργία καταλόγου αν δεν υπάρχει
if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

$gradesPath = uploadFile("gradesFile");
$englishCertPath = uploadFile("englishCert");

// Επεξεργασία πολλαπλών αρχείων για άλλες γλώσσες
$otherCertsPaths = [];
if (!empty($_FILES['otherCerts']['name'][0])) {
    foreach ($_FILES['otherCerts']['tmp_name'] as $i => $tmpName) {
        if ($_FILES['otherCerts']['error'][$i] == 0) {
            $fileName = basename($_FILES['otherCerts']['name'][$i]);
            $targetPath = "uploads/" . uniqid() . "_" . $fileName;
            if (move_uploaded_file($tmpName, $targetPath)) {
                $otherCertsPaths[] = $targetPath;
            }
        }
    }
}
$otherCertsJoined = implode(",", $otherCertsPaths);

// Εισαγωγή στην βάση
$sql = "INSERT INTO applications 
    (first_name, last_name, student_id, percentage, average, english_level, other_languages, uni1, uni2, uni3, grades_file, english_cert, other_certs, accepted_terms)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $firstName, $lastName, $studentId, $percentage, $average, $englishLevel, $otherLanguages,
    $uni1, $uni2, $uni3, $gradesPath, $englishCertPath, $otherCertsJoined, $terms
]);

// Επιτυχές redirect μετά την καταχώρηση
header("Location: index.php");
exit();

?>
