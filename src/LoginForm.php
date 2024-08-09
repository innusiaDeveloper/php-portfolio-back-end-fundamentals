<?php
class LoginForm
{
    private $errors = [];
    private $successMessage = "";


    // Verarbeitet die Übermittlung des Anmeldeformulars, überprüft, ob die Felder ausgefüllt sind, und leitet den Anmeldeprozess ein.
    public function handleFormSubmission()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? "";
            $password = $_POST['password'] ?? "";

            if (empty($username)) {
                $this->errors['username'] = "Nutzername darf nicht leer sein.";
            }

            if (empty($password)) {
                $this->errors['password'] = "Passwort darf nicht leer sein.";
            }

            if (empty($this->errors)) {
                $this->processLogin($username, $password);
            }
        }
    }

    // Stellt eine Verbindung zur Datenbank her, überprüft die Anmeldedaten des Benutzers und startet eine Sitzung bei erfolgreicher Anmeldung.
    private function processLogin($username, $password)
    {
        require_once './config/database.php';
        $database = new Database();
        $conn = $database->getConnection();

        if ($conn === null) {
            $this->errors['database'] = "Datenbankverbindung fehlgeschlagen.";
            return;
        }

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;

                header("Location: blog.php");
                exit();
            } else {
                $this->errors['password'] = "Falsches Passwort.";
            }
        } else {
            $this->errors['username'] = "Nutzername nicht gefunden.";
        }
    }

    // Gibt ein Array mit Fehlern zurück, die bei der Verarbeitung des Formulars aufgetreten sind.
    public function getErrors()
    {
        return $this->errors;
    }

    // Gibt eine Erfolgsmeldung zurück (wird im aktuellen Code nicht verwendet).
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }
}
