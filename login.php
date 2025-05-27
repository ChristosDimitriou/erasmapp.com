<?php
session_start();

$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Σύνδεση Erasmus Portal</title>
  <link rel="stylesheet" href="styles/login_style.css" />
</head>
<body>
  <header>
    <h1>Τίτλος Σελίδας</h1>
    <nav>
      <ul class="menu">
        <li><a href="index.php">Αρχική</a></li>
        <li><a href="more.php">Πληροφορίες</a></li>
        <li><a href="reqs.php">Απαιτήσεις</a></li>
        <li><a href="application.php">Αίτηση</a></li>
        <?php if (!$isLoggedIn): ?>
          <li><a href="sign-up.php">Εγγραφή</a></li>
          <li><a href="login.php">Είσοδος</a></li>
        <?php else: ?>
          <li><a href="profile.php">Προφίλ</a></li>
          <li><a href="logout.php">Αποσύνδεση</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  
  <main>
    <div class="login-form">
      <h2>Φόρμα Σύνδεσης</h2>
      <form action="submit_login.php" method="post">
        <label for="username">Όνομα Χρήστη</label>
        <input type="text" id="username" name="username" required />

        <label for="password">Κωδικός</label>
        <input type="password" id="password" name="password" required />

        <button type="submit">Σύνδεση</button>
      </form>
    </div>
  </main>

  <footer>
    © 2025 Erasmus Portal. All rights reserved.
  </footer>
</body>
</html>
