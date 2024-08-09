<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Berechtigungsprüfung
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Datei in die Download-Verarbeitung einbinden
require_once './Fileupload.php';
require_once './config/database.php';

// Funktion zum Senden einer JSON-Antwort
function sendJsonResponse($data, $statusCode = 200)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

try {
    // Verarbeiten Sie die POST-Anfrage
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $uploadHandler = new FileUpload(); // Ein FileUpload-Objekt erstellen

        // Dateiinformationen und Alternativtext abrufen
        $fileData = $_FILES['bild'];
        $altText = $_POST['alt'];


        // Generate a date-coded path (e.g., 2022/12/31)
        $datePath = date('Y/m/d');
        $uploadDir = __DIR__ . '/uploads/' . $datePath;

        // Rufet die Datei-Upload-Methode von FileUpload auf
        $result = $uploadHandler->uploadFile($fileData, $altText);

        // Abhängig vom Ergebnis eine JSON-Antwort senden
        if ($result) {
            // Mit der Datenbank verbinden und alle Beiträge abrufen
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC");
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ob_start();
            foreach ($posts as $post) {
                $path = htmlspecialchars($post['path'], ENT_QUOTES, 'UTF-8');
                $alt = htmlspecialchars($post['alt'], ENT_QUOTES, 'UTF-8');
                echo '<div class="post">';
                echo '<img src="' . $path . '" alt="' . $alt . '">';
                echo '<p>' . $alt . '</p>';
                echo '</div>';
            }
            $postsHtml = ob_get_clean();

            sendJsonResponse(['success' => true, 'html' => $postsHtml], 201);
        } else {
            sendJsonResponse(['success' => false, 'errors' => $uploadHandler->getErrors()], 422);
        }
    }

    // Mit der Datenbank verbinden und alle Beiträge abrufen
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    sendJsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog</title>
    <link rel="stylesheet" href="./assets/style/style.css" />
    <link rel="stylesheet" href="./assets//style/blog.css" />
    <script src="./assets/js/main.js" defer></script>
    <script src="./assets/js/upload.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header>
        <div class="wrapper-header">
            <div class="header">
                <div class="logo">
                    <a href="#">
                        <img class="desktop-logo" src="./assets/img/logo-homepage-nav.svg" alt="Creative Event Solutions Logo" />
                    </a>
                    <a href="#">
                        <img class="mobile-logo" src="./assets/img/mobile.svg" alt="Creative Event Solutions Mobile Logo" />
                    </a>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-button">Registrieren</a>
                    <a href="blog.php" class="nav-button">Blog</a>
                    <a href="logout.php" class="nav-button">Ausloggen</a>
                    <a href="#"><img src="./assets/img/icon-instagram-black.svg" alt="Instagram" /></a>
                    <a href="#"><img src="./assets/img/icon-facebook-black.svg" alt="Facebook" /></a>
                </nav>
                <div class="burger">
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    <main>
        <section id="content">
            <div class="wrapper">
                <h1>Willkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>In unserem Blog können Sie Ihre Eindrücke und Erlebnisse teilen, angefangen bei Veranstaltungen mit Kollegen bis hin zu Treffen mit Kunden. Dieser Blog wird eine Plattform für ehrliches Feedback über unser Unternehmen und seine Partner sein und es uns ermöglichen, die Bedürfnisse und Erwartungen besser zu verstehen. Nehmen Sie an den Diskussionen teil und teilen Sie Ihre Geschichten!</p>
                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                    <label for="alt">Text:</label>
                    <input type="text" name="alt" id="alt">
                    <label for="bild">Bild:</label>
                    <input type="file" name="bild" id="bild" accept="image/*">
                    <button type="submit">Upload</button>
                </form>

                <!-- Fügen einen Abschnitt hinzu, um heruntergeladene Bilder anzuzeigen -->
                <section id="posts">
                    <?php
                    foreach ($posts as $post) {
                        $path = htmlspecialchars($post['path'], ENT_QUOTES, 'UTF-8');
                        $alt = htmlspecialchars($post['alt'], ENT_QUOTES, 'UTF-8');
                        echo '<div class="post">';
                        echo '<img src="' . $path . '" alt="' . $alt . '">';
                        echo '<p>' . $alt . '</p>';
                        echo '</div>';
                    }

                    ?>
                </section>
            </div>
        </section>
    </main>
    <footer>
        <div class="container">
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="#">Kontakt</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Impressum</a></li>
                    <li><a href="#">Datenschutz</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Kontakt</h4>
                <ul>
                    <li><a href="#">Kaufingerstr.5</a></li>
                    <li><a href="#">80333 München</a></li>
                    <li><a href="#">+49(0)39-35-665</a></li>
                    <li><a href="#">info@kontakt.com</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>© 2024 Creativ Event Solutions</h4>
                <div class="social-links">
                    <a href="#"><img src="./assets/img/icon-facebook-white.svg" alt="facebook" /></a>
                    <a href="#"><img src="./assets/img/icon-Instagram-white.svg" alt="instagram" /></a>
                    <a href="#"><img src="./assets/img/icon-linkedIn-white.svg" alt="inkedin" /></a>
                    <a href="#"><img src="./assets/img/icon-xing-white.svg" alt="inkedin" /></a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>