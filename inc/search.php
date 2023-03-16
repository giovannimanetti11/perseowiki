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
$sql = "SELECT wh_posts.*, wh_postmeta.meta_value AS meta_box_nome_scientifico
FROM wh_posts
LEFT JOIN wh_postmeta ON (wh_posts.ID = wh_postmeta.post_id AND wh_postmeta.meta_key = 'meta-box-nome-scientifico')
LEFT JOIN (
    SELECT object_id
    FROM wh_term_relationships
    INNER JOIN wh_term_taxonomy ON (wh_term_relationships.term_taxonomy_id = wh_term_taxonomy.term_taxonomy_id)
    WHERE wh_term_taxonomy.taxonomy = 'post_tag'
) AS tags ON (wh_posts.ID = tags.object_id)
WHERE ((wh_posts.post_title LIKE '%$keywords%') OR (wh_posts.post_content LIKE '%$keywords%') OR (wh_postmeta.meta_value LIKE '%$keywords%'))
    AND (wh_posts.post_status = 'publish')
    AND (wh_posts.post_type = 'post')
    AND (tags.object_id IS NOT NULL OR tags.object_id IS NULL)
GROUP BY wh_posts.ID
ORDER BY wh_posts.post_title ASC
LIMIT 10";



$result = $conn->query($sql);

// Mostrare i risultati della ricerca
if ($result->num_rows > 0) {
  $response = array();
  while ($row = $result->fetch_assoc()) {
      $post_content = strip_tags($row["post_content"]); // rimuove i tag HTML dal contenuto del post
      $post = array(
        "title" => $row["post_title"],
        "content" => $post_content,
        "meta_box_nome_scientifico" => $row["meta_box_nome_scientifico"], 
        "featured_image" => get_the_post_thumbnail_url($row["ID"], 'medium'),
        "permalink" => get_permalink($row["ID"]) 
    );
      array_push($response, $post);
  }

  echo json_encode($response);
} else {
  echo json_encode(array("message" => "Nessun risultato trovato."));
}




$conn->close();

?>