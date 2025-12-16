<?php
//navbar eingebunden
include 'header.php';
?>

	<h1>Bibliothek Startseite</h1>
	<form method="post" action="">
		<label for="suche">Buchtitel oder Autor:</label>
		<input type="text" id="suche" name="suche" required>
		<button type="submit">Suchen</button>
	</form>

	<?php
	//Datenbankverbindung herstellen
	$conn = new mysqli("localhost", "root", "", "bibliothek");
    //Datenbankverbindung in UTF8 öffnen, für korrekte Darstellung
	$conn->set_charset("utf8");

    //prüft auf erfolgreiche Datenbank-Verbindung
	if ($conn->connect_error) {
		die("Verbindung fehlgeschlagen: " . $conn->connect_error);
	}

	//Prüft ob eine Suche durchgeführt werden soll
	if (isset($_POST['suche']) && $_POST['suche'] !== "") {
        //wird verwendet um SQL-Injection zu verweigern
		$suche = $conn->real_escape_string($_POST['suche']);
        //SQL ABfrage an die Tabelle Buch
		$sql = "SELECT isbn, titel, autor, verlag, preis, status 
				FROM Buch 
				WHERE titel LIKE '%$suche%' 
				   OR autor LIKE '%$suche%'";

		echo "<h2>Suchergebnisse:</h2>";

	} else {

		//alle Bücher anzeigen, wenn keine Suche durchgeführt wurde
		$sql = "SELECT isbn, titel, autor, verlag, preis, status 
				FROM Buch 
				ORDER BY titel ASC";

		echo "<h2>Alle Bücher:</h2>";
	}
    //führt die Abfrage aus
	$result = $conn->query($sql);
    //prüft ob es Ergebnisse gibt
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
        //gibt die Daten in einer Tabelle aus
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
