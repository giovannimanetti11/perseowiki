<?php
/**
 * Template Name: Archivio Blog
 * Template per la visualizzazione dell'archivio del Custom Post Type "Blog" nel tema PerseoWiki
 *
 * @package perseowiki
 */

get_header();
?>
<main id="primary" class="site-main">
    <div class="container">
        <h1 class="page-title">Blog</h1>
        <div class="row">
            <?php if ( have_posts() ) : ?>
                <?php $post_counter = 1;
                while ( have_posts() ) : the_post(); ?>
                    <?php
                    // Imposta la dimensione dell'immagine in base al contatore di post
                    $image_size = ( $post_counter <= 3 ) ? 'large' : 'medium';
                    // Imposta le classi delle colonne per il layout responsivo
                    $column_classes = 'col-xl-4 col-lg-6 col-md-6 col-sm-12';

                    // Aggiungi una classe personalizzata per i primi 3 post
                    if ( $post_counter <= 3 ) {
                        $column_classes .= ' featured-post';
                    }
                    ?>
                    <div class="<?php echo $column_classes; ?> blog-card">
                        <a href="<?php the_permalink(); ?>" class="post-link">
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <div class="post-thumbnail">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( $image_size ); ?>
                                    <?php else : ?>
                                        <img src="<?php echo get_template_directory_uri() . '/assets/images/default-post-thumbnail.jpg'; ?>" alt="<?php the_title(); ?>" />
                                    <?php endif; ?>
                                </div>

                                <div class="entry-header">
                                    <h2 class="entry-title">
                                        <?php the_title(); ?>
                                    </h2>
                                    <div class="entry-date">
                                        <?php the_time('j F Y'); ?>
                                    </div>
                                </div>

                                <div class="entry-summary">
                                    <?php the_excerpt(); ?>
                                </div>
                            </article>
                        </a>
                    </div>
                    <?php
                    $post_counter++;
                endwhile;
                ?>

            <?php else : ?>
                <p><?php _e( 'Nessun articolo trovato.', 'perseowiki' ); ?></p>
            <?php endif; ?>
        </div>


    </div>
</main>
<?php
get_footer();
?>
