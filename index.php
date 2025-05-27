<?php
session_start();

$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Αρχική - Erasmus App</title>
  <link rel="stylesheet" href="styles/style.css" />
</head>
<body>
  <header>
    <h1>
      Καλώς ήρθες στην Erasmus<?php 
        if ($isLoggedIn) {
          echo ', ' . htmlspecialchars($user['fname']) . '!';
        } else {
          echo '!';
        }
      ?>
    </h1>
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
      <h2>Τι είναι το πρόγραμμα Erasmus;</h2>
      <p>Το Erasmus είναι ένα ευρωπαϊκό πρόγραμμα που επιτρέπει τη φοίτηση σε συνεργαζόμενα πανεπιστήμια του εξωτερικού για ένα ή περισσότερα εξάμηνα.</p>
      <img src="media/erasmus.jpg" alt="Φοιτητές Erasmus" />
    </section>
    <video width="100%" controls>
      <source src="media/video1.mp4" type="video/mp4">
      Το βίντεο δεν υποστηρίζεται από τον browser σας.
    </video>
  </main>

  <footer>
    <p>&copy; 2025 Erasmus App - Πανεπιστήμιο Πελοποννήσου</p>
  </footer>
</body>
</html>
