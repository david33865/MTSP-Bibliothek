<?php
// Navbar einbinden
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="David Danninger">
    <title>Startseite</title>
</head>
<body>
    <h1>Bibliothek Startseite</h1>

<?php
//Willkommensnachricht anzeigen
if (isset($_SESSION["admin_id"])) {
    echo "<h2>Hallo " . $_SESSION["admin_vname"] . " " . $_SESSION["admin_name"] . "</h2>";
} else {
    echo "<h2>Hallo Gast!</h2>";
}
?>

<form method="post" action="">
    <label for="suche">Buchtitel oder Autor:</label>
    <input type="text" id="suche" name="suche" required>
    <button type="submit">Suchen</button>
</form>

<?php
// Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "", "bibliothek");

// Datenbankverbindung in UTF8 öffnen
$conn->set_charset("utf8");

//Prüft auf erfolgreiche Datenbank-Verbindung
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

//Prüft ob etwas gesucht wurde
if (isset($_POST['suche']) && $_POST['suche'] !== "") {

    //Schutz vor SQL-Injection
    $suche = $conn->real_escape_string($_POST['suche']);

    //SQL-Abfrage für Suche
    $sql = "SELECT isbn, titel, autor, verlag, preis, status 
            FROM buch 
            WHERE titel LIKE '%$suche%' 
               OR autor LIKE '%$suche%'";

    echo "<h2>Suchergebnisse:</h2>";

} else {

    //Alle Bücher anzeigen falls keins gesucht wurde
    $sql = "SELECT isbn, titel, autor, verlag, preis, status 
            FROM buch 
            ORDER BY titel ASC";

    echo "<h2>Alle Bücher:</h2>";
}

//Führt die Abfrage aus
$result = $conn->query($sql);

//Prüft Ergebnisse und gibt sie aus
if ($result && $result->num_rows > 0) {


    echo "<table>";
    echo "<tr>
            <th>ISBN</th>
            <th>Titel</th>
            <th>Autor</th>
            <th>Verlag</th>
            <th>Preis (€)</th>
            <th>Status</th>
          </tr>";

    //Daten ausgeben
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['isbn']}</td>
                <td>{$row['titel']}</td>
                <td>{$row['autor']}</td>
                <td>{$row['verlag']}</td>
                <td>{$row['preis']}</td>
                <td>{$row['status']}</td>
              </tr>";
    }

    echo "</table>";

} else {
    //falls keine Bücher gefunden wurden
    echo "<p>Keine Bücher gefunden.</p>";
}

//Datenbankverbindung schließen
$conn->close();
?>

</body>
</html>
