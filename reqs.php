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
  <title>Απαιτήσεις Erasmus - Erasmus App</title>
  
  <link rel="stylesheet" href="styles/reqs_style.css" />
</head>
<body>
  <header>
    <h1>Κατ’ Ελάχιστον Απαιτήσεις Συμμετοχής στο Erasmus<?php echo $isLoggedIn ? ', ' . htmlspecialchars($user['fname']) . '!' : '!'; ?></h1>
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
    <section class="intro-section">
      <h2>Ποιοι φοιτητές έχουν δικαίωμα αίτησης Erasmus;</h2>
      <p>Για να μπορεί ένας φοιτητής ή φοιτήτρια να υποβάλει αίτηση για μετακίνηση μέσω Erasmus, θα πρέπει να πληροί τουλάχιστον τις παρακάτω προϋποθέσεις:</p>
      
      <table border="1" cellpadding="10" style="margin: 1rem auto; border-collapse: collapse;">
        <thead>
          <tr style="background-color: #0055a5; color: white;">
            <th>Απαίτηση</th>
            <th>Ελάχιστο Κριτήριο</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Τρέχον έτος σπουδών</td>
            <td>≥ 2ο έτος</td>
          </tr>
          <tr>
            <td>Ποσοστό περασμένων μαθημάτων</td>
            <td>≥ 70%</td>
          </tr>
          <tr>
            <td>Μέσος όρος μαθημάτων</td>
            <td>≥ 6.50</td>
          </tr>
          <tr>
            <td>Γνώση αγγλικής γλώσσας</td>
            <td>≥ Επίπεδο B2</td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top: 2rem;">
        <h3>Ενημερωτικά αρχεία</h3>
        <ul>
          <li><a href="media/erasmus1.pdf" target="_blank" rel="noopener noreferrer">Οδηγός Συμμετοχής Erasmus (PDF)</a></li>
          <li><a href="media/erasmus2.pdf" target="_blank" rel="noopener noreferrer">2oς Οδηγός Συμμετοχής Erasmus (PDF)</a></li>
        </ul>
      </div>

      <div style="margin-top: 2rem;">
        <h3>Εικόνες Σχετικές με το Πρόγραμμα</h3>
        <div class="reqs-img-wrapper">
          <img src="media/erasmus_team.jpg" alt="Ομάδα Erasmus" class="reqs-img" />
          <img src="media/erasmus_city.jpg" alt="Πόλη Erasmus" class="reqs-img" />
        </div>        
      </div>
    </section>

    <section style="margin-top: 3rem;">
      <h2>Γρήγορος Έλεγχος Πληρότητας</h2>
      <form id="eligibilityForm">
        <label for="year">Τρέχον έτος σπουδών:</label><br />
        <select id="year" name="year" required>
          <option value="1">1ο</option>
          <option value="2">2ο</option>
          <option value="3">3ο</option>
          <option value="4">4ο</option>
          <option value="5">Μεγαλύτερο</option>
        </select><br /><br />

        <label for="percentage">Ποσοστό περασμένων μαθημάτων (%):</label><br />
        <input type="number" id="percentage" name="percentage" min="0" max="100" required /><br /><br />

        <label for="avg">Μέσος όρος μαθημάτων:</label><br />
        <input type="number" step="0.01" id="avg" name="avg" min="0" max="10" required /><br /><br />

        <p>Πιστοποιητικό γνώσης αγγλικής γλώσσας:</p>
        <label><input type="radio" name="english" value="A1" /> A1</label><br />
        <label><input type="radio" name="english" value="A2" /> A2</label><br />
        <label><input type="radio" name="english" value="B1" /> B1</label><br />
        <label><input type="radio" name="english" value="B2" /> B2</label><br />
        <label><input type="radio" name="english" value="C1" /> C1</label><br />
        <label><input type="radio" name="english" value="C2" /> C2</label><br /><br />

        <button type="submit">Έλεγχος</button>
      </form>
    </section>

    <div id="result" style="margin-top: 1rem; font-weight: bold;"></div>
  </main>

  <footer>
    <p>&copy; 2025 Erasmus App - Πανεπιστήμιο Πελοποννήσου</p>
  </footer>

  <script src="reqs_check.js"></script>
</body>
</html>
