<?php

define("THEME_DIR", get_template_directory_uri());

/*
 * 
 * Add excerpt to pages
 *
 */

add_post_type_support( 'page', 'excerpt' );

/*
 * 
 * Add support for html 5
 *
 */

add_action(
    'after_setup_theme',
    function() {
        add_theme_support( 'html5', [ 'script', 'style' ] );
    }
);

/*
 * Add info for open graphs [[DISABLED BC MANAGED BY YOAST SEO]]
 * 


 function add_open_graph_and_twitter_meta_tags() {
    if (is_single() || is_page()) {
        global $post;
        $og_title = get_the_title();
        $og_description = get_the_excerpt();
        $og_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium', false);
        $og_image[0] = set_url_scheme($og_image[0], 'https');
        $og_image_width = $og_image[1];
        $og_image_height = $og_image[2];

        $og_url = get_permalink();
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($og_description) . '" />' . "\n";
        echo '<meta property="og:image" content="' . esc_attr($og_image[0]) . '" />' . "\n";
        echo '<meta property="og:image:width" content="' . esc_attr($og_image[1]) . '" />' . "\n";
        echo '<meta property="og:image:height" content="' . esc_attr($og_image[2]) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_attr($og_url) . '" />' . "\n";
        
        // Twitter Card meta tags
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($og_title) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($og_description) . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . esc_attr($og_image[0]) . '" />' . "\n";
    }
}
add_action('wp_head', 'add_open_graph_and_twitter_meta_tags');


 */


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
 * Removes meta tag "generator" and rss feed links and wordpress http headers
 *
 */

remove_action('wp_head', 'wp_generator');

remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

function remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_x_pingback');




/*
 * 
 * Add custom meta box for additional images.
 *
 */

function custom_post_type_metabox() {
    add_meta_box(
        'additional_images',
        'Immagini Aggiuntive',
        'additional_images_callback',
        'post',
        'side'
    );
}
add_action('add_meta_boxes', 'custom_post_type_metabox');

function additional_images_callback($post) {
    $nonce = wp_create_nonce(basename(__FILE__));

    error_log("Nonce created in callback: " . $nonce);
    echo '<input type="hidden" name="additional_images_nonce" id="additional_images_nonce" value="' . esc_attr($nonce) . '" />';

    $additional_images_raw = get_post_meta($post->ID, 'additional_images', true);
    $additional_images = !empty($additional_images_raw) ? json_decode($additional_images_raw, true) : array();

    echo '<input type="hidden" name="additional_images_data" id="additional_images_data" value="' . esc_attr($additional_images_raw) . '" />';
    echo '<a href="#" id="upload_additional_images_button" class="button" onclick="openMediaUploader(event)">Carica Immagini Aggiuntive</a>';



    echo '<div id="additional_images_container">';
    foreach ($additional_images as $attachment_id) {
        // Verifica se l'ID dell'allegato è valido
        if ($attachment_id > 0) {
            $attachment_url = wp_get_attachment_url($attachment_id);
            echo '<div style="display: inline-block; position: relative;">';
            echo '<img src="' . esc_url($attachment_url) . '" data-id="' . intval($attachment_id) . '" style="width: 100px; height: auto; margin: 5px;" />';
            echo '<button type="button" class="remove-image-button" style="position: absolute; top: 0; right: 0;">X</button>';
            echo '</div>';
        }
    }
    echo '</div>';
}

