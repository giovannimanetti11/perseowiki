<?php
header("HTTP/1.1 200 OK");
// Connessione al database
require_once 'config.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connessione fallita: " . mysqli_connect_error());
}

// Ricezione dei parametri di ricerca
$keywords = $_GET["keywords"];

if (empty($keywords)) {
    die("Parametro di ricerca non specificato.");
}

function sanitizeKeywords($keywords) {
  return preg_replace('/[^A-Za-z0-9À-ú\s]/u', '', $keywords);
}

$keywords = sanitizeKeywords($keywords);

// Escape the keywords to prevent SQL injection
$keywords = mysqli_real_escape_string($conn, $keywords);

// Query per la ricerca dei post
$sql_posts = "SELECT wh_posts.*, wh_postmeta.meta_value AS meta_box_nome_scientifico
FROM wh_posts
LEFT JOIN wh_postmeta ON (wh_posts.ID = wh_postmeta.post_id AND wh_postmeta.meta_key = 'meta-box-nome-scientifico')
WHERE ((wh_posts.post_title LIKE '%{$keywords}%') OR (wh_posts.post_content LIKE '%{$keywords}%') OR (wh_postmeta.meta_value LIKE '%{$keywords}%'))
    AND (wh_posts.post_status = 'publish')
    AND (wh_posts.post_type = 'post')
GROUP BY wh_posts.ID
ORDER BY (wh_posts.post_title LIKE '%{$keywords}%') DESC, wh_posts.post_title ASC
LIMIT 10";

// Query per la ricerca dei tag
$sql_tags = "SELECT wh_terms.term_id, wh_terms.name, wh_terms.slug
FROM wh_terms
INNER JOIN wh_term_taxonomy ON (wh_terms.term_id = wh_term_taxonomy.term_id)
WHERE (wh_terms.name LIKE '%{$keywords}%')
    AND (wh_term_taxonomy.taxonomy = 'post_tag')
ORDER BY wh_terms.name ASC
LIMIT 10";

$result_posts = $conn->query($sql_posts);
$result_tags = $conn->query($sql_tags);

$response = array("posts" => array(), "tags" => array());

// Mostrare i risultati della ricerca dei post
if ($result_posts->num_rows > 0) {
  while ($row = $result_posts->fetch_assoc()) {
      $post_content = strip_tags($row["post_content"]); // rimuove i tag HTML dal contenuto del post
      $post = array(
        "title" => $row["post_title"],
        "content" => $post_content,
        "meta_box_nome_scientifico" => $row["meta_box_nome_scientifico"], 
        "featured_image" => get_the_post_thumbnail_url($row["ID"], 'medium'),
        "permalink" => get_permalink($row["ID"]) 
    );
      array_push($response["posts"], $post);
  }
}

// Mostrare i risultati della ricerca dei tag
if ($result_tags->num_rows > 0) {
  while ($row = $result_tags->fetch_assoc()) {
      $tag = array(
        "name" => $row["name"],
        "slug" => $row["slug"],
        "permalink" => get_term_link(intval($row["term_id"]), 'post_tag')
    );
      array_push($response["tags"], $tag);
  }
}

echo json_encode($response);

$conn->close();

?>

