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
                    // Set the image size based on the post counter
                    $image_size = ( $post_counter <= 3 ) ? 'large' : 'medium';
                    // Set column classes for responsive layout
                    $column_classes = 'col-xl-4 col-lg-6 col-md-6 col-sm-12';

                    // Add a custom class for the first 3 posts
                    if ( $post_counter <= 3 ) {
                        $column_classes .= ' latest-post';
                    }

                    // Retrieve the custom author ID from the post meta
                    $custom_author_id = get_post_meta(get_the_ID(), 'meta-box-author-dropdown', true);
                    // Get the custom author's name, fallback to default if empty
                    $custom_author_name = $custom_author_id ? get_the_title($custom_author_id) : 'Editors of WikiHerbalist';
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
                                        <i class="fa fa-calendar" aria-hidden="true"></i> <?php the_time('j F Y'); ?> | <?php echo $custom_author_name; ?>
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
                <p><?php _e( 'No articles found.', 'perseowiki' ); ?></p>
            <?php endif; ?>
        </div>


    </div>
</main>
<?php
get_footer();
?>