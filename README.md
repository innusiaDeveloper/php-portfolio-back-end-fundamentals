# Login / Registrierungsformular

Kurze Beschreibung des Projekts.

## Anforderungen

-Docker
-Mac OS

## 1. Docker Desktop installieren

1.Laden Docker Desktop für Mac von der offiziellen Website herunter: [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)
2.Führen die Installationsdatei aus und folgen Sie den Anweisungen auf dem Bildschirm.
3.Starten Docker Desktop nach der Installation.
4.Wenn dazu aufgefordert werden, Docker Desktop in Ihren Systemeinstellungen Zugriff zu gewähren, erlauben Sie dies.

## 2. Datenbank und Tabelle erstellen

1.Öffnen Docker Desktop.
2.Suchen in der Suchleiste nach dem Image `mariadb`.
3.Klicken auf die Schaltfläche "Ausführen" neben dem Image `mariadb`.
4.Warten bis der MariaDB-Container gestartet ist.
5.Öffnen ein Terminal und geben Sie den folgenden Befehl ein:

bash
docker exec -it <name_des_mariadb_containers> bash

6.Verbinden innerhalb des Containers mit MariaDB:

bash
mysql -u root -p

Geben das Passwort (`my-secret-pw`) für den Benutzer `root` ein.

7.Erstellen die Datenbank `mariadb`:

sql
CREATE DATABASE mariadb;

8.Verwenden die erstellte Datenbank:

sql
USE mariadb;

9.Erstellen die Tabelle `users`:

sql
CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL,
password VARCHAR(255) NOT NULL,
gender VARCHAR(10) NOT NULL,
country VARCHAR(255) NOT NULL
);

10.Beenden MariaDB:

sql
exit

11.Beenden den Container:

bash
exit

## 3. Mit phpMyAdmin verbinden

1.Öffnen Docker Desktop und suchen Sie nach dem Image `phpmyadmin`.
2.Klicken auf die Schaltfläche "Ausführen" neben dem Image `phpmyadmin`.
3.Warten bis der phpMyAdmin-Container gestartet ist.
4.Öffnen Sie in Ihrem Browser:

localhost:8080

5.Geben den Benutzernamen `root` und das Passwort `my-secret-pw` ein.
6.Wählen die Datenbank `mariadb` aus und arbeiten Sie mit der Tabelle `users`.

## 4. PHP-Code (OOP und PDO)

php

<?php
class Database
{
    private $servername = 'db';
    private $username_db = 'root';
    private $password_db = 'my-secret-pw';
    private $dbname = 'mariadb';
    private $conn = null;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username_db, $this->password_db);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            echo "Database connection failed: " . $e->getMessage();
            $this->conn = null;
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

## 5. Composer und zusätzliche Anwendungen installieren

Composer installieren
1. Öffnen ein Terminal.
2. Führen den folgenden Befehl aus, um Composer herunterzuladen und zu installieren.

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

3. Überprüfen die Installation, indem Sie den folgenden Befehl ausführen:

composer --version

## 6. Zusätzliche Anwendungen installieren

1. Öffnen die Dockerfile und stellen Sie sicher, dass die folgenden Zeilen vorhanden sind, um alle erforderlichen Abhängigkeiten zu installieren:

RUN apt-get update && \
    apt-get install -y libpng-dev libpq-dev libyaml-dev zip unzip nano libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
    pecl install yaml && \
    docker-php-ext-configure gd --with-jpeg --with-freetype && \
    docker-php-ext-install pdo pdo_mysql gd exif && \
    docker-php-ext-enable yaml

RUN a2enmod rewrite
RUN service apache2 restart

2. Fügen Sie folgende Zeile hinzu, um den Webserver im Vordergrund zu starten und Composer-Abhängigkeiten zu installieren:

CMD composer install --no-interaction --optimize-autoloader;apache2-foreground

## 7. Port 80 für den Webserver freigeben

1. Fügen Sie die folgende Zeile in der Dockerfile hinzu, um den Port 80 freizugeben:

EXPOSE 80
