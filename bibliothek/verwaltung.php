<?php
include 'header.php'; 
//Prüft ob ein Admin angemeldet ist
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "bibliothek");
$conn->set_charset("utf8");
if ($conn->connect_error) die("DB-Fehler: " . $conn->connect_error);

//Buch löschen (zuerst alle Ausleihen löschen)
if (isset($_GET['delete'])) {
    $isbn = $conn->real_escape_string($_GET['delete']);
    
    //Alle Ausleihen des Buches löschen
    $conn->query("DELETE FROM ausleihen WHERE isbn='$isbn'");
    
    //Buch löschen
    $conn->query("DELETE FROM buch WHERE isbn='$isbn'");
    
    //Seite neu laden
    header("Location: verwaltungs.php");
    exit;
}

//Buch hinzufügen
if (isset($_POST['add'])) {
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $titel = $conn->real_escape_string($_POST['titel']);
    $autor = $conn->real_escape_string($_POST['autor']);
    $preis = (float)$_POST['preis'];
    //Neues Buch in die Datenbank einfügen
    $conn->query("INSERT INTO buch (isbn, titel, autor, preis, status) VALUES ('$isbn','$titel','$autor','$preis','verfügbar')");
    
    header("Location: verwaltungs.php");
    exit;
}

//Buch ausleihen
if (isset($_POST['borrow'])) {
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $nutzer_id = (int)$_POST['nutzer_id'];
    $admin_id = $_SESSION['admin_id'];
    $datum = date("Y-m-d");
//Neuen Eintrag in Ausleihen erstellen
    $conn->query("INSERT INTO ausleihen (nutzer_id, isbn, bibliothekar_id, datum, leihstatus) VALUES ('$nutzer_id','$isbn','$admin_id','$datum','ausgeliehen')");
    $conn->query("UPDATE buch SET status='ausgeliehen' WHERE isbn='$isbn'");
    
    header("Location: verwaltungs.php");
    exit;
}

//Buch zurückgeben
if (isset($_GET['return'])) {
    $id = (int)$_GET['return'];
    $heute = date("Y-m-d");
    //Leihstatus auf zurückgegeben setzen
    $conn->query("UPDATE ausleihen SET leihstatus='zurückgegeben', rueckgabedatum='$heute' WHERE ausleihen_id='$id'");
    //Buchstatus auf verfügbar setzen
    $isbn = $conn->query("SELECT isbn FROM ausleihen WHERE ausleihen_id='$id'")->fetch_assoc()['isbn'];
    $conn->query("UPDATE buch SET status='verfügbar' WHERE isbn='$isbn'");
    
    header("Location: verwaltungs.php");
    exit;
}

//Daten für Anzeige holen
$buecher = $conn->query("SELECT * FROM buch ORDER BY titel");
$nutzer  = $conn->query("SELECT * FROM nutzer");
//Ausgeliehene Bücher anzeigen
$ausgeliehen = $conn->query("
    SELECT a.ausleihen_id, b.titel, n.vname, n.name
    FROM ausleihen a
    JOIN buch b ON a.isbn = b.isbn
    JOIN nutzer n ON a.nutzer_id = n.nutzer_id
    WHERE a.leihstatus='ausgeliehen'
");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="David Danninger">
    <title>Bücherverwaltung</title>
</head>
<body>

<!-- Link zur Startseite -->
<h1>Bücherverwaltung</h1>

<!-- Neues Buch hinzufügen -->
<form method="post">
    ISBN: <input name="isbn" required>
    Titel: <input name="titel" required>
    Autor: <input name="autor" required>
    Preis: <input type="number" step="0.01" name="preis" required>
    <button name="add">Hinzufügen</button>
</form>

<hr>
<table border="1" cellpadding="5">
<tr>
    <th>ISBN</th>
    <th>Titel</th>
    <th>Autor</th>
    <th>Preis</th>
    <th>Status</th>
    <th>Aktionen</th>
</tr>

<?php while($b = $buecher->fetch_assoc()): ?>
<tr>
    <td><?= $b['isbn'] ?></td>
    <td><?= $b['titel'] ?></td>
    <td><?= $b['autor'] ?></td>
    <td><?= $b['preis'] ?> €</td>
    <td><?= $b['status'] ?></td>
    <td>
        <!-- Buch löschen -->
        <a href="?delete=<?= $b['isbn'] ?>" onclick="return confirm('Buch löschen?')">Löschen</a>
        
        <!-- Buch ausleihen falls verfügbar -->
        <?php if($b['status']=='verfügbar'): ?>
        <form method="post" style="display:inline;">
            <input type="hidden" name="isbn" value="<?= $b['isbn'] ?>">
            <select name="nutzer_id">
                <?php while($n = $nutzer->fetch_assoc()): ?>
                    <option value="<?= $n['nutzer_id'] ?>"><?= $n['vname']." ".$n['name'] ?></option>
                <?php endwhile; ?>
            </select>
            <button name="borrow">Ausleihen</button>
        </form>
        <?php $nutzer->data_seek(0); endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

<hr>
<h2>Aktive Ausleihen</h2>
<table border="1" cellpadding="5">
<tr>
    <th>Buch</th>
    <th>Ausgeliehen von</th>
    <th>Aktion</th>
</tr>
<?php while($a = $ausgeliehen->fetch_assoc()): ?>
<tr>
    <td><?= $a['titel'] ?></td>
    <td><?= $a['vname']." ".$a['name'] ?></td>
    <td><a href="?return=<?= $a['ausleihen_id'] ?>">Zurückgeben</a></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
<?php $conn->close(); ?>
