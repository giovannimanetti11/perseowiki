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
        <div class="row">
            <div class="col-md-9">
                <?php if ( have_posts() ) : ?>
                    <?php
                    $blog_categories = get_terms( array(
                        'taxonomy' => 'blog_category',
                        'hide_empty' => false,
                    ) );
                    
                    foreach ( $blog_categories as $category ) :
                        $category_image = get_term_meta( $category->term_id, 'category-image-url', true );
                        ?>
                        <div class="category-wrapper">
                            <img src="<?php echo $category_image; ?>" alt="<?php echo $category->name; ?>">
                            <h3 class="category-title"><?php echo $category->name; ?></h3>
                        </div>
                    <?php endforeach; ?>
                    


                    <ul id="post_filter" data-layout="blog_newspaper">
                        <li class="filter-item">
                            <a data-view="latest" title="Latest" href="javascript:;" class="filter_active">
                            <i class="fa-solid fa-clock"></i>Latest
                            </a>
                        </li>
                        <li class="filter-item">
                            <a data-view="trending" title="Trending" href="javascript:;">
                                <i class="fa fa-bolt" aria-hidden="true"></i>Trending
                            </a>
                        </li>
                        <li class="filter-item">
                            <a data-view="hot" title="Hot" href="javascript:;">
                                <i class="fa fa-fire"></i>Hot
                            </a>
                        </li>
                        <li class="filter-item">
                            <a data-view="editors_picks" title="Editor Picks" href="javascript:;">
                                <i class="fa fa-heart"></i>Editor Picks
                            </a>
                        </li>
                    </ul>


                    <div class="row">
                        <?php
                        $post_counter = 1;
                        while ( have_posts() ) : the_post(); ?>
                            <?php
                            // Imposta la dimensione dell'immagine in base al contatore di post
                            $image_size = ( $post_counter <= 3 ) ? 'large' : 'medium';

                            // Imposta le classi delle colonne per il layout responsivo
                            $column_classes = 'col-lg-4 col-md-6 col-sm-12';

                            // Aggiungi una classe personalizzata per i primi 3 post
                            if ( $post_counter <= 3 ) {
                                $column_classes .= ' featured-post';
                            }
                            ?>
                            <div class="<?php echo $column_classes; ?>">
                                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( $image_size ); ?>
                                        </a>
                                    <?php endif; ?>

                                    <div class="entry-header">
                                        <h2 class="entry-title">
                                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>
                                        <div class="entry-date">
                                            <?php the_date(); ?>
                                        </div>

                                    </div>

                                    <div class="entry-summary">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </article>
                            </div>
                            <?php
                            $post_counter++;
                        endwhile;
                        ?>
                    </div>

                <?php else : ?>
                    <p><?php _e( 'Nessun articolo trovato.', 'perseowiki' ); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <!-- get_sidebar(); ?> -->
            </div>
        </div>
    </div>
</main>


<?php
get_footer();
?>

