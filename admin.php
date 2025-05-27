<?php
session_start();
$isLoggedIn = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;

if (!$isLoggedIn || $user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$successMessage = '';
$errorMessage = '';

// Σύνδεση με βάση
$mysqli = new mysqli("localhost", "root", "", "erasmapp_db", 3307);
if ($mysqli->connect_error) {
    die("Σφάλμα σύνδεσης: " . $mysqli->connect_error);
}

// Υποβολή νέας περιόδου
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_date'], $_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if ($start_date && $end_date) {
        $mysqli->query("DELETE FROM application_periods");
        $stmt = $mysqli->prepare("INSERT INTO application_periods (start_date, end_date) VALUES (?, ?)");
        $stmt->bind_param("ss", $start_date, $end_date);
        if ($stmt->execute()) {
            $successMessage = "Η περίοδος ορίστηκε επιτυχώς!";
        } else {
            $errorMessage = "Σφάλμα κατά την αποθήκευση.";
        }
        $stmt->close();
    } else {
        $errorMessage = "Συμπληρώστε και τις δύο ημερομηνίες.";
    }
}

// Φιλτράρισμα αιτήσεων
$min_success_rate = $_GET['min_success_rate'] ?? '';
$university_filter = $_GET['university_filter'] ?? '';
$order_by_avg = isset($_GET['order_by_avg']);
$filters = [];
$params = [];

// Κατασκευή query με φίλτρα
$query = "SELECT * FROM applications WHERE 1=1";

if ($min_success_rate !== '') {
    $query .= " AND percentage >= ?";
    $filters[] = $min_success_rate;
}

if ($university_filter !== '') {
    $query .= " AND (uni1 = ? OR uni2 = ? OR uni3 = ?)";
    array_push($filters, $university_filter, $university_filter, $university_filter);
}

if ($order_by_avg) {
    $query .= " ORDER BY average DESC";
}

// Εκτέλεση prepared statement
$stmt = $mysqli->prepare($query);
if ($filters) {
    $types = str_repeat('s', count($filters));
    $stmt->bind_param($types, ...$filters);
}
$stmt->execute();
$applications_result = $stmt->get_result();

// Τρέχουσα περίοδος
$result = $mysqli->query("SELECT * FROM application_periods LIMIT 1");
$currentPeriod = $result->fetch_assoc();

// Πανεπιστήμια για dropdown
$universities = [];
$uniResult = $mysqli->query("SELECT name FROM universities");
while ($row = $uniResult->fetch_assoc()) {
    $universities[] = $row['name'];
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Διαχείριση Περιόδου Αιτήσεων</title>
    <link rel="stylesheet" href="styles/admin_style.css">
</head>
<body>
<header>
    <h1>Διαχείριση Περιόδου Αιτήσεων Erasmus</h1>
</header>

<nav>
    <ul class="menu">
        <li><a href="index.php">Αρχική</a></li>
        <li><a href="more.php">Περισσότερα</a></li>
        <li><a href="reqs.php">Απαιτήσεις</a></li>
        <li><a href="application.php">Αίτηση</a></li>
        <?php if (!$isLoggedIn): ?>
            <li><a href="sign-up.php">Εγγραφή</a></li>
            <li><a href="login.php">Είσοδος</a></li>
        <?php else: ?>
            <li><a href="profile.php">Προφίλ</a></li>
            <li><a href="admin.php">Περίοδος</a></li>
            <li><a href="logout.php">Αποσύνδεση</a></li>
        <?php endif; ?>
    </ul>
</nav>

<main>
    <?php if ($successMessage): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <h2>Ορισμός νέας περιόδου αιτήσεων</h2>
    <form method="POST" action="admin.php" class="period-form">
        <label>Ημερομηνία Έναρξης:</label>
        <input type="date" name="start_date" required value="<?= $currentPeriod['start_date'] ?? '' ?>"><br>
        <label>Ημερομηνία Λήξης:</label>
        <input type="date" name="end_date" required value="<?= $currentPeriod['end_date'] ?? '' ?>"><br>
        <button type="submit">Αποθήκευση</button>
    </form>

    <hr>

    <h2>Φίλτρα Προβολής Αιτήσεων</h2>
    <form method="GET" action="admin.php">
        <label>Ελάχιστο ποσοστό επιτυχίας:</label>
        <input type="number" name="min_success_rate" min="0" max="100" value="<?= htmlspecialchars($min_success_rate) ?>"> %

        <label>Πανεπιστήμιο:</label>
        <select name="university_filter">
            <option value="">-- Όλα --</option>
            <?php foreach ($universities as $uni): ?>
                <option value="<?= $uni ?>" <?= $university_filter === $uni ? 'selected' : '' ?>><?= $uni ?></option>
            <?php endforeach; ?>
        </select>

        <label><input type="checkbox" name="order_by_avg" <?= $order_by_avg ? 'checked' : '' ?>> Ταξινόμηση κατά Μ.Ο.</label>

        <button type="submit">Εφαρμογή Φίλτρων</button>
    </form>

    <h2>Λίστα Αιτήσεων</h2>
    <form method="POST" action="finalize.php">
        <table border="1" cellpadding="5">
            <tr>
                <th>Όνομα</th>
                <th>Επίθετο</th>
                <th>ΑΜ</th>
                <th>Μ.Ο.</th>
                <th>Ποσοστό Επιτυχίας</th>
                <th>Επίπεδο Αγγλικών</th>
                <th>1η Επιλογή</th>
                <th>2η Επιλογή</th>
                <th>3η Επιλογή</th>
                <th>Αρχεία PDF</th>
                <th>Τελική Επιλογή</th>
            </tr>
            <?php while ($row = $applications_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['average']) ?></td>
                    <td><?= htmlspecialchars($row['percentage']) ?>%</td>
                    <td><?= htmlspecialchars($row['english_level']) ?></td>
                    <td><?= htmlspecialchars($row['uni1']) ?></td>
                    <td><?= htmlspecialchars($row['uni2']) ?></td>
                    <td><?= htmlspecialchars($row['uni3']) ?></td>
                    <td>
                        <?php if (!empty($row['uploaded_file'])): ?>
                            <a href="uploads/<?= htmlspecialchars($row['uploaded_file']) ?>" target="_blank">Προβολή</a>
                        <?php else: ?>
                            Χωρίς αρχείο
                        <?php endif; ?>
                    </td>
                    <td><input type="checkbox" name="final_accept[]" value="<?= $row['id'] ?>"></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <button type="submit">Αποθήκευση Επιλογών Τελικής Αποδοχής</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 ErasmApp. Όλα τα δικαιώματα διατηρούνται.</p>
</footer>
</body>
</html>
