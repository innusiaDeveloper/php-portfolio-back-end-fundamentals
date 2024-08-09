document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("#uploadForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(form);

      fetch("blog.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            alert("Upload successful!");
            // Aktualisiertes HTML aus der Serverantwort abrufen
            const postsSection = data.html; // Nehmen wir an, der Server gibt HTML als data.html zurück

            // Den Inhalt des Abschnitts #posts aktualisieren
            document.getElementById("posts").innerHTML = postsSection;
          } else {
            alert(
              "Upload failed! " + (data.errors ? data.errors.join(", ") : "")
            );
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  } else {
    console.error("Form with id 'uploadForm' not found.");
  }
});
