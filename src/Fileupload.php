<?php
require_once './config/database.php';

class FileUpload
{

    // Zulässige Dateitypen und maximale Dateigröße
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxSize = 2000000; // 2 MB
    private $uploadPath;
    private $errors = [];
    private $db;

    // Klassenkonstruktor, initialisiert die Verbindung zur Datenbank und den Pfad zum Download-Verzeichnis
    public function __construct()
    {
        $this->db = new Database();
        $this->uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads'; // Pfad zum Upload-Ordner
    }

    // Hauptmethode zum Herunterladen einer Datei, prüft die Daten und speichert die Datei
    public function uploadFile(array $fileData, string $altText): bool
    {
        // Überprüfen Sie, ob die Datei heruntergeladen wurde
        if (!isset($fileData['tmp_name']) || !is_uploaded_file($fileData['tmp_name'])) {
            $this->errors[] = 'Keine Datei hochgeladen.';
            return false;
        }
        // Überprüfen ob der Alternativtext gültig ist
        if (!$this->validateAltText($altText)) {
            $this->errors[] = 'Ungültiger Alternativtext.';
            return false;
        }

        // Gültigkeit des Dateityps prüfen
        if (!$this->validateFileType($fileData['type'])) {
            $this->errors[] = 'Ungültiger Dateityp.';
            return false;
        }

        // Dateigröße prüfen
        if (!$this->validateFileSize($fileData['size'])) {
            $this->errors[] = 'Datei zu groß.';
            return false;
        }



        // Einen eindeutigen Dateinamen generieren
        $fileName = $this->generateFileName($fileData['name']);
        $filePath = $this->createFilePath($fileName);

        // Verschieben die heruntergeladene Datei in das Verzeichnis
        if (!move_uploaded_file($fileData['tmp_name'], $filePath)) {
            $this->errors[] = 'Fehler beim Verschieben der Datei.';
            return false;
        }


        // Miniaturansicht erstellen
        $thumbFileName = 'thumb_' . $fileName;
        $thumbFilePath = $this->createFilePath($thumbFileName);


        if (!$this->createThumbnail($filePath, $thumbFilePath, 150)) {
            $this->errors[] = 'Fehler beim Erstellen der Miniaturansicht.';
            return false;
        }

        // Relative Pfade zu Dateien
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
        $relativeThumbPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $thumbFilePath);


        //Dateiinformationen in der Datenbank speichern
        if (!$this->saveToDatabase($relativePath, $relativeThumbPath, $altText)) {
            $this->errors[] = 'Fehler beim Speichern in der Datenbank.';
            return false;
        }

        return true;
    }

    // Alternativtext validieren
    private function validateAltText(string $altText): bool
    {
        return !empty($altText) && strlen($altText) <= 255;
    }

    // Dateitypvalidierung
    private function validateFileType(string $fileType): bool
    {
        return in_array($fileType, $this->allowedTypes);
    }

    // Dateigrößenvalidierung
    private function validateFileSize(int $fileSize): bool
    {
        return $fileSize <= $this->maxSize;
    }

    // Einen eindeutigen Dateinamen basierend auf dem Original generieren
    private function generateFileName(string $originalFileName): string
    {
        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension; // Verhindert Duplikate
        return $fileName;
    }

    // Erstellen einen Pfad zum Speichern der Datei, einschließlich der Erstellung eines Verzeichnisses
    private function createFilePath(string $fileName): string
    {
        $dateCode = date('Y/m/d');
        $path = $this->uploadPath . '/' . $dateCode;

        if (!is_dir($path)) {
            mkdir($path, 0755, true); // Ordner erstellen, falls nicht vorhanden
        }

        return $path . '/' . $fileName;
    }

    //Dateiinformationen in der Datenbank speichern
    private function saveToDatabase(string $relativePath, string $relativeThumbPath, string $altText): bool
    {
        $conn = $this->db->getConnection();
        $sql = "INSERT INTO posts (path, thumbnail_path, alt, created_at, updated_at) VALUES (:path, :thumbnail_path, :alt, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':path', $relativePath);
        $stmt->bindParam(':thumbnail_path', $relativeThumbPath);
        $stmt->bindParam(':alt', $altText);

        return $stmt->execute();
    }

    // Ein Miniaturbild erstellen
    private function createThumbnail(string $filePath, string $thumbPath, int $thumbWidth): bool
    {
        if (!file_exists($filePath)) {
            $this->errors[] = 'Originalbild existiert nicht.';
            return false;
        }

        list($width, $height, $type) = getimagesize($filePath);
        if ($width === false || $height === false) {
            $this->errors[] = 'Fehler beim Ermitteln der Bildgröße.';
            return false;
        }

        $srcImage = null;

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($filePath);
                break;
            default:

                $this->errors[] = 'Nicht unterstützter Bildtyp.';

                return false;
        }

        if ($srcImage === false) {
            $this->errors[] = 'Fehler beim Erstellen des Bildes.';

            return false;
        }


        // Berechnen die Höhe des Miniaturbilds im Verhältnis zur Breite
        $thumbHeight = floor($height * ($thumbWidth / $width));
        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbImage, $thumbPath);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbImage, $thumbPath);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbImage, $thumbPath);
                break;
        }

        imagedestroy($srcImage);
        imagedestroy($thumbImage);
        return true;
    }

    // Gibt ein Array von Fehlern zurück
    public function getErrors(): array
    {
        return $this->errors;
    }
}

class UploadHandler
{
    private $fileUpload;

    public function __construct()
    {
        $this->fileUpload = new FileUpload();
    }

    public function uploadFile($fileData, $altText)
    {
        try {
            $success = $this->fileUpload->uploadFile($fileData, $altText);
            if (!$success) {
                throw new Exception(implode(', ', $this->fileUpload->getErrors()));
            }
            echo 'Datei erfolgreich hochgeladen.';
        } catch (Exception $e) {
            echo 'Fehler: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $handler = new UploadHandler();
    $handler->uploadFile($_FILES['file'], $_POST['alt']);
}
