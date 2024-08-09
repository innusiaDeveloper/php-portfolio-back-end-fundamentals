/* -------------------------------------------------------------------------------------- */
/* ---------------------------------Formular Validierung--------------------------------- */
/* -------------------------------------------------------------------------------------- */

document.addEventListener("DOMContentLoaded", function () {
  form.addEventListener("submit", function (event) {
    event.preventDefault();

    let validationSuccess = true;

    // Benutzernamen prüfen
    if (usernameError) {
      validationSuccess = false;
      document.getElementById("usernameError").textContent = usernameError;
      document.getElementById("usernameError").style.display = "block";
    } else {
      document.getElementById("usernameError").style.display = "none";
    }

    // ... (prüft auf E-Mail, Passwort usw.)

    if (validationSuccess) {
      form.submit();
    }
  });
});
