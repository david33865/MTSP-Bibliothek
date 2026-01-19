<?php
session_start();
//Header einbinden
include 'header.php';
//Überprüfen ob Admin eingeloggt ist
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}
//Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "", "bibliothek");
$conn->set_charset("utf8");
if ($conn->connect_error) die("DB Fehler: " . $conn->connect_error);
//Hilfsfunktionen
function es($str) { global $conn; return $conn->real_escape_string($str); }
function redirect() { header("Location: verwaltung.php"); exit(); }
//Buch hinzufügen oder aktualisieren
if (isset($_POST['add']) || isset($_POST['update'])) {
    $isbn = es($_POST['isbn']);
    $titel = es($_POST['titel']);
    $autor = es($_POST['autor']);
    $verlag = es($_POST['verlag']);
    $preis = floatval($_POST['preis']);
//Buch hinzufügen
    if (isset($_POST['add'])) {
        //SQL Befehl zum Einfügen eines neuen Buches
        $conn->query("INSERT INTO buch (isbn, titel, autor, verlag, preis, status)
                      VALUES ('$isbn', '$titel', '$autor', '$verlag', '$preis', 'verfügbar')");
    } else {
        //SQL Befehl zum Aktualisieren eines Buches
        $conn->query("UPDATE buch SET titel='$titel', autor='$autor', verlag='$verlag', preis='$preis'
                      WHERE isbn='$isbn'");
    }
    //Seit neu laden
    redirect();
}
//Buch löschen
if (isset($_GET['delete'])) {
    $isbn = es($_GET['delete']);
    $conn->query("DELETE FROM ausleihen WHERE isbn='$isbn'");
    $conn->query("DELETE FROM buch WHERE isbn='$isbn'");
    redirect();
}
//Buch ausleihen
if (isset($_POST['borrow'])) {
    $isbn = es($_POST['isbn']);
    $nutzer = intval($_POST['nutzer_id']);
    $datum = date("Y-m-d");
    //Buch ausleihen und Status aktualisieren
    $conn->query("INSERT INTO ausleihen (nutzer_id, isbn, bibliothekar_id, datum, leihstatus)
                  VALUES ('$nutzer', '$isbn', '{$_SESSION["admin_id"]}', '$datum', 'ausgeliehen')");
    $conn->query("UPDATE buch SET status='ausgeliehen' WHERE isbn='$isbn'");
    redirect();
}
//Buch zurückgeben
if (isset($_GET['return'])) {
    $id = intval($_GET['return']);
    $heute = date("Y-m-d");
    //Leihstatus aktualisieren und Buchstatus ändern
    $conn->query("UPDATE ausleihen SET leihstatus='zurückgegeben', rueckgabedatum='$heute'
                  WHERE ausleihen_id='$id'");

    $isbn = $conn->query("SELECT isbn FROM ausleihen WHERE ausleihen_id='$id'")
                 ->fetch_assoc()['isbn'];

    $conn->query("UPDATE buch SET status='verfügbar' WHERE isbn='$isbn'");
    redirect();
}
//Daten für Anzeige abrufen
$buecher = $conn->query("SELECT * FROM buch ORDER BY titel ASC");
$nutzer_list = $conn->query("SELECT * FROM nutzer ORDER BY vname ASC")->fetch_all(MYSQLI_ASSOC);
$ausgeliehen = $conn->query("
    SELECT a.ausleihen_id, b.titel, n.vname, n.name
    FROM ausleihen a
    JOIN buch b ON a.isbn = b.isbn
    JOIN nutzer n ON a.nutzer_id = n.nutzer_id
    WHERE a.leihstatus='ausgeliehen'
");
//Bücher bearbeiten
$edit_isbn = $_GET['edit'] ?? null;
$edit_data = $edit_isbn ? $conn->query("SELECT * FROM buch WHERE isbn='".es($edit_isbn)."'")->fetch_assoc() : null;
?>

<div class="container">

    <h1 class="mb-4">Bücherverwaltung</h1>

    <h2>Neues Buch hinzufügen</h2>

    <form method="post" class="row g-3 mb-4">
        <div class="col-md-2"><input name="isbn" class="form-control" placeholder="ISBN" required></div>
        <div class="col-md-3"><input name="titel" class="form-control" placeholder="Titel" required></div>
        <div class="col-md-2"><input name="autor" class="form-control" placeholder="Autor" required></div>
        <div class="col-md-2"><input name="verlag" class="form-control" placeholder="Verlag" required></div>
        <div class="col-md-2"><input type="number" step="0.01" name="preis" class="form-control" placeholder="Preis" required></div>
        <div class="col-md-1"><button name="add" class="btn btn-success w-100">+</button></div>
    </form>

    <?php if ($edit_data): ?>
        <h2>Buch bearbeiten</h2>

        <form method="post" class="row g-3 mb-4">
            <div class="col-md-2"><input name="isbn" class="form-control" value="<?= $edit_data['isbn'] ?>" readonly></div>
            <div class="col-md-3"><input name="titel" class="form-control" value="<?= $edit_data['titel'] ?>"></div>
            <div class="col-md-2"><input name="autor" class="form-control" value="<?= $edit_data['autor'] ?>"></div>
            <div class="col-md-2"><input name="verlag" class="form-control" value="<?= $edit_data['verlag'] ?>"></div>
            <div class="col-md-2"><input type="number" step="0.01" name="preis" class="form-control" value="<?= $edit_data['preis'] ?>"></div>
            <div class="col-md-1"><button name="update" class="btn btn-primary w-100">Speichern</button></div>
        </form>
    <?php endif; ?>

    <h2>Bücherliste</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ISBN</th>
                    <th>Titel</th>
                    <th>Autor</th>
                    <th>Verlag</th>
                    <th>Preis</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            //Bücher aus der Datenbank anzeigen
            while ($b = $buecher->fetch_assoc()): ?>
                <tr>
                    <td><?= $b['isbn'] ?></td>
                    <td><?= $b['titel'] ?></td>
                    <td><?= $b['autor'] ?></td>
                    <td><?= $b['verlag'] ?></td>
                    <td><?= $b['preis'] ?> €</td>
                    <td><?= $b['status'] ?></td>
                    <td>
                        <a href="?edit=<?= $b['isbn'] ?>" class="btn btn-sm btn-primary">Bearbeiten</a>
                        <a href="?delete=<?= $b['isbn'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Löschen?')">Löschen</a>

                        <?php if ($b['status'] == 'verfügbar'): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="isbn" value="<?= $b['isbn'] ?>">
                                <select name="nutzer_id" class="form-select form-select-sm d-inline w-auto">
                                    <?php foreach ($nutzer_list as $n): ?>
                                        <option value="<?= $n['nutzer_id'] ?>">
                                            <?= $n['vname'] . " " . $n['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button name="borrow" class="btn btn-sm btn-success">Ausleihen</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <h2>Aktive Ausleihen</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Buch</th>
                    <th>Nutzer</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($a = $ausgeliehen->fetch_assoc()): ?>
                <tr>
                    <td><?= $a['titel'] ?></td>
                    <td><?= $a['vname'] . " " . $a['name'] ?></td>
                    <td><a href="?return=<?= $a['ausleihen_id'] ?>" class="btn btn-sm btn-warning">Zurückgeben</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

<?php $conn->close(); ?>
