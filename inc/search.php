<?php
header("HTTP/1.1 200 OK");
// Database connection
require_once 'config.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connessione fallita: " . mysqli_connect_error());
}

// Receiving search parameters
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

// Init pagination

$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;


// Query to search in posts
$sql_posts = "SELECT wh_posts.*, wh_postmeta.meta_value AS meta_box_nome_scientifico
FROM wh_posts
LEFT JOIN wh_postmeta ON (wh_posts.ID = wh_postmeta.post_id AND wh_postmeta.meta_key = 'meta-box-nome-scientifico')
WHERE (SOUNDEX(wh_posts.post_title) = SOUNDEX('{$keywords}') OR wh_posts.post_title LIKE '%{$keywords}%' 
OR SOUNDEX(wh_posts.post_content) = SOUNDEX('{$keywords}') OR wh_posts.post_content LIKE '%{$keywords}%')
AND (wh_posts.post_status = 'publish')
AND (wh_posts.post_type = 'post')
GROUP BY wh_posts.ID
ORDER BY wh_posts.post_title ASC";




// Query to search in tags
$sql_tags = "SELECT wh_terms.term_id, wh_terms.name, wh_terms.slug
FROM wh_terms
INNER JOIN wh_term_taxonomy ON (wh_terms.term_id = wh_term_taxonomy.term_id)
WHERE (SOUNDEX(wh_terms.name) = SOUNDEX('{$keywords}') OR wh_terms.name LIKE '%{$keywords}%')
AND (wh_term_taxonomy.taxonomy = 'post_tag')
ORDER BY wh_terms.name ASC";


// Query to search in glossario (termine) CPT
$sql_glossary_terms = "SELECT wh_posts.*
FROM wh_posts
WHERE (SOUNDEX(wh_posts.post_title) = SOUNDEX('{$keywords}') OR wh_posts.post_title LIKE '%{$keywords}%')
AND (wh_posts.post_status = 'publish')
AND (wh_posts.post_type = 'termine')
ORDER BY wh_posts.post_title ASC";


$result_posts = $conn->query($sql_posts);
$result_tags = $conn->query($sql_tags);
$result_glossary_terms = $conn->query($sql_glossary_terms);

$response = array("posts" => array(), "tags" => array(), "glossary_terms" => array());


// Show post search results
if ($result_posts->num_rows > 0) {
  while ($row = $result_posts->fetch_assoc()) {
      $post_content = strip_tags($row["post_content"]);
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

// Show tag search results
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

// Show termini search results
if ($result_glossary_terms->num_rows > 0) {
  while ($row = $result_glossary_terms->fetch_assoc()) {
      $glossary_term = array(
        "title" => $row["post_title"],
        "featured_image" => get_the_post_thumbnail_url($row["ID"], 'medium'),
        "permalink" => get_permalink($row["ID"])
    );
      array_push($response["glossary_terms"], $glossary_term);
  }
}

$combined_results = array_merge($response["posts"], $response["tags"], $response["glossary_terms"]);
usort($combined_results, function ($a, $b) {
  return strcmp($a["title"], $b["title"]);
});

$total_results = count($combined_results);

$limited_results = array_slice($combined_results, $offset, $limit);


$final_response = array("posts" => array(), "tags" => array(), "glossary_terms" => array(), "total_results" => $total_results);

foreach ($limited_results as $result) {
  if (isset($result["content"])) {
    array_push($final_response["posts"], $result);
  } elseif (isset($result["slug"])) {
    array_push($final_response["tags"], $result);
  } else {
    array_push($final_response["glossary_terms"], $result);
  }
}

echo json_encode($final_response);


$conn->close();

?>