function save_additional_images_meta($post_id) {

    error_log("Received post_id: " . $post_id);
    if (!isset($_POST['additional_images_nonce'])) {
        error_log("additional_images_nonce not set.");
        return $post_id;
    }

    $nonce = $_POST['additional_images_nonce'];
    error_log("Received nonce in save_additional_images_meta: " . $nonce);
    if (!wp_verify_nonce($nonce, basename(__FILE__))) {
        error_log("Nonce verification failed.");
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    if ('post' == $_POST['post_type'] && !current_user_can('edit_post', $post_id))
        return $post_id;

    $additional_images = !empty($_POST['additional_images_data']) ? array_map('intval', explode(',', $_POST['additional_images_data'])) : array();

    update_post_meta($post_id, 'additional_images', json_encode($additional_images));
}




add_action('save_post', 'save_additional_images_meta');


/*
 * 
 * Remove category pagination.
 *
 */


function remove_category_pagination( $query ) {
    if ( $query->is_category() && $query->is_main_query() ) {
        $query->set( 'nopaging', 1 );
    }
}
add_action( 'pre_get_posts', 'remove_category_pagination' );




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
    if (is_single() || is_page_template('single-termine.php')) {
        wp_enqueue_script( 'single-post', get_template_directory_uri() . '/inc/js/single-post.js');
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_single_post_scripts' );


function add_print_css() {
    if (is_single() || is_page_template('single-termine.php')) {
      wp_enqueue_style('print-style', get_template_directory_uri() . '/print.css', array(), '1.0', 'print');
    }
  }
  add_action('wp_enqueue_scripts', 'add_print_css');


function perseowiki_enqueue_home_scripts() {
    if (is_home() || is_front_page()) {
        wp_enqueue_script('home-js', get_template_directory_uri() . '/inc/js/home.js');
    } elseif (is_404()) {
        wp_enqueue_script('home-js', get_template_directory_uri() . '/inc/js/home.js');
    }
}
add_action('wp_enqueue_scripts', 'perseowiki_enqueue_home_scripts');


function enqueue_observations_map_scripts() {
    if ( ! is_single() ) {
        return;
    }

    wp_enqueue_style( 'openlayers-css', 'https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css', array(), null, 'all' );

    wp_enqueue_script( 'polyfill', 'https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL', array(), null, true );
    wp_script_add_data( 'polyfill', 'defer', true );

    wp_enqueue_script( 'openlayers', 'https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js', array(), null, true );
    wp_script_add_data( 'openlayers', 'defer', true );

    wp_enqueue_script( 'observationsMap', get_template_directory_uri() . '/inc/js/observationsMap.js', array( 'polyfill', 'openlayers' ), '1.0.0', true );
    wp_script_add_data( 'observationsMap', 'defer', true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_observations_map_scripts' );



function enqueue_admin_scripts() {
    if (is_admin()) {
        wp_enqueue_script(
            'wikiherbalist-admin-script',
            get_template_directory_uri() . '/inc/js/admin-script.js',
            array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n', 'wp-data' ),
            filemtime( get_template_directory() . '/inc/js/admin-script.js' )
        );
        wp_enqueue_style('custom_admin_css', get_stylesheet_directory_uri() . '/admin.css');
        wp_localize_script('custom-admin-script', 'wikiherbalistAjax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wikiherbalist_save_revisions'),
        ));
    }
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');







/*
 * 
 * Extract config.php API keys
 *
 */ 

function get_aws_credentials() {
    $config_file_path = get_stylesheet_directory() . '/inc/config.php';
    if (file_exists($config_file_path)) {
        require_once($config_file_path);
        $access_key = constant('AWS_ACCESS_KEY') ?: '';
        $secret_key = constant('AWS_SECRET_KEY') ?: '';

        return array(
            'accessKeyId' => $access_key,
            'secretAccessKey' => $secret_key
        );
    } else {
        echo 'Errore: file di configurazione AWS mancante o non valido.';
        return null;
    }
}

/*
 * 
 * Add Amazon SDK and Custom JS and add Amazon API keys to it
 *
 */


function enqueue_aws_sdk_and_custom_scripts() {
    if (is_single() || is_page_template('single-termine.php') || is_tag()) {
      wp_enqueue_script('aws-sdk', 'https://sdk.amazonaws.com/js/aws-sdk-2.962.0.min.js', array(), '2.962.0', false);
      wp_enqueue_script('custom-polly', get_template_directory_uri() . '/inc/js/custom-polly.js', array('aws-sdk'), '1.0.0', true);
      $awsCredentials = get_aws_credentials();
      wp_localize_script('custom-polly', 'awsCredentials', $awsCredentials);
    }
  }
  add_action('wp_enqueue_scripts', 'enqueue_aws_sdk_and_custom_scripts');

/*
 * 
 * Add Image field to categories
 *
 */

function perseowiki_add_category_image_field() {
    ?>
    <div class="form-field">
        <label for="category-image-url"><?php _e( 'Immagine URL', 'perseowiki' ); ?></label>
        <input type="text" name="category-image-url" id="category-image-url" value="">
        <p class="description"><?php _e( 'Inserisci l\'URL dell\'immagine per la categoria.', 'perseowiki' ); ?></p>
    </div>
    <?php
}
add_action( 'category_add_form_fields', 'perseowiki_add_category_image_field', 10, 2 );

function perseowiki_edit_category_image_field( $term ) {
    $image_url = get_term_meta( $term->term_id, 'category-image-url', true );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="category-image-url"><?php _e( 'Immagine URL', 'perseowiki' ); ?></label></th>
        <td>
            <input type="text" name="category-image-url" id="category-image-url" value="<?php echo esc_attr( $image_url ) ? esc_attr( $image_url ) : ''; ?>">
            <p class="description"><?php _e( 'Inserisci l\'URL dell\'immagine per la categoria.', 'perseowiki' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'category_edit_form_fields', 'perseowiki_edit_category_image_field', 10, 2 );

function perseowiki_save_category_image( $term_id ) {
    if ( isset( $_POST['category-image-url'] ) ) {
        update_term_meta( $term_id, 'category-image-url', esc_url_raw( $_POST['category-image-url'] ) );
    }
}
add_action( 'edited_category', 'perseowiki_save_category_image', 10, 2 );
add_action( 'create_category', 'perseowiki_save_category_image', 10, 2 );


/*
 * 
 * Add CPT blog.
 *
 */

 function perseowiki_create_blog_cpt() {
    $labels = array(
        'name' => __('Blog', 'perseowiki'),
        'singular_name' => __('Articolo del blog', 'perseowiki'),
        'add_new' => __('Aggiungi nuovo', 'perseowiki'),
        'add_new_item' => __('Aggiungi nuovo articolo del blog', 'perseowiki'),
        'edit_item' => __('Modifica articolo del blog', 'perseowiki'),
        'new_item' => __('Nuovo articolo del blog', 'perseowiki'),
        'view_item' => __('Visualizza articolo del blog', 'perseowiki'),
        'search_items' => __('Cerca articoli del blog', 'perseowiki'),
        'not_found' => __('Nessun articolo blog trovato', 'perseowiki'),
        'not_found_in_trash' => __('Nessun articolo del blog trovato nel cestino', 'perseowiki'),
        'all_items' => __('Tutti gli articoli del blog', 'perseowiki'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'rewrite' => array('slug' => 'blog'),
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields'),
        'menu_icon' => 'dashicons-welcome-write-blog',
    );

    register_post_type('blog', $args);
}
add_action('init', 'perseowiki_create_blog_cpt');

/*
 * 
 * Add custom category to CPT blog.
 *
 */

 function perseowiki_register_blog_categories() {
    $labels = array(
        'name'              => _x( 'Categorie del blog', 'taxonomy general name', 'perseowiki' ),
        'singular_name'     => _x( 'Categoria del blog', 'taxonomy singular name', 'perseowiki' ),
        'search_items'      => __( 'Cerca categorie del blog', 'perseowiki' ),
        'all_items'         => __( 'Tutte le categorie del blog', 'perseowiki' ),
        'parent_item'       => __( 'Categoria del blog genitore', 'perseowiki' ),
        'parent_item_colon' => __( 'Categoria del blog genitore:', 'perseowiki' ),
        'edit_item'         => __( 'Modifica categoria del blog', 'perseowiki' ),
        'update_item'       => __( 'Aggiorna categoria del blog', 'perseowiki' ),
        'add_new_item'      => __( 'Aggiungi nuova categoria del blog', 'perseowiki' ),
        'new_item_name'     => __( 'Nuova categoria del blog', 'perseowiki' ),
        'menu_name'         => __( 'Categorie del blog', 'perseowiki' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'categorie-blog' ),
    );

    register_taxonomy( 'blog_category', array( 'blog' ), $args );
}

add_action( 'init', 'perseowiki_register_blog_categories' );

  
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
 * Add CPT members
 *
 */

add_action('init', 'create_members_cpt');

function create_members_cpt() {
  $labels = array(
    'name' => _x('Members', 'Post Type General Name', 'textdomain'),
    'singular_name' => _x('Member', 'Post Type Singular Name', 'textdomain'),
    'menu_name' => __('Members', 'textdomain'),
    'all_items' => __('All Members', 'textdomain'),
  );

  $args = array(
    'label' => __('Member', 'textdomain'),
    'description' => __('Members of the scientific committee', 'textdomain'),
    'labels' => $labels,
    'supports' => array('title', 'editor', 'thumbnail'),
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 5,
    'menu_icon' => 'dashicons-groups',
    'show_in_admin_bar' => true,
    'can_export' => true,
    'has_archive' => true,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'capability_type' => 'post',
  );

  register_post_type('members', $args);
}

add_action( 'init', 'create_member_category', 0 );

function create_member_category() {

  $labels = array(
    'name' => _x( 'Member Categories', 'taxonomy general name', 'textdomain' ),
    'singular_name' => _x( 'Member Category', 'taxonomy singular name', 'textdomain' ),
    'search_items' =>  __( 'Search Member Categories', 'textdomain' ),
    'all_items' => __( 'All Member Categories', 'textdomain' ),
    'parent_item' => __( 'Parent Member Category', 'textdomain' ),
    'parent_item_colon' => __( 'Parent Member Category:', 'textdomain' ),
    'edit_item' => __( 'Edit Member Category', 'textdomain' ), 
    'update_item' => __( 'Update Member Category', 'textdomain' ),
    'add_new_item' => __( 'Add New Member Category', 'textdomain' ),
    'new_item_name' => __( 'New Member Category Name', 'textdomain' ),
    'menu_name' => __( 'Member Categories', 'textdomain' ),
  );  

  register_taxonomy('member_category',array('members'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'member-category' ),
  ));
}



add_action('add_meta_boxes', 'add_affiliation_metabox');

function add_affiliation_metabox() {
  add_meta_box(
    'affiliation_metabox', 
    'Affiliation',
    'affiliation_metabox_callback', 
    'members', 
    'normal', 
    'high' 
  );
}

function affiliation_metabox_callback($post) {
  
  wp_nonce_field(basename(__FILE__), 'affiliation_nonce');
  
  
  $affiliation_value = get_post_meta($post->ID, '_affiliation', true);
  
  
  echo '<input type="text" name="affiliation" value="' . esc_attr($affiliation_value) . '" class="widefat">';
}


add_action('save_post', 'save_affiliation_metabox', 10, 2);

function save_affiliation_metabox($post_id, $post) {
  
  if(!isset($_POST['affiliation_nonce']) || !wp_verify_nonce($_POST['affiliation_nonce'], basename(__FILE__)))
    return $post_id;
  
  
  if(!current_user_can('edit_post', $post_id))
    return $post_id;
  
  
  $new_affiliation_value = (isset($_POST['affiliation']) ? sanitize_text_field($_POST['affiliation']) : '');
  update_post_meta($post_id, '_affiliation', $new_affiliation_value);
}


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
 * ADD custom field "Plurale" to Glossario CPT
 *
 */

 function perseowiki_add_plurale_metabox() {
    add_meta_box(
        'perseowiki_plurale_metabox',
        'Plurale',
        'perseowiki_plurale_metabox_callback',
        'termine',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'perseowiki_add_plurale_metabox');


function perseowiki_plurale_metabox_callback($post) {
    wp_nonce_field('perseowiki_plurale_metabox', 'perseowiki_plurale_metabox_nonce');

    $plurale = get_post_meta($post->ID, '_perseowiki_plurale', true);

    echo '<label for="perseowiki_plurale">Plurale:</label>';
    echo '<input type="text" id="perseowiki_plurale" name="perseowiki_plurale" value="' . esc_attr($plurale) . '" size="25" />';
}


function perseowiki_save_plurale_metabox_data($post_id) {
    if (!isset($_POST['perseowiki_plurale_metabox_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['perseowiki_plurale_metabox_nonce'], 'perseowiki_plurale_metabox')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['perseowiki_plurale'])) {
        return;
    }

    $plurale_data = sanitize_text_field($_POST['perseowiki_plurale']);
    update_post_meta($post_id, '_perseowiki_plurale', $plurale_data);
}
add_action('save_post', 'perseowiki_save_plurale_metabox_data');


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
 * Integrate PubMed API to fetch the publication count based on the scientific name
 */

 function fetch_pubmed_publications_count($scientific_name) {
    $api_key = 'bb4a4fd9b7035c08fbaf115a3099b7c07408'; // Replace with your actual API key

    // Estrai solo le prime due parole dal nome scientifico
    $parts = explode(' ', $scientific_name);
    $binomial_name = $parts[0] . ' ' . $parts[1];

    // Construct the URL for PubMed API request
    $url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=" . urlencode($binomial_name) . "&retmode=json&api_key=" . $api_key;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    error_log('URL della richiesta: ' . $url);

    $response = curl_exec($ch);
    if ($response === false) {
        error_log('CURL Error: ' . curl_error($ch)); // Log cURL error
        curl_close($ch);
        return 'Nd'; // Return Nd in case of error
    } else {
        error_log('Risposta API: ' . $response); // Log API response
    }
    curl_close($ch);

    $data = json_decode($response, true);
    error_log('PubMed API Response: ' . print_r($data, true)); // Log the response for debugging

    // Extract and return the publication count from the API response
    $count = isset($data['esearchresult']['count']) ? $data['esearchresult']['count'] : 0;

    return $count;
}







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
 * ADD custom field "Tossica"
 *
 */

// Add the metabox to the desired post type (e.g. 'post')
add_action('add_meta_boxes', 'add_tossica_metabox');

function add_tossica_metabox() {
    add_meta_box('tossica', 'Tossica', 'tossica_metabox_callback', 'post', 'side', 'default');
}

// Callback to display the metabox content
function tossica_metabox_callback($post) {
    wp_nonce_field('tossica_metabox', 'tossica_metabox_nonce');
    $tossica = get_post_meta($post->ID, '_tossica', true);
    echo '<input type="checkbox" id="tossica" name="tossica" value="1"' . checked(1, $tossica, false) . '/>';
}

// Save the custom field value on post save
add_action('save_post', 'save_tossica_metabox_data');

function save_tossica_metabox_data($post_id) {
    // Verify data security and validity
    if (!isset($_POST['tossica_metabox_nonce']) || !wp_verify_nonce($_POST['tossica_metabox_nonce'], 'tossica_metabox')) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the value of the "Tossica" checkbox
    if (isset($_POST['tossica'])) {
        update_post_meta($post_id, '_tossica', 1);
    } else {
        delete_post_meta($post_id, '_tossica');
    }
}



/*
 *
 * ADD custom metabox Reviews
 *
 */

 function wikiherbalist_add_custom_metabox() {
    add_meta_box(
        'wikiherbalist_revision_metabox', 
        'Dettagli Revisione', 
        'wikiherbalist_revision_metabox_callback', 
        ['post', 'termine'], 
        'side', 
        'high'
    );
}
add_action('add_meta_boxes', 'wikiherbalist_add_custom_metabox');

function wikiherbalist_revision_metabox_callback($post) {
    
    wp_nonce_field( basename(__FILE__), 'wikiherbalist_revisions_nonce' );
    $wikiherbalist_stored_meta = get_post_meta($post->ID, '_wikiherbalist_revision_data', true);
    error_log(print_r($wikiherbalist_stored_meta, true));
    $members = get_posts(['post_type' => 'members', 'numberposts' => -1]);
    ?>
    <div>
        <label for="member-list">Membri</label>
        <select name="member" id="member-list">
            <option value="">Seleziona un Membro</option>
            <?php foreach($members as $member) { ?>
                <option value="<?php echo $member->ID; ?>"><?php echo $member->post_title; ?></option>
            <?php } ?>
        </select>
    </div>
    <div>
        <label for="revision-date">Data</label>
        <input type="date" id="revision-date" name="revision-date">
    </div>
    <div>
        <button type="button" id="add-revision">Aggiungi</button>
    </div>
    <div id="revisions-list">
        <h4>Revisioni</h4>
        <?php
        if ($wikiherbalist_stored_meta) {
            foreach ($wikiherbalist_stored_meta as $revision) {
                $member = get_post($revision->memberId);
                $date = date("d-m-Y", strtotime($revision->date));
                echo '<div class="revision-item" data-member-id="' . $member->ID . '" data-date="' . $revision->date . '">';
                echo '<span>' . $member->post_title . ' ' . $date . '</span>';
                echo '<button class="remove-revision">X</button>';
                echo '</div>';
            }
        }
        ?>
    </div>
    <?php
}

function wikiherbalist_save_revision($post_id) {
    error_log('wikiherbalist_save_revision called');
    error_log(print_r($_POST, true));

    if (!isset($_POST['wikiherbalist_revisions_nonce'])) {
        return $post_id;
    }
    $nonce = $_POST['wikiherbalist_revisions_nonce'];
    if (!wp_verify_nonce($nonce, basename(__FILE__))) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    $revision_data = array();
    if (isset($_POST['revisions'])) {
        $revision_data = json_decode(stripslashes($_POST['revisions']));
    }

    update_post_meta($post_id, '_wikiherbalist_revision_data', $revision_data);
}
add_action('save_post', 'wikiherbalist_save_revision');







/*
 * 
 * AJAX get posts by letter [homepage]
 *
 */

 function title_starts_with_char($where, $wp_query) {
    global $wpdb;
    if ($start_char = $wp_query->get('start_char')) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . $start_char . '%\'';
    }
    return $where;
}
add_filter('posts_where', 'title_starts_with_char', 10, 2);


  
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
        $titles_and_links[] = array(
            'title' => get_the_title(),
            'link' => get_permalink(),
            'excerpt' => get_the_excerpt(),
            'plurale' => get_post_meta(get_the_ID(), '_perseowiki_plurale', true),
            'id' => get_the_ID(),
        );
    }
    wp_reset_postdata();
    echo json_encode($titles_and_links);
    wp_die();
}


add_action('wp_ajax_get_all_posts_titles_and_links', 'get_all_posts_titles_and_links');
add_action('wp_ajax_nopriv_get_all_posts_titles_and_links', 'get_all_posts_titles_and_links');


// AJAX TO RETRIEVE PROPERTIES AND HERBS

function get_therapeutic_properties_and_herbs() {
    global $wpdb;

    $query = "
        SELECT t.name AS property, GROUP_CONCAT(p.post_title ORDER BY p.post_title ASC SEPARATOR ', ') AS herbs
        FROM wh_terms t
        INNER JOIN wh_term_taxonomy tt ON t.term_id = tt.term_id
        INNER JOIN wh_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
        INNER JOIN wh_posts p ON tr.object_id = p.ID
        WHERE tt.taxonomy = 'post_tag' AND p.post_status = 'publish'
        GROUP BY t.term_id
        ORDER BY t.name
    ";

    $results = $wpdb->get_results($query);

    return $results;
}



/*
 * 
 * ADD CUSTOM BREADCRUMB TO BE PLACED WITH <?php custom_breadcrumb(); ?>
 *
 */

function custom_breadcrumb() {
    require_once(ABSPATH . WPINC . '/post-template.php');
    global $post;
    echo '<div class="breadcrumbs">';

    if (!is_front_page()) {

        $separator = ' <span> > </span> ';

        echo '<a href="' . home_url('/') . '">Home</a>' . $separator;

        if (get_post_type($post) == 'termine') { 
            echo '<a href="' . get_post_type_archive_link('termine') . '">Glossario</a>';
    
        }
        if (get_post_type($post) == 'blog') { 
            echo '<a href="' . get_post_type_archive_link('blog') . '">Blog</a>';
            echo $separator;
            echo the_title();
    
        }elseif (is_single()) {

            $categories = get_the_category();
            $cat_parents = array();

            // Loop through the post categories and only insert the parent categories into an array
            foreach ($categories as $category) {
                if ($category->parent == 0) {
                    array_push($cat_parents, $category);
                }
            }

            // Sort the parent categories array in descending order
            $cat_parents = array_reverse($cat_parents);

            // Display the deepest parent category with a link to the category
            echo '<a href="' . get_category_link($cat_parents[0]->cat_ID) . '">' . $cat_parents[0]->name . '</a>';

            echo $separator;
            echo the_title();
        } elseif (is_page()) {
            echo the_title();
        } elseif (is_category()) {
            single_cat_title();
        } elseif (is_tag()) {
            single_tag_title();
        } elseif (is_day()) {
            echo "<a href='" . get_year_link(get_the_time('Y')) . "'>" . get_the_time('Y') . "</a>";
            echo $separator;
            echo "<a href='" . get_month_link(get_the_time('Y'), get_the_time('m')) . "'>" . get_the_time('F') . "</a>";
            echo $separator;
            echo get_the_time('d');
        } elseif (is_month()) {
            echo "<a href='" . get_year_link(get_the_time('Y')) . "'>" . get_the_time('Y') . "</a>";
            echo $separator;
            echo get_the_time('F');
        } elseif (is_year()) {
            echo get_the_time('Y');
        } elseif (is_author()) {
            echo "Archives by: " . get_the_author_meta('display_name', $post->post_author);
        } else {
            echo get_the_title();
        }

    }

    echo '</div>';
}

/*
 * 
 * EDIT RIGHT NOW WIDGET IN WORDPRESS DASHBOARD
 *
 */

 function custom_dashboard_widget() {
    add_meta_box('dashboard_right_now', 'In sintesi', 'custom_dashboard_widget_output', 'dashboard', 'normal', 'high');
}

function custom_dashboard_widget_output() {
    $num_posts = wp_count_posts();
    $num_pages = wp_count_posts('page');
    $num_comments = wp_count_comments();
    $num_blog_posts = wp_count_posts('blog');
    $num_glossary_terms = wp_count_posts('termine');
    $num_all_comments = $num_comments->total_comments;
    $output = '<ul class="widget-sintesi">';
    $output .= '<li class="widget-sintesi-articoli"> <a href="edit.php">'.$num_posts->publish.' articoli</a></li>';
    $output .= '<li class="widget-sintesi-pagine"> <a href="edit.php?post_type=page">'.$num_pages->publish.' pagine</a></li>';
    $output .= '<li class="widget-sintesi-blog"> <a href="edit.php?post_type=blog">'.$num_blog_posts->publish.' post del blog</a></li>';
    $output .= '<li class="widget-sintesi-termine"> <a href="edit.php?post_type=termine">'.$num_glossary_terms->publish.' termini del glossario</a></li>';
    $output .= '<li class="widget-sintesi-commenti"> <a href="edit-comments.php">'.$num_all_comments.' commenti</a></li>';
    $output .= '<li>Versione di WordPress: ' . get_bloginfo('version') . '</li>';
    $output .= '<li>Tema attuale: ' . wp_get_theme()->get('Name') . '</li>';
    $output .= '</ul>';
    echo $output;
}

add_action('wp_dashboard_setup', 'custom_dashboard_widget');




/*
 * 
 * ADD STRUCTURED DATA - MARKUP SCHEMA.ORG AND DISABLE YOAST SEO ONE
 *
 */

 function disable_yoast_json_ld_for_single_and_tag($data) {
    if (is_single() || is_tag()) {
        return array();
    }
    return $data;
}
add_filter('wpseo_json_ld_output', 'disable_yoast_json_ld_for_single_and_tag');


 function perseowiki_schema_markup_post() {
    if (is_single()) {

        $schema = 'https://schema.org/';
        $type = 'MedicalWebPage';
        $url = get_permalink();
        $title = get_the_title();
        $description = get_the_excerpt();
        $datePublished = get_the_date('c');
        $dateModified = get_the_modified_date('c');
        $author = "Redazione WikiHerbalist";
        $image = get_the_post_thumbnail_url();

        $sections = array(
            array(
                '@type' => 'WebPageElement',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-1',
                'name' => 'Proprietà',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Nome scientifico',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-2',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Nome comune',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-3',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Parti usate',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-4',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Fitochimica',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-5',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Botanica',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-6',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Raccolta',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-7',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Modalità d\'uso',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-8',
            ),            
            array(
                '@type' => 'WebPageElement',
                'name' => 'Utilizzo tradizionale',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-9',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Ricerca scientifica',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-10',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Avvertenze e Controindicazioni',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-11',
            ),
            array(
                '@type' => 'WebPageElement',
                'name' => 'Riferimenti',
                'isAccessibleForFree' => 'True',
                'cssSelector' => '#section-12',
            ),
            
        );

        echo '<script type="application/ld+json">';
        echo json_encode(array(
            '@context' => $schema,
            '@type' => $type,
            'about'=> array(
                '@type' => 'Substance',
                'name'=> $title,
            ),
            'url' => $url,
            'image' => $image,
            'headline' => $title,
            'description' => $description,
            'datePublished' => $datePublished,
            'dateModified' => $dateModified,
            'publisher' => array(
                '@type' => 'Organization',
                'name' => 'WikiHerbalist',
                'logo' => 'https://www.wikiherbalist.com/wp-content/uploads/2023/03/logo.svg'
            ),
            'author' => array(
                '@type' => 'Person',
                'name' => $author
            ),
            'hasPart' => $sections,
            'inLanguage' => 'it',
            'specialty' => array(
                'Herbal Medicine',
                'Phytotherapy'
            ),
            'potentialAction' => array(
                '@type' => 'ReadAction',
                'target' => $url
            ),
        ));
        echo '</script>';
    }
}

    function perseowiki_schema_markup_tag() {
        if (is_tag()) {

            $schema = 'https://schema.org/';
            $type = ['CollectionPage', 'MedicalWebPage'];
            $url = get_tag_link(get_queried_object()->term_id);
            $title = single_tag_title('', false);
            $description = tag_description(get_queried_object()->term_id);
            $image = get_the_post_thumbnail_url();
    
            echo '<script type="application/ld+json">';
            echo json_encode(array(
                '@context' => $schema,
                '@type' => $type,
                'name' => $title,
                'description' => $description,
                'url' => $url,
                'image' => $image,
                'mainEntity' => array(
                    '@type' => 'MedicalEntity',
                    'name' => $title
                ),
                'inLanguage' => 'it'
            ));
            echo '</script>';
        }
    }
    

add_action('wp_head', 'perseowiki_schema_markup_tag');
add_action('wp_head', 'perseowiki_schema_markup_post');


?>