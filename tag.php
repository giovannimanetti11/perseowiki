<?php
/**
 * Template Name: Tag Archive
 * Description: Custom template for displaying tag archives
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <?php if ( have_posts() ) : ?>

      <div class="tag-header">
          <h1>Proprietà <?php single_tag_title(); ?></h1>
          <div class="container">
            <p><?php echo strip_tags(tag_description()); ?></p>

            <span>Di seguito troverai l'elenco delle erbe presenti su WikiHerbalist che hanno proprietà <?php single_tag_title(); ?></span>
          </div>
          
      </div>

      <div class="tag-posts">

      <?php
        $tag = get_queried_object(); 
        $args = array(
            'tag_id' => $tag->term_id, 
            'post_type' => 'post', 
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $posts = get_posts($args);

        $output = '';
        $output .= '<div class="cards-container">';
        foreach ($posts as $post) : setup_postdata($post);
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

        endforeach;
        $output .= '</div>';

        wp_reset_postdata(); // Ripristina i dati del post originale

        echo $output;
    ?> 


      </div><!-- .tag-posts -->

    <?php else :

      get_template_part( 'content', 'none' ); // Include the "no posts found" template

    endif; ?>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
