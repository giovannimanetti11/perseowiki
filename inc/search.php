<?php
// Connessione al database
$servername = "localhost";
$username = "wikiherbalist";
$password = "_CXbGnX8Jd43eFr!";
$dbname = "wikiherbalist";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connessione fallita: " . mysqli_connect_error());
}

// Ricezione dei parametri di ricerca
$keywords = $_GET["keywords"];

if (empty($keywords)) {
    die("Parametro di ricerca non specificato.");
  }

// Query per la ricerca
$sql = "SELECT * FROM wp_posts WHERE post_title LIKE '%$keywords%' OR post_content LIKE '%$keywords%'";


$result = mysqli_query($conn, $sql);

// Mostrare i risultati della ricerca
if (mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_assoc($result)) {
    echo "<h2>" . $row["title"] . "</h2>";
    echo "<p>" . $row["content"] . "</p>";
  }
} else {
  echo "Nessun risultato trovato.";
}

mysqli_close($conn);
?>
