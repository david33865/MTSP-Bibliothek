<?php 
//header einbinden
include 'header.php'; ?>
<div class="container" style="max-width: 500px;">

<?php
//Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "", "bibliothek");
$conn->set_charset("utf8");

$fehler = "";
//Überprüfen ob Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string($_POST["email"]);
    $passwort = hash("sha256", $_POST["passwort"]);
//SQL Abfrage zum Überprüfen der Anmeldedaten
    $sql = "SELECT * FROM bibliothekar WHERE email='$email' AND passwort='$passwort'";
    $result = $conn->query($sql);
//falls Anmeldedaten korrekt sind
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
//Session-Variablen setzen
        $_SESSION["admin_id"] = $admin["bibliothekar_id"];
        $_SESSION["admin_name"] = $admin["name"];
        $_SESSION["admin_vname"] = $admin["vname"];
//Weiterleitung zur Startseite
        header("Location: index.php");
        exit;
    } else {
        //falls Anmeldedaten falsch sind
        $fehler = "Falsche Anmeldedaten";
    }
}
?>

<h1 class="mb-4">Admin Login</h1>

<?php if ($fehler): ?>
    <div class="alert alert-danger"><?= $fehler ?></div>
<?php endif; ?>

<form method="post" class="card p-4 shadow-sm">
    <div class="mb-3">
        <label class="form-label">E-Mail</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Passwort</label>
        <input type="password" name="passwort" class="form-control" required>
    </div>

    <button class="btn btn-success w-100">Login</button>
</form>

</div>

</body>
</html>
