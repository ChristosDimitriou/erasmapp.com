<?php
session_start();


$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;

$fname = $isLoggedIn ? htmlspecialchars($user['fname'] ?? '') : '';
$lname = $isLoggedIn ? htmlspecialchars($user['lname'] ?? '') : '';
$am = $isLoggedIn ? htmlspecialchars($user['am'] ?? '') : '';

// ... συνέχεια κώδικα όπως έχεις ...


$isLoggedIn = isset($_SESSION['user']);
$fname = $isLoggedIn ? htmlspecialchars($_SESSION['user']['fname'] ?? '') : '';
$lname = $isLoggedIn ? htmlspecialchars($_SESSION['user']['lname'] ?? '') : '';
$am = $isLoggedIn ? htmlspecialchars($_SESSION['user']['am'] ?? '') : '';

$readonlyOrDisabled = $isLoggedIn ? 'readonly' : 'disabled';
$disabledAttr = $isLoggedIn ? '' : 'disabled';

// --- Έλεγχος αν η περίοδος αιτήσεων είναι ενεργή ---
$periodActive = false;
$periodMessage = '';

try {
    $pdo = new PDO("mysql:host=localhost;port=3307;dbname=erasmapp_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM application_periods LIMIT 1");
    $period = $stmt->fetch(PDO::FETCH_ASSOC);

    $today = date('Y-m-d');

    if ($period && $today >= $period['start_date'] && $today <= $period['end_date']) {
        $periodActive = true;
    } else {
        $periodMessage = "Η περίοδος αιτήσεων δεν είναι ενεργή αυτή τη στιγμή. Παρακαλώ επιστρέψτε αργότερα.";
        // Απενεργοποίηση φόρμας
        $disabledAttr = 'disabled';
        $readonlyOrDisabled = 'disabled';
    }
} catch (PDOException $e) {
    $periodMessage = "Σφάλμα σύνδεσης με τη βάση δεδομένων.";
    $disabledAttr = 'disabled';
    $readonlyOrDisabled = 'disabled';
}

// --- Φόρτωση πανεπιστημίων ---
$universities = [];
try {
  $stmt = $pdo->query("SELECT name FROM universities ORDER BY name ASC");
  $universities = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
  // Αν υπάρχει πρόβλημα με πανεπιστήμια, απλά θα εμφανίσουμε κενή λίστα
  $universities = [];
}

?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Αίτηση Erasmus - Erasmus App</title>
  <link rel="stylesheet" href="styles/application_style.css" />
  <style>
    input:disabled, select:disabled, button:disabled {
      background-color: #eee;
      color: #777;
      cursor: not-allowed;
    }
  </style>
</head>
<body>
  <header>
    <h1>Φόρμα Αίτησης Erasmus</h1>
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

          <?php if ($user['role'] === 'admin'): ?>
            <li><a href="admin.php">Περίοδος</a></li>
          <?php endif; ?>

          <li><a href="logout.php">Αποσύνδεση</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main>

    <?php if (!$isLoggedIn): ?>
      <p style="color: red; font-weight: bold;">Πρέπει να συνδεθείτε για να συμπληρώσετε την αίτηση.</p>
    <?php endif; ?>

    <?php if (!$periodActive): ?>
      <p style="color: red; font-weight: bold;"><?= htmlspecialchars($periodMessage) ?></p>
    <?php endif; ?>

    <form class="application-form" action="submit_application.php" method="post" enctype="multipart/form-data">
      <h2>Στοιχεία Φοιτητή/Φοιτήτριας</h2>

      <label for="firstName">Όνομα:</label>
      <input type="text" id="firstName" name="firstName" required value="<?= $fname ?>" <?= $readonlyOrDisabled ?> />

      <label for="lastName">Επίθετο:</label>
      <input type="text" id="lastName" name="lastName" required value="<?= $lname ?>" <?= $readonlyOrDisabled ?> />

      <label for="studentId">Αριθμός Μητρώου:</label>
      <input type="text" id="studentId" name="studentId" required value="<?= $am ?>" <?= $readonlyOrDisabled ?> />

      <label for="percentage">Ποσοστό περασμένων μαθημάτων (%):</label>
      <input type="number" id="percentage" name="percentage" min="0" max="100" required <?= $disabledAttr ?> />

      <label for="average">Μέσος όρος μαθημάτων:</label>
      <input type="number" step="0.01" id="average" name="average" min="0" max="10" required <?= $disabledAttr ?> />

      <p>Πιστοποιητικό γνώσης αγγλικής γλώσσας:</p>
      <div class="radio-group">
        <label><input type="radio" name="englishLevel" value="A1" <?= $disabledAttr ?> /> A1</label>
        <label><input type="radio" name="englishLevel" value="A2" <?= $disabledAttr ?> /> A2</label>
        <label><input type="radio" name="englishLevel" value="B1" <?= $disabledAttr ?> /> B1</label>
        <label><input type="radio" name="englishLevel" value="B2" <?= $disabledAttr ?> /> B2</label>
        <label><input type="radio" name="englishLevel" value="C1" <?= $disabledAttr ?> /> C1</label>
        <label><input type="radio" name="englishLevel" value="C2" <?= $disabledAttr ?> /> C2</label>
      </div>

      <p>Γνώση επιπλέον ξένων γλωσσών:</p>
      <div class="radio-group">
        <label><input type="radio" name="otherLanguages" value="yes" <?= $disabledAttr ?> /> ΝΑΙ</label>
        <label><input type="radio" name="otherLanguages" value="no" <?= $disabledAttr ?> /> ΟΧΙ</label>
      </div>

      <label for="uni1">Πανεπιστήμιο - 1η επιλογή:</label>
      <select id="uni1" name="uni1" required <?= $disabledAttr ?>>
        <option value="">-- Επιλέξτε --</option>
        <?php foreach ($universities as $uni): ?>
          <option value="<?= htmlspecialchars($uni) ?>"><?= htmlspecialchars($uni) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="uni2">Πανεπιστήμιο - 2η επιλογή:</label>
      <select id="uni2" name="uni2" <?= $disabledAttr ?>>
        <option value="">-- Επιλέξτε --</option>
        <?php foreach ($universities as $uni): ?>
          <option value="<?= htmlspecialchars($uni) ?>"><?= htmlspecialchars($uni) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="uni3">Πανεπιστήμιο - 3η επιλογή:</label>
      <select id="uni3" name="uni3" <?= $disabledAttr ?>>
        <option value="">-- Επιλέξτε --</option>
        <?php foreach ($universities as $uni): ?>
          <option value="<?= htmlspecialchars($uni) ?>"><?= htmlspecialchars($uni) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="gradesFile">Αναλυτική βαθμολογία:</label>
      <input type="file" id="gradesFile" name="gradesFile" required <?= $disabledAttr ?> />

      <label for="englishCert">Πτυχίο αγγλικής γλώσσας:</label>
      <input type="file" id="englishCert" name="englishCert" required <?= $disabledAttr ?> />

      <label for="otherCerts">Πτυχία άλλων ξένων γλωσσών:</label>
      <input type="file" id="otherCerts" name="otherCerts[]" multiple <?= $disabledAttr ?> />

      <label><input type="checkbox" name="terms" required <?= $disabledAttr ?> /> Αποδέχομαι τους όρους</label>

      <button type="submit" <?= $disabledAttr ?>>Υποβολή Αίτησης</button>
    </form>
  </main>

  <footer>
    <p>&copy; 2025 Erasmus App - Πανεπιστήμιο Πελοποννήσου</p>
  </footer>
</body>
</html>
