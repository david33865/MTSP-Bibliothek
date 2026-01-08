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
    <title>Login</title>
</head>
<body>
    
<?php
//Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "", "bibliothek");

//Datenbankverbindung in UTF8 öffnen
$conn->set_charset("utf8");

//Varaible für Fehlermeldung erstellen
$fehler = "";

//Prüft ob Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    //Eingaben sichern
    $email = $conn->real_escape_string($_POST["email"]);
    //Passwort mit SHA256 hashen
    $passwort = hash("sha256", $_POST["passwort"]);

    //SQL-Abfrage für den Login
    $sql = "SELECT * FROM bibliothekar 
            WHERE email = '$email' 
            AND passwort = '$passwort'";

    $result = $conn->query($sql);

    //Prüft ob genau ein Benutzer gefunden wurde
    if ($result->num_rows === 1) {

        //Admin-Daten holen
        $admin = $result->fetch_assoc();

        //Session-Variablen setzen
        $_SESSION["admin_id"] = $admin["bibliothekar_id"];
        $_SESSION["admin_name"] = $admin["name"];
        $_SESSION["admin_vname"] = $admin["vname"];

        //nach login zur Startseite weiterleiten
        header("Location: index.php");
        exit;

    } else {
        //falls falsche Anmeldedaten eingegeben wurden
        $fehler = "Falsche Anmeldedaten";
    }
}
?>

<h1>Admin Login</h1>

<?php
//Fehlermeldung anzeigen falls vorhanden
if ($fehler != "") {
    echo "<p style='color:red;'>$fehler</p>";
}
?>

<form method="post">
    <label>E-Mail:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Passwort:</label><br>
    <input type="password" name="passwort" required><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
