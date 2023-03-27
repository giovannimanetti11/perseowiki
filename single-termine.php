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
            <!-- <button class="button edit-button" type="button"><i class="fa-solid fa-pen-to-square"></i> Modifica</button> -->
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

        <div class="article-info">
            <div class="post-date">
            <?php
                $data_pubblicazione = get_the_date();
                $data_ultimo_aggiornamento = get_the_modified_date();
                if ($data_pubblicazione != $data_ultimo_aggiornamento) {
                echo "Scheda pubblicata il $data_pubblicazione e aggiornata il $data_ultimo_aggiornamento";
                } else {
                echo "Scheda pubblicata il $data_pubblicazione";
                }
            ?>
            </div>
            <?php
            $words_per_minute = 200;
            $content = get_post_field( 'post_content', $post->ID );
            $word_count = str_word_count( strip_tags( $content ) );
            $reading_time = ceil( $word_count / $words_per_minute );
            ?>
            <div class="post-reading-time">
                <i class="fa-solid fa-clock"></i> Tempo di lettura: <?php echo $reading_time; ?> min
            </div>

        </div>
        

        <div class="post-content-text">
            <div class="index">
                
            </div>

            <div class="post-content-module">
                <div class="post-featured-image">
                <?php if ( has_post_thumbnail() ) { 
                    the_post_thumbnail( 'thumbnail' ); 
                    $thumbnail_id = get_post_thumbnail_id();
                    $thumbnail = get_post( $thumbnail_id );
                    $caption = get_the_post_thumbnail_caption();
                    $description = !empty( $thumbnail ) ? $thumbnail->post_content : '';
                    if ( ! empty( $description ) ) {
                        echo '<div class="wp-image-description">' . $description . '</div>';
                    }
                    if ( ! empty( $caption ) ) {
                        echo '<div class="wp-caption-text">' . $caption . '</div>';
                    }
                } ?>
                </div>
                <span></span>
            </div>


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