<?php
header("HTTP/1.1 200 OK");
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
$sql = "SELECT * FROM wh_posts WHERE ( post_title LIKE '%$keywords%' OR post_content LIKE '%$keywords%' ) AND ( post_type = 'post' OR post_type = 'page' ) ORDER BY `post_title` ASC LIMIT 10";


$result = $conn->query($sql);



// Mostrare i risultati della ricerca
if ( $result->num_rows > 0 ) {
  $response = array();
  while($row = $result->fetch_assoc() ) {
  $post_content = strip_tags($row["post_content"]); // rimuove i tag HTML dal contenuto del post
  $post = array(
  "title" => $row["post_title"],
  "content" => $post_content,
  "featured_image" => get_the_post_thumbnail_url($row["ID"], 'medium'),
  "permalink" => get_permalink($row["ID"]) // aggiungi il permalink del post
  );
  array_push($response, $post);
  }
  echo json_encode($response);
  } else {
  echo json_encode(array("message" => "Nessun risultato trovato."));
  }

$conn->close();

?>
