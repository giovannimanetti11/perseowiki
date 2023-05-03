<?php
/**
 * Template Name: Categoria
 * Template Post Type: category
 *
 * @package WordPress
 * @subpackage PerseoWiki
 * @since 1.0.0
 */

get_header();
?>

<section class="category">
  <div class="container">
    <div class="category-header">
      <h1 class="category-title"><?php single_cat_title(); ?></h1>
      <?php $category_description = category_description();
        if ( ! empty( $category_description ) ) : ?>
        <p class="category-excerpt"><?php echo $category_description; ?></p>
        <?php endif; ?>

    </div>

    <?php
    $category = get_queried_object();
    if ( $category && ! is_wp_error( $category ) ) {
        $cat_id = $category->term_id;
    } else {
        $cat_id = 0;
    }
    
    $args = array(
        'post_type' => 'post',
        'orderby'   => 'title',
        'order'     => 'ASC',
        'cat'       => $cat_id,
        'posts_per_page' => -1
    );
    
    $query = new WP_Query( $args );

    if ( $query->have_posts() ) :
        $output = '<div class="cards-container">'; 
        
        while ( $query->have_posts() ) :
            $query->the_post();
            
            // Get post data
            $image = get_the_post_thumbnail_url( $post->ID, 'large' );
            $alt = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
            $title = get_the_title();
            $nome_scientifico = get_post_meta( get_the_ID(), 'meta-box-nome-scientifico', true );
            $link = get_permalink();
            $tossica = get_post_meta(get_the_ID(), '_tossica', true);

            // Add a new card
            $output .= '<div class="card-wrapper">';
            $output .= '<div class="card">';
            $output .= '<a href="' . $link . '" class="card-link"></a>'; 
            $output .= '<img class="card-img-top" src="' . $image . '" alt="' . $alt . '">';
            $output .= '<div class="card-body">';
            $output .= '<h3 class="card-title">' . $title . '</h3>';
            $output .= '<h4 class="card-scientific-name">' . $nome_scientifico . '</h4>';
            // $output .= '<a href="' . $link . '" class="btn btn-card">Apri Scheda</a>';
            if (!empty($tossica)) {
                $output .= '<i class="fa-solid fa-skull-crossbones" id="icon-skull" title="Pianta tossica"></i>';
            }
            $output .= '</div></div></div>';


        endwhile;

        $output .= '</div>'; // Chiudi il contenitore flex

        // Output the cards
        echo $output;

    else :
        echo '<p>Non ci sono articoli in questa categoria.</p>';
    endif;
    ?>


  </div>
</section>

<?php
get_footer();
?>
