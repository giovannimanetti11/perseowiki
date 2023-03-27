<?php

define("THEME_DIR", get_template_directory_uri());

/*
 * Add info for open graphs
 * 
 */

function add_open_graph_meta_tags() {
    if (is_single() || is_page()) {
        global $post;
        $og_title = get_the_title();
        $og_description = get_the_excerpt();
        $og_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        $og_url = get_permalink();
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($og_description) . '" />' . "\n";
        echo '<meta property="og:image" content="' . esc_attr($og_image[0]) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_attr($og_url) . '" />' . "\n";
    }
}
add_action('wp_head', 'add_open_graph_meta_tags');



/*
 * Add support for core custom logo and add featured images to post and pages.
 *
 * @link https://codex.wordpress.org/Theme_Logo
 * @link https://codex.wordpress.org/Post_Thumbnails
 */

function perseowiki_support() {

  add_theme_support( 
    'custom-logo', [
    'width'       => 180,
    'height'      => 50,
    'flex-width'  => true,
    'flex-height' => true,
  ]);


  add_theme_support( 'post-thumbnails' );

}

add_action( 'after_setup_theme', 'perseowiki_support' );

/*
 * 
 * Add custom menu.
 *
 */

function perseowiki_nav_menus() {

  register_nav_menus([
    'perseowiki-primary-menu' => esc_html__( 'Primary Menu', 'perseowiki' ),
    'perseowiki-category-menu' => esc_html__( 'Category Menu', 'perseowiki' ),
  ]);
}

add_action( 'init', 'perseowiki_nav_menus' );


/*
 * 
 * Add custom styles and scripts.
 *
 */

