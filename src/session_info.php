<?php
session_start();

//Überprüfen, ob der Benutzer berechtigt ist
if (!isset($_SESSION['user_id'])) {
    // Wenn der Benutzer nicht autorisiert ist, leiten ihn zur Anmeldeseite weiter
    header("Location: login.php");
    exit();
}

// Sitzungsdaten anzeigen
echo "<h1>Session Information</h1>";
echo "<pre>";
var_dump($_SESSION); //die Session ausgibt var_dump
echo "</pre>";
