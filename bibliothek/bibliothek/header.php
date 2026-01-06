<?php
//Session wird nur gestartet falls noch keine aktiv ist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="David Danninger">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothek</title>
    <link rel="stylesheet" href="/uebungen/bibliothek/style.css?v=2">
</head>
<body>

<!-- Navigationsleiste -->
<div class="navbar">
    <a href="index.php">Startseite</a>
    <a href="buecher.php">Bücherverwaltung</a>

    <?php
     //Prüft ob ein Admin angemeldet ist
    if (isset($_SESSION["admin_id"])) {
        // Begrüßung + Logout anzeigen
        echo '<a href="logout.php">Logout</a>';
    } else {
        //Login anzeigen falls kein Admin angemeldet ist
        echo '<a href="login.php">Login</a>';
    }
        
    ?>
</div>