function perseowiki_styles() {

  wp_enqueue_style('perseowiki-style', get_template_directory_uri().'/style.css');
  wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' );
  wp_enqueue_style( 'font-awesome-free', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css' );
  wp_enqueue_script( 'custom-functions', get_template_directory_uri().'/inc/js/functions.js' );

  wp_localize_script( 'custom-functions', 'myAjax', array( 'ajax_url' => admin_url('admin-ajax.php') ) );
}

add_action('wp_enqueue_scripts', 'perseowiki_styles');

function enqueue_single_post_scripts() {
    if ( is_singular( 'post' ) ) {
        wp_enqueue_script( 'single-post', get_template_directory_uri() . '/inc/js/single-post.js');
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_single_post_scripts' );


function add_print_css() {
    if (is_single()) {
      wp_enqueue_style('print-style', get_template_directory_uri() . '/print.css', array(), '1.0', 'print');
    }
  }
  add_action('wp_enqueue_scripts', 'add_print_css');
  
/*
 * 
 * Add CPT glossario.
 *
 */

  function perseowiki_custom_post_type() {

    $labels = array(
        'name' => 'Glossario',
        'singular_name' => 'Termine',
        'menu_name' => 'Glossario',
        'add_new_item' => 'Aggiungi nuovo termine',
        'edit_item' => 'Modifica termine',
        'view_item' => 'Vedi termine',
        'all_items' => 'Tutti i termini',
        'search_items' => 'Cerca termine',
        'not_found' => 'Nessun termine trovato',
        'not_found_in_trash' => 'Nessun termine trovato nel cestino'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-book',
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats' ),
        'rewrite' => array( 'slug' => 'glossario' )
    );

    register_post_type( 'termine', $args );
}
add_action( 'init', 'perseowiki_custom_post_type' );

/*
 * 
 * Add custom category to CPT glossario.
 *
 */

function aggiungi_taxonomy() {

    $labels = array(
        'name' => 'Categorie',
        'singular_name' => 'Categoria',
        'search_items' => 'Cerca categoria',
        'all_items' => 'Tutte le categorie',
        'parent_item' => 'Categoria padre',
        'parent_item_colon' => 'Categoria padre:',
        'edit_item' => 'Modifica categoria',
        'update_item' => 'Aggiorna categoria',
        'add_new_item' => 'Aggiungi nuova categoria',
        'new_item_name' => 'Nome nuova categoria',
        'menu_name' => 'Categorie'
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'rewrite' => array( 'slug' => 'categorie' )
    );

    register_taxonomy( 'categoria', 'termine', $args );
}
add_action( 'init', 'aggiungi_taxonomy' );

/*
 * 
 * Add svg support .
 *
 */

function add_svg_to_upload_mimes( $upload_mimes ) {
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes', 10, 1 );

/*
 * 
 * remove wp version number from scripts and styles
 *
 */

function remove_css_js_version( $src ) {
  if( strpos( $src, '?ver=' ) )
      $src = remove_query_arg( 'ver', $src );
  return $src;
}
add_filter( 'style_loader_src', 'remove_css_js_version', 9999 );
add_filter( 'script_loader_src', 'remove_css_js_version', 9999 );



/*
 * 
 * ADD custom field "Nome scientifico"
 *
 */

function custom_meta_box_markup($post)
{
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

    ?>
        <input name="meta-box-nome-scientifico" type="text" value="<?php echo get_post_meta($post->ID, "meta-box-nome-scientifico", true); ?>">
    <?php  
}

function add_custom_meta_box()
{
    add_meta_box("custom-meta-box", "Nome scientifico", "custom_meta_box_markup", "post", "side", "high", null);
}

add_action("add_meta_boxes", "add_custom_meta_box");

function save_custom_meta_box($post_id, $post, $update)
{
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "post";
    if($slug != $post->post_type)
        return $post_id;

    $meta_box_nome_scientifico_value = "";
    if(isset($_POST["meta-box-nome-scientifico"]))
    {
        $meta_box_nome_scientifico_value = $_POST["meta-box-nome-scientifico"];
    }   
    update_post_meta($post_id, "meta-box-nome-scientifico", $meta_box_nome_scientifico_value);
}

add_action("save_post", "save_custom_meta_box", 10, 3);


/*
 * ADD custom field "Nome comune"
 *
 */

 function custom_meta_box_markup_comune($post)
 {
     wp_nonce_field(basename(__FILE__), "meta-box-nonce");
 
     ?>
         <input name="meta-box-nome-comune" type="text" value="<?php echo get_post_meta($post->ID, "meta-box-nome-comune", true); ?>">
     <?php  
 }
 
 function add_custom_meta_box_comune()
 {
     add_meta_box("custom-meta-box-comune", "Nome comune", "custom_meta_box_markup_comune", "post", "side", "high", null);
 }
 
 add_action("add_meta_boxes", "add_custom_meta_box_comune");
 
 function save_custom_meta_box_comune($post_id, $post, $update)
 {
     if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
         return $post_id;
 
     if(!current_user_can("edit_post", $post_id))
         return $post_id;
 
     if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
         return $post_id;
 
     $slug = "post";
     if($slug != $post->post_type)
         return $post_id;
 
     $meta_box_nome_comune_value = "";
     if(isset($_POST["meta-box-nome-comune"]))
     {
         $meta_box_nome_comune_value = $_POST["meta-box-nome-comune"];
     }   
     update_post_meta($post_id, "meta-box-nome-comune", $meta_box_nome_comune_value);
 }
 
 add_action("save_post", "save_custom_meta_box_comune", 10, 3);
 
 
/*
 * ADD custom field "Parti usate"
 *
 */
 
 function custom_meta_box_markup_parti($post)
 {
     wp_nonce_field(basename(__FILE__), "meta-box-nonce");
 
     ?>
         <input name="meta-box-parti-usate" type="text" value="<?php echo get_post_meta($post->ID, "meta-box-parti-usate", true); ?>">
     <?php  
 }
 
 function add_custom_meta_box_parti()
 {
     add_meta_box("custom-meta-box-parti", "Parti usate", "custom_meta_box_markup_parti", "post", "side", "high", null);
 }
 
 add_action("add_meta_boxes", "add_custom_meta_box_parti");
 
 function save_custom_meta_box_parti($post_id, $post, $update)
 {
     if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
         return $post_id;
 
     if(!current_user_can("edit_post", $post_id))
         return $post_id;
 
     if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
         return $post_id;
 
     $slug = "post";
     if($slug != $post->post_type)
         return $post_id;
 
     $meta_box_parti_usate_value = "";
     if(isset($_POST["meta-box-parti-usate"]))
     {
         $meta_box_parti_usate_value = $_POST["meta-box-parti-usate"];
     }   
     update_post_meta($post_id, "meta-box-parti-usate", $meta_box_parti_usate_value);
 }
 
 add_action("save_post", "save_custom_meta_box_parti", 10, 3);


/*
 * ADD custom field "Costituenti"
 *
 */

function custom_meta_box_markup_costituenti($post)
{
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

    ?>
        <input name="meta-box-costituenti" type="text" value="<?php echo get_post_meta($post->ID, "meta-box-costituenti", true); ?>">
    <?php  
}

function add_custom_meta_box_costituenti()
{
    add_meta_box("custom-meta-box-costituenti", "Costituenti", "custom_meta_box_markup_costituenti", "post", "side", "high", null);
}

add_action("add_meta_boxes", "add_custom_meta_box_costituenti");

function save_custom_meta_box_costituenti($post_id, $post, $update)
{
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "post";
    if($slug != $post->post_type)
        return $post_id;

    $meta_box_costituenti_value = "";
    if(isset($_POST["meta-box-costituenti"]))
    {
        $meta_box_costituenti_value = $_POST["meta-box-costituenti"];
    }   
    update_post_meta($post_id, "meta-box-costituenti", $meta_box_costituenti_value);
}

add_action("save_post", "save_custom_meta_box_costituenti", 10, 3);




/*
 * 
 * AJAX get posts by letter [homepage]
 *
 */


     function get_posts_by_letter() {
      $letter = $_GET['letter'];
      add_filter( 'posts_where', 'search_by_letter', 10, 2 );
      $args = array(
          'post_type' => 'post',
          'posts_per_page' => -1,
          'orderby' => 'title',
          'order' => 'ASC',
      );
      $posts = new WP_Query($args);
      remove_filter( 'posts_where', 'search_by_letter', 10, 2 );
      $output = '';
      $count = 0; // counter for posts
      $counter = 0; // counter for every 3 posts
      while ($posts->have_posts()) {
          $posts->the_post();
          $title = get_the_title();
          $link = get_permalink();
          $image = get_the_post_thumbnail_url(null, 'medium');
          $alt = get_the_title();
          $nome_scientifico = get_post_meta(get_the_ID(), 'meta-box-nome-scientifico', true); 
  
          // Create a new card deck for every 3 posts
          if ($counter % 3 == 0) {
              $output .= '<div class="card-deck">';
          }
  
          $count++;
  
          // Add a new card
          $output .= '<div class="card">';
          $output .= '<img class="card-img-top" src="' . $image . '" alt="' . $alt . '">';
          $output .= '<div class="card-body">';
          $output .= '<h4 class="card-title">' . $title . '</h4>';
          $output .= '<p class="card-scientific-name">' . $nome_scientifico . '</p>'; 
          $output .= '<a href="' . $link . '" class="btn btn-card">Apri Scheda</a>';
          $output .= '</div></div>';
  
          // Close the card deck for every 3 posts
          if ($counter % 3 == 2 || $counter == $posts->post_count - 1) {
              $output .= '</div>';
          }
  
          $counter++;
      }
  
  
      wp_reset_postdata();
      echo json_encode(array(
        'data' => $output,
        'count' => $count,
      ));
      wp_die();
  }
    
  function search_by_letter($where, &$query) {
      global $wpdb;
      $letter = $_GET['letter'];
      $where .= " AND $wpdb->posts.post_title LIKE '$letter%'";
      return $where;
  }
    
  add_action('wp_ajax_get_posts_by_letter', 'get_posts_by_letter');
  add_action('wp_ajax_nopriv_get_posts_by_letter', 'get_posts_by_letter');

  
/*
 * 
 * AJAX TO GET ALL POST WITH THEIR TITLES AND PERMALINKS
 *
 */
function get_all_posts_titles_and_links() {
    $args = array(
        'post_type' => array('post', 'page', 'termine'),
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $posts = new WP_Query($args);
    $titles_and_links = array();
    while ($posts->have_posts()) {
        $posts->the_post();
        if (!is_singular()) {
            $titles_and_links[] = array(
                'title' => get_the_title(),
                'link' => get_permalink(),
            );
        }
    }
    wp_reset_postdata();
    echo json_encode($titles_and_links);
    wp_die();
}

add_action('wp_ajax_get_all_posts_titles_and_links', 'get_all_posts_titles_and_links');
add_action('wp_ajax_nopriv_get_all_posts_titles_and_links', 'get_all_posts_titles_and_links');







?>