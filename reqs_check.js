document.getElementById("eligibilityForm").addEventListener("submit", function (event) {
  event.preventDefault();
  console.log("Form submitted"); // ✅ DEBUG

  const year = parseInt(document.getElementById("year").value);
  const percentage = parseFloat(document.getElementById("percentage").value);
  const avg = parseFloat(document.getElementById("avg").value);
  const english = document.querySelector('input[name="english"]:checked');

  const resultDiv = document.getElementById("result");

  if (!english) {
    resultDiv.textContent = "Παρακαλώ επιλέξτε επίπεδο αγγλικών.";
    resultDiv.style.color = "red";
    return;
  }

  const englishLevel = english.value;

  const meetsYear = year >= 2;
  const meetsPercentage = percentage >= 70;
  const meetsAvg = avg >= 6.5;
  const meetsEnglish = ["B2", "C1", "C2"].includes(englishLevel);

  if (meetsYear && meetsPercentage && meetsAvg && meetsEnglish) {
    resultDiv.textContent = "✅ Συγχαρητήρια! Πληροίτε τις ελάχιστες απαιτήσεις για συμμετοχή στο Erasmus.";
    resultDiv.style.color = "green";
  } else {
    resultDiv.textContent = "❌ Δυστυχώς δεν πληροίτε όλες τις απαιτήσεις για συμμετοχή.";
    resultDiv.style.color = "red";
  }
});
