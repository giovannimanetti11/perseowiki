<?php get_header(); ?>

<main class="post-container">
    <?php 
    
    if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article class="post-content" id="post-content">

        <h1><?php the_title(); ?></h1>

        <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-nome-scientifico', true ); ?>
        <?php if (!empty($meta_box_value)) : ?>
            <div class="meta-box-nome-scientifico"><?php echo esc_html( $meta_box_value ); ?></div>
        <?php endif; ?>

        <div class="post-buttons">
            <button class="button print-button" type="button" onclick="printArticle()"><i class="fa fa-print" aria-hidden="true"></i> Stampa</button>
            <button class="button share-button" type="button" onclick="openSharePopup()"><i class="fa fa-share-alt" aria-hidden="true"></i> Condividi</button>
            <button class="button edit-button" type="button"><i class="fa-solid fa-pen-to-square"></i> Modifica</button>
        </div>

        <!-- Popup -->
        <div class="popup" id="share-popup">
            <div class="popup-content">
                <div class="popup-header">
                    <i class="fa fa-share-alt" aria-hidden="true"></i>
                    <span>Condividi l'articolo</span>
                    <button class="close-button" onclick="closeSharePopup()">X</button>
                </div>
                <div class="popup-body">
                <span>Condividi sui social</span>
                <div class="social-icons">
                    <a href="#" class="social-icon facebook" onclick="shareUrl('https://www.facebook.com/sharer/sharer.php?u=<?php echo urldecode(get_permalink()); ?>')"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon twitter" onclick="shareUrl('https://twitter.com/intent/tweet?url=<?php echo urldecode(get_permalink()); ?>')"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="social-icon linked" onclick="shareUrl('https://www.linkedin.com/shareArticle?url=<?php echo urldecode(get_permalink()); ?>')"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
                </div>

                <span>URL</span>
                <div class="copy-link">
                    <button class="copy-button" onclick="copyToClipboard()"> <i class="fas fa-copy"></i> </button>
                    <a href="<?php the_permalink(); ?>" id="article-url"><?php the_permalink(); ?></a>
                </div>
                </div>
            </div>
        </div>


        <div class="post-content-text">
            <div class="post-content-module">
                <div class="post-featured-image">
                <?php if ( has_post_thumbnail() ) { 
                    the_post_thumbnail( 'thumbnail' ); 
                    $caption = get_the_post_thumbnail_caption();
                    if ( ! empty( $caption ) ) {
                        echo '<div class="wp-caption-text">' . $caption . '</div>';
                    }
                } ?>
                </div>
                <span></span>
                <div class="post-tags">
                <?php $post_tags = wp_get_post_tags( get_the_ID() ); ?>
                <?php if ( $post_tags ) : ?>
                    <ul class="post-tags-list">
                    <h3>Proprietà</h3>
                    <?php foreach ( $post_tags as $tag ) : ?>
                        <li>
                        <?php $tag_link = get_term_link( $tag ); ?>
                        <?php printf( '<a href="%s">%s</a>', esc_url( $tag_link ), esc_html( ucfirst( $tag->name ) ) ); ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                </div>
            </div>
            <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-nome-comune', true ); ?>
            <?php if (!empty($meta_box_value)) : ?>
                <div class="meta-box-nome-comune"><h3>Nome comune</h3> <?php echo esc_html( $meta_box_value ); ?></div>
            <?php endif; ?>
            <br>
            <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-parti-usate', true ); ?>
            <?php if (!empty($meta_box_value)) : ?>
                <div class="meta-box-parti-usate"><h3>Parti usate</h3> <?php echo esc_html( $meta_box_value ); ?></div>
            <?php endif; ?>
            <br>
            <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-costituenti', true ); ?>
            <?php if (!empty($meta_box_value)) : ?>
                <div class="meta-box-costituenti"><h3>Costituenti</h3> <?php echo esc_html( $meta_box_value ); ?></div>
            <?php endif; ?>
            <br>
            <?php the_content(); ?>
        </div>

    </article>

    <?php endwhile;

else :
    _e( 'Sorry, no posts were found.', 'textdomain' );
endif;

?>

</main>

<?php get_footer(); ?>