function validateForm() {
  const fname = document.getElementById("fname").value;
  const lname = document.getElementById("lname").value;
  const am = document.getElementById("am").value;
  const phone = document.getElementById("phone").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm-password").value;

  const nameRegex = /^[Α-Ωα-ωΆ-Ώά-ώA-Za-z]+$/;
  const amRegex = /^2022\d+$/;
  const phoneRegex = /^\d{10}$/;

  if (!nameRegex.test(fname)) {
    alert("Το όνομα πρέπει να περιέχει μόνο γράμματα.");
    return false;
  }

  if (!nameRegex.test(lname)) {
    alert("Το επίθετο πρέπει να περιέχει μόνο γράμματα.");
    return false;
  }

  if (!amRegex.test(am)) {
    alert("Ο αριθμός μητρώου πρέπει να ξεκινά με 2022 και να έχει συνολικά 9 ψηφία.");
    return false;
  }

  if (!phoneRegex.test(phone)) {
    alert("Το τηλέφωνο πρέπει να έχει ακριβώς 10 ψηφία.");
    return false;
  }

  if (password.length < 5) {
    alert("Ο κωδικός πρέπει να έχει τουλάχιστον 5 χαρακτήρες.");
    return false;
  }

  if (password !== confirmPassword) {
    alert("Οι κωδικοί δεν ταιριάζουν.");
    return false;
  }

  return true;
}
