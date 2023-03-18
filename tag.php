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
          <h1>Proprietà: <?php single_tag_title(); ?></h1>
          <div class="container">
            <p><?php echo tag_description(); ?></p>
            <span>Di seguito troverai le erbe di WikiHerbalist che hanno proprietà <?php single_tag_title(); ?></span>
          </div>
          
      </div>

      <div class="tag-posts">

      <?php
        $tag = get_queried_object(); // Ottiene l'oggetto del tag corrente
        $args = array(
            'tag_id' => $tag->term_id, // Usa l'ID del tag corrente per filtrare i post
            'post_type' => 'post', // Tipo di post da cercare
            'posts_per_page' => -1, // Numero di post da visualizzare (-1 per mostrare tutti)
        );

        $posts = get_posts($args); // Esegui la query

        $output = '';
        $count = 0;

        foreach ($posts as $post) : setup_postdata($post);
            $title = get_the_title();
            $image = get_the_post_thumbnail_url($post->ID, 'large');
            $alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
            $nome_scientifico = get_post_meta(get_the_ID(), 'meta-box-nome-scientifico', true); 
            $link = get_permalink();

            // Create a new card deck for every 3 posts
            if ($count % 3 == 0) {
                $output .= '<div class="card-deck">';
            }

            // Add a new card
            $output .= '<div class="card">';
            $output .= '<img class="card-img-top" src="' . $image . '" alt="' . $alt . '">';
            $output .= '<div class="card-body">';
            $output .= '<h4 class="card-title">' . $title . '</h4>';
            $output .= '<p class="card-scientific-name">' . $nome_scientifico . '</p>'; 
            $output .= '<a href="' . $link . '" class="btn btn-card">Apri Scheda</a>';
            $output .= '</div></div>';

            // Close the card deck for every 3 posts
            if ($count % 3 == 2 || $count == count($posts) - 1) {
                $output .= '</div>';
            }

            $count++;
        endforeach;

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
