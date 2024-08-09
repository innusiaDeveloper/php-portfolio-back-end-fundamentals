<?php

include './config/database.php';
class RegistrationForm
{
    private $username;
    private $email;
    private $password;
    private $passwordRepeat;
    private $gender;
    private $country;
    private $agbs;
    private $errors = [];
    private $successMessage;
    private $db;

    // Initialisiert die Formulardaten und erstellt eine Datenbankverbindung.
    public function __construct()
    {
        $this->username = $_POST['username'] ?? '';
        $this->email = $_POST['email'] ?? '';
        $this->password = $_POST['password'] ?? '';
        $this->passwordRepeat = $_POST['password_repeat'] ?? '';
        $this->gender = $_POST['gender'] ?? '';
        $this->country = $_POST['country'] ?? '';
        $this->agbs = isset($_POST['agbs']) ? true : false;
        $this->db = new Database();
    }


    // Verarbeitet die Formularübermittlung, validiert die Eingaben und führt die Registrierung durch, falls keine Fehler vorliegen.
    public function handleFormSubmission()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateForm();
            if (empty($this->errors)) {
                $this->processRegistration();
            }
        }
    }

    // Überprüft die Eingaben des Benutzers auf Gültigkeit und fügt entsprechende Fehlermeldungen hinzu.
    private function validateForm()
    {
        // Benutzernamen validieren
        if (empty($this->username)) {
            $this->errors['username'][] = 'Nutzername ist erforderlich';
        } elseif (strlen($this->username) < 4) {
            $this->errors['username'][] = 'Nutzername muss mindestens 4 Zeichen enthalten';
        } elseif (strlen($this->username) > 16) {
            $this->errors['username'][] = 'Nutzername darf maximal 16 Zeichen enthalten';
        } elseif (preg_match('/\s/', $this->username)) {
            $this->errors['username'][] = 'Nutzername darf keine Leerzeichen enthalten';
        } else {
            // Auf Benutzerexistenz prüfen
            $this->checkIfUsernameExists();
        }

        // E-Mail-Validierung
        if (empty($this->email)) {
            $this->errors['email'][] = 'E-Mail Adresse ist erforderlich';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'][] = 'Ungültige E-Mail Adresse';
        } else {
            // Überprüfen ob eine E-Mail vorhanden ist
            $this->checkIfEmailExists();
        }

        /// Passwortvalidierung
        if (empty($this->password)) {
            $this->errors['password'][] = 'Passwort ist erforderlich';
        } elseif (strlen($this->password) < 8) {
            $this->errors['password'][] = 'Passwort muss mindestens 8 Zeichen enthalten';
        } elseif (!preg_match('/[a-z]/', $this->password)) {
            $this->errors['password'][] = 'Passwort muss mindestens einen Kleinbuchstaben enthalten';
        } elseif (!preg_match('/[A-Z]/', $this->password)) {
            $this->errors['password'][] = 'Passwort muss mindestens einen Großbuchstaben enthalten';
        } elseif (!preg_match('/\d/', $this->password)) {
            $this->errors['password'][] = 'Passwort muss mindestens eine Zahl enthalten';
        } elseif (!preg_match('/[\W]/', $this->password)) {
            $this->errors['password'][] = 'Passwort muss mindestens ein Sonderzeichen enthalten';
        } elseif (preg_match('/\s/', $this->password)) {
            $this->errors['password'][] = 'Passwort darf keine Leerzeichen enthalten';
        }

        // Validierung passwort_repeat
        if (empty($this->passwordRepeat)) {
            $this->errors['password_repeat'][] = 'Passwort wiederholen ist erforderlich';
        } elseif ($this->password !== $this->passwordRepeat) {
            $this->errors['password_repeat'][] = 'Passwörter stimmen nicht überein';
        }

        // Geschlechtsvalidierung
        if (empty($this->gender)) {
            $this->errors['gender'][] = 'Geschlecht ist erforderlich';
        }

        // Validierungsland
        if (empty($this->country)) {
            $this->errors['country'][] = 'Land ist erforderlich';
        }

        // AGBs validieren
        if (empty($this->agbs)) {
            $this->errors['agbs'][] = 'Sie müssen die AGBs akzeptieren';
        }
    }


    // Prüft, ob der eingegebene Benutzername bereits in der Datenbank existiert.
    private function checkIfUsernameExists()
    {
        $conn = $this->db->getConnection();
        if ($conn === null) {
            $this->errors['db'][] = "Database connection failed.";
            return;
        }

        try {
            $stmt = $conn->prepare("SELECT username FROM users WHERE username = :username");
            $stmt->bindParam(':username', $this->username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $this->errors['username'][] = "Username is already taken.";
            }
        } catch (PDOException $e) {
            $this->errors['db'][] = "Database error: " . $e->getMessage();
        }
    }


    // Prüft, ob die eingegebene E-Mail-Adresse bereits in der Datenbank existiert.
    private function checkIfEmailExists()
    {
        $conn = $this->db->getConnection();
        if ($conn === null) {
            $this->errors['db'][] = "Database connection failed.";
            return;
        }

        try {
            $stmt = $conn->prepare("SELECT email FROM users WHERE email = :email");
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $this->errors['email'][] = "Email is already taken.";
            }
        } catch (PDOException $e) {
            $this->errors['db'][] = "Database error: " . $e->getMessage();
        }
    }


    // Registriert den Benutzer in der Datenbank, wenn die Validierung erfolgreich war, und leitet ihn zur Anmeldeseite weiter.
    private function processRegistration()
    {
        $conn = $this->db->getConnection();
        if ($conn === null) {
            $this->errors['db'][] = "Database connection failed.";
            return;
        }

        try {
            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, gender, country, agbs) VALUES (:username, :email, :password, :gender, :country, :agbs)");
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':country', $this->country);
            $stmt->bindParam(':agbs', $this->agbs, PDO::PARAM_BOOL);

            if ($stmt->execute()) {
                $this->successMessage = "Registration successful. Please log in.";
                header("Location: login.php");
                exit();
            } else {
                $this->errors['db'][] = "Database error: Could not insert user.";
            }
        } catch (PDOException $e) {
            $this->errors['db'][] = "Database error: " . $e->getMessage();
        }
    }

    // Gibt ein Array mit allen aufgetretenen Fehlern zurück.
    public function getErrors()
    {
        return $this->errors;
    }


    // Gibt die Erfolgsmeldung zurück, wenn die Registrierung erfolgreich war.
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    // Gibt den eingegebenen Benutzernamen zurück.
    public function getUsername()
    {
        return $this->username;
    }

    // Gibt die eingegebene E-Mail-Adresse zurück.
    public function getEmail()
    {
        return $this->email;
    }

    // Gibt das ausgewählte Geschlecht zurück.
    public function getGender()
    {
        return $this->gender;
    }

    // Gibt das ausgewählte Land zurück.
    public function getCountry()
    {
        return $this->country;
    }

    // Gibt zurück, ob die AGBs akzeptiert wurden.
    public function getAgbs()
    {
        return $this->agbs;
    }


    // Verarbeitet die Anmeldung, überprüft die Eingaben und startet eine Sitzung bei erfolgreicher Anmeldung.
    public function processLogin($username, $password)
    {
        // Einfache Validierung
        if (empty($username) || empty($password)) {
            $this->errors['login'][] = "Bitte geben Sie sowohl Benutzername als auch Passwort ein.";
            return;
        }

        // Eingabedaten bereinigen
        $username = filter_var($username, ENT_QUOTES, 'UTF-8');

        try {
            $conn = $this->db->getConnection();

            // Vorbereitete Abfrage zur Verhinderung von SQL-Injections
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Passwortüberprüfung
                if (password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $username;

                    // Weiterleitung nach erfolgreicher Anmeldung
                    header("Location: session_info.php");
                    exit();
                } else {
                    $this->errors['password'][] = "Falsches Passwort.";
                }
            } else {
                $this->errors['username'][] = "Nutzername nicht gefunden.";
            }
        } catch (PDOException $e) {
            $this->errors['db'][] = "Datenbankfehler: " . $e->getMessage();
        }
    }
}

$form = new RegistrationForm();
$form->handleFormSubmission();
