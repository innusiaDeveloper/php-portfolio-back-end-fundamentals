<?php
session_start();
session_destroy(); // Beendet die aktuelle Sitzung
header("Location: login.php"); // Leitet den Benutzer zur Anmeldeseite weiter
exit();
