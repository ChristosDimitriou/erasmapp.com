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
  <title>Εγγραφή Χρήστη</title>
  <link rel="stylesheet" href="styles/sign-up_style.css" />
</head>
<body>

  <header>
    <h1>Πλατφόρμα Erasmus</h1>
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
    <section class="signup-form">
      <h2>Φόρμα Εγγραφής</h2>
      <form action="submit_signup.php" method="post" onsubmit="return validateForm();">
        <label for="fname">Όνομα:</label>
        <input type="text" id="fname" name="fname" required pattern="[Α-Ωα-ωΆ-Ώά-ώA-Za-z]+" title="Μόνο γράμματα" />

        <label for="lname">Επίθετο:</label>
        <input type="text" id="lname" name="lname" required pattern="[Α-Ωα-ωΆ-Ώά-ώA-Za-z]+" title="Μόνο γράμματα" />
        
        <label for="am">Αριθμός Μητρώου:</label>
        <input type="text" id="am" name="am" required pattern="2022[0-9]+" title="Ο Αριθμός Μητρώου πρέπει να ξεκινά με 2022 και να περιέχει μόνο ψηφία." />

        <label for="phone">Τηλέφωνο:</label>
        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" title="Πρέπει να είναι ακριβώς 10 ψηφία" />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required />

        <label for="password">Κωδικός:</label>
        <input type="password" id="password" name="password" required minlength="5" title="Τουλάχιστον 5 χαρακτήρες" />

        <label for="confirm-password">Επιβεβαίωση Κωδικού:</label>
        <input type="password" id="confirm-password" name="confirm-password" required />

        <button type="submit">Εγγραφή</button>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Erasmus+ Πλατφόρμα Φοιτητικής Μετακίνησης</p>
  </footer>

  <script src="validation.js"></script>
</body>
</html>
