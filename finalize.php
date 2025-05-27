<?php
session_start();

// Έλεγχος αν είναι admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "erasmapp_db";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

// Αρχικά, μηδενίζουμε όλες τις αιτήσεις (final_accept = 0)
$conn->query("UPDATE applications SET final_accept = 0");

// Έπειτα, θέτουμε final_accept = 1 για όσους επιλέχθηκαν
if (isset($_POST['final_accept']) && is_array($_POST['final_accept'])) {
    $placeholders = implode(',', array_fill(0, count($_POST['final_accept']), '?'));
    $stmt = $conn->prepare("UPDATE applications SET final_accept = 1 WHERE id IN ($placeholders)");

    $types = str_repeat('i', count($_POST['final_accept']));
    $stmt->bind_param($types, ...$_POST['final_accept']);

    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Επιστροφή πίσω στην admin σελίδα
header("Location: admin.php?finalization=success");
exit;
