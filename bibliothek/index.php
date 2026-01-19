<?php
//heaqder einbinden
 include 'header.php'; ?>

<div class="container">

    <h1 class="mb-4">Bibliothek Startseite</h1>
    
    <?php 
    //Willkommensnachricht
    if (isset($_SESSION["admin_id"])): ?>
        <h3>Hallo <?= $_SESSION["admin_vname"] . " " . $_SESSION["admin_name"] ?></h3>
    <?php else: ?>
        <h3>Hallo Gast!</h3>
    <?php endif; ?>

    <form method="post" class="row g-3 mb-4">
        <div class="col-md-3">
            <select class="form-select" name="filter">
                <option value="titel">Titel</option>
                <option value="autor">Autor</option>
                <option value="verlag">Verlag</option>
            </select>
        </div>

        <div class="col-md-6">
            <input type="text" name="suche" class="form-control" placeholder="Suchbegriff" required>
        </div>

        <div class="col-md-3">
            <button class="btn btn-success w-100">Suchen</button>
        </div>
    </form>

    <?php
    //Datenbankverbindung herstellen
    $conn = new mysqli("localhost", "root", "", "bibliothek");
    $conn->set_charset("utf8");

    if ($conn->connect_error) die("DB Fehler: " . $conn->connect_error);
    //Suchfunktion
    if(isset($_POST['suche'])) {
        $suche = $conn->real_escape_string($_POST['suche']);
        $filter = $_POST['filter'];

        echo "<h2>Suchergebnisse:</h2>";
        //SQL Abfrage mit Suchfilter
        $sql = "SELECT * FROM buch WHERE $filter LIKE '%$suche%'";
    } else {
        //falls keine Suche durchgeführt wurde
        echo "<h2>Alle Bücher:</h2>";
        $sql = "SELECT * FROM buch ORDER BY titel ASC";
    }

    $result = $conn->query($sql);
    ?>
   
    <?php
    //Bücher aus der Datenbank anzeigen
    if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ISBN</th>
                        <th>Titel</th>
                        <th>Autor</th>
                        <th>Verlag</th>
                        <th>Preis (€)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['isbn'] ?></td>
                        <td><?= $row['titel'] ?></td>
                        <td><?= $row['autor'] ?></td>
                        <td><?= $row['verlag'] ?></td>
                        <td><?= $row['preis'] ?></td>
                        <td><?= $row['status'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Keine Bücher gefunden.</p>
    <?php endif; ?>

</div>

</body>
</html>
