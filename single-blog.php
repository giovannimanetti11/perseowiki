<?php
/**
 * Template Name: Singolo Post del Blog
 * Template per la visualizzazione di un singolo post del Custom Post Type "Blog" nel tema PerseoWiki
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
                    $categories = get_categories();
                    foreach ( $categories as $category ) :
                        $category_image = get_term_meta( $category->term_id, 'category-image-url', true );
                        ?>
                        <div class="category-wrapper">
                            <img src="<?php echo $category_image; ?>" alt="<?php echo $category->name; ?>">
                            <h3 class="category-title"><?php echo $category->name; ?></h3>
                        </div>
                    <?php endforeach; ?>


                    <ul id="post_filter" data-layout="blog_newspaper">
                        <li>
                            <a data-view="latest" title="Latest" href="javascript:;" class="filter_active">
                                <i class="fa fa-clock-o"></i>Latest
                            </a>
                        </li>
                        <li>
                            <a data-view="trending" title="Trending" href="javascript:;">
                                <i class="fa fa-flash"></i>Trending
                            </a>
                        </li>
                        <li>
                            <a data-view="hot" title="Hot" href="javascript:;">
                                <i class="fa fa-fire"></i>Hot
                            </a>
                        </li>
                        <li>
                            <a data-view="editors_picks" title="Editor Picks" href="javascript:;">
                                <i class="fa fa-heart"></i>Editor Picks
                            </a>
                        </li>
                    </ul>


                    <!-- Inserisci qui il codice per visualizzare gli articoli del blog -->
                <?php else : ?>
                    <p><?php _e( 'Nessun articolo trovato.', 'perseowiki' ); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</main>


<?php
get_footer();
?>
