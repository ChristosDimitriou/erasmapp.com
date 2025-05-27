<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Προφίλ Χρήστη</title>
  <link rel="stylesheet" href="styles/profile_style.css" />
</head>
<body>
  <header>
    <h1>Καλώς ήρθες, <?php echo htmlspecialchars($user['fname']); ?>!</h1>
    <nav>
      <ul class="menu">
        <li><a href="index.php">Αρχική</a></li>
        <li><a href="more.php">Πληροφορίες</a></li>
        <li><a href="reqs.php">Απαιτήσεις</a></li>
        <li><a href="application.php">Αίτηση</a></li>
        <li><a href="profile.php">Προφίλ</a></li>
        <?php if ($user['role'] === 'admin'): ?>
            <li><a href="admin.php">Περίοδος</a></li>
          <?php endif; ?>
        <li><a href="logout.php">Αποσύνδεση</a></li>
        
      </ul>
    </nav>
  </header>

  <main>
    <h2>Στοιχεία Προφίλ</h2>
    <form action="update_profile.php" method="post">
  <label for="fname">Όνομα:</label>
  <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required />

  <label for="lname">Επώνυμο:</label>
  <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required />

  <label for="am">Αριθμός Μητρώου:</label>
  <input type="text" id="am" name="am" value="<?php echo htmlspecialchars($user['am']); ?>" required />

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />

  <button type="submit">Αποθήκευση Αλλαγών</button>
</form>

  </main>

  <footer>
    <p>&copy; 2025 Erasmus Portal - Πανεπιστήμιο Πελοποννήσου</p>
  </footer>
</body>
</html>
