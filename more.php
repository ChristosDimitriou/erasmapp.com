<?php
session_start();

$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;

$servername = "localhost";
$username = "root";
$password = "";
$database = "erasmapp_db";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης με τη βάση δεδομένων: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Περισσότερα - Erasmus App</title>
  <link rel="stylesheet" href="styles/more_style.css" />
</head>
<body>
  <header>
    <h1>Περισσότερα για το Erasmus<?php echo $isLoggedIn ? ', ' . htmlspecialchars($user['fname']) . '!' : '!'; ?></h1>
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
    <section class="intro">
      <h2>Ενημερωτικό Υλικό για το Erasmus</h2>
      <p>Το πρόγραμμα Erasmus προσφέρει μια μοναδική ευκαιρία στους φοιτητές να σπουδάσουν σε συνεργαζόμενα πανεπιστήμια του εξωτερικού, να αναπτύξουν δεξιότητες και να γνωρίσουν νέους πολιτισμούς.</p>

      <h3>Χρήσιμοι Σύνδεσμοι:</h3>
      <ul>
        <li><a href="https://erasmus-plus.ec.europa.eu/" target="_blank" rel="noopener noreferrer">Επίσημη Ιστοσελίδα Erasmus+</a></li>
        <li><a href="https://www.uop.gr/" target="_blank" rel="noopener noreferrer">Πανεπιστήμιο Πελοποννήσου</a></li>
        <li><a href="https://www.youtube.com/watch?v=9FBL7D8syPk" target="_blank" rel="noopener noreferrer">Βίντεο: Εμπειρίες Erasmus στο YouTube</a></li>
      </ul>

      <h3>Εικόνες από Erasmus</h3>
      <div class="intro-content">
        <img src="media/students1.jpg" alt="Φοιτητές στο εξωτερικό" class="intro-img" />
        <img src="media/university.jpg" alt="Πανεπιστήμιο στο εξωτερικό" class="intro-img" />
      </div>

      <h3>Βίντεο</h3>
      <video width="100%" controls>
        <source src="media/video2.mp4" type="video/mp4" />
        Ο browser σας δεν υποστηρίζει το video.
      </video>
    </section>
    <?php
 // σύνδεση με βάση

// Έλεγχος αν έχει λήξει η περίοδος
$now = date('Y-m-d');
$showResults = false;

$sql = "SELECT * FROM application_periods ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
  if ($row['end_date'] < $now) {
    // Η περίοδος έληξε, οπότε δείχνουμε αποτελέσματα σε όλους
    $showResults = true;
  } else if ($isLoggedIn && $user['role'] === 'admin') {
    // Αν είναι admin, δείχνουμε πάντα
    $showResults = true;
  }
}

if ($showResults):

  $accepted_sql = "SELECT * FROM applications WHERE final_accept = 1";
  $accepted_result = $conn->query($accepted_sql);
?>

<section class="results">
  <h2>Αποτελέσματα Erasmus</h2>

  <?php if ($accepted_result && $accepted_result->num_rows > 0): ?>
    <p>Οι παρακάτω φοιτητές έχουν γίνει δεκτοί για μετακίνηση μέσω του προγράμματος Erasmus:</p>
    <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
      <tr>
        <th>Όνομα</th>
        <th>Επώνυμο</th>
        <th>Α.Μ.</th>
        <th>Μ.Ο.</th>
        <th>Ποσοστό</th>
        <th>Επίπεδο Αγγλικών</th>
        <th>Επιλεγμένο Πανεπιστήμιο</th>
      </tr>
      <?php while ($row = $accepted_result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['first_name']) ?></td>
          <td><?= htmlspecialchars($row['last_name']) ?></td>
          <td><?= htmlspecialchars($row['student_id']) ?></td>
          <td><?= htmlspecialchars($row['average']) ?></td>
          <td><?= htmlspecialchars($row['percentage']) ?>%</td>
          <td><?= htmlspecialchars($row['english_level']) ?></td>
          <td><?= htmlspecialchars($row['uni1']) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>Δεν έχουν ακόμη ανακοινωθεί δεκτές αιτήσεις.</p>
  <?php endif; ?>
</section>

<?php endif; ?>

  </main>

  <footer>
    <p>&copy; 2025 Erasmus App - Πανεπιστήμιο Πελοποννήσου</p>
  </footer>
</body>
</html>
