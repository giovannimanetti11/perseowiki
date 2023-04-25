<?php get_header(); ?>


<main class="post-container">
    <?php custom_breadcrumb(); ?>
    <?php 
    
    if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article class="post-content" id="post-content">

        <div class="images-container">
            <div class="post-featured-image">
                <?php
                if (has_post_thumbnail()) {
                $full_image_url = wp_get_attachment_image_url(get_post_thumbnail_id(), 'full');
                $alt_text = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true); 
                the_post_thumbnail('medium', array('id' => 'featured-image', 'data-full-image-url' => $full_image_url, 'alt' => $alt_text));
                }
                ?>
            </div>

            <div class="additional-images-thumbnails">
                <?php
                $additional_images_raw = get_post_meta(get_the_ID(), 'additional_images', true);
                $additional_images = !empty($additional_images_raw) ? json_decode($additional_images_raw, true) : array();

                foreach ($additional_images as $attachment_id) {
                if ($attachment_id > 0) {
                    $thumbnail_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                    $full_image_url = wp_get_attachment_image_url($attachment_id, 'full');
                    $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true); 
                    echo '<img src="' . esc_url($thumbnail_url) . '" data-full-image-url="' . esc_url($full_image_url) . '" class="additional-image-thumbnail" alt="' . esc_attr($alt_text) . '" />';
                }
                }
                ?>
            </div>
        </div>




        <div class="article-title">
            <h1><?php the_title(); ?></h1> <i class="fa-solid fa-volume-up" data-text="<?php echo esc_attr(get_the_title()); ?>" data-language="it"></i>
        </div>

        <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-nome-scientifico', true ); ?>
        <?php if (!empty($meta_box_value)) : ?>
            <div class="meta-box-nome-scientifico"><?php echo esc_html( $meta_box_value ); ?></div>
        <?php endif; ?>

        

        <div class="post-buttons">
            <button class="button print-button" type="button" onclick="printArticle()"><i class="fa fa-print" aria-hidden="true"></i> Stampa</button>
            <button class="button share-button" type="button" onclick="openSharePopup()"><i class="fa fa-share-alt" aria-hidden="true"></i> Condividi</button>
            <button class="button citation-button" type="button" onclick="openCitationPopup()"><i class="fas fa-quote-left"></i> Cita</button>

            <!-- <button class="button edit-button" type="button"><i class="fa-solid fa-pen-to-square"></i> Modifica</button> -->
        </div>

        <!-- Popup -->
        <div class="popup" id="share-popup">
            <div class="popup-content">
                <div class="popup-header">
                    <i class="fa fa-share-alt" aria-hidden="true"></i>
                    <span>Condividi l'articolo</span>
                    <button class="close-button" onclick="closeSharePopup()"><i class="fa fa-times"></i></button>
                </div>
                <div class="popup-body">
                    <span>Condividi sui social</span>
                    <div class="social-icons">
                        <a href="#" class="social-icon facebook" onclick="shareUrl('https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>')"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon twitter" onclick="shareUrl('https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>')"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="social-icon linkedin" onclick="shareUrl('https://www.linkedin.com/shareArticle?url=<?php echo urlencode(get_permalink()); ?>')"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
                        <a href="#" class="social-icon whatsapp" onclick="shareUrlWhatsApp(); return false;"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                        <a href="#" class="social-icon telegram" onclick="shareUrl('https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>')"><i class="fab fa-telegram" aria-hidden="true"></i></a>
                    </div>


                    <span>URL</span>
                    <div class="copy-link">
                        <button class="copy-button" onclick="copyToClipboard()"> <i class="fas fa-copy"></i> </button>
                        <a href="<?php the_permalink(); ?>" id="article-url"><?php the_permalink(); ?></a>
                        <textarea id="hidden-url" readonly><?php the_permalink(); ?></textarea>
                    </div>
                    <div id="copy-message" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Citation Popup -->
        <div class="popup" id="citation-popup">
            <div class="popup-content">
                <div class="popup-header">
                    <i class="fas fa-quote-left" aria-hidden="true"></i>
                    <span>Cita l'articolo</span>
                    <button class="close-button" onclick="closeCitationPopup()"><i class="fa fa-times"></i></button>
                </div>
                <div class="popup-body">
                    <p>Nonostante sia stato fatto il possibile per seguire le regole dello stile di citazione, potrebbero esserci alcune discrepanze. In caso di domande, si prega di consultare il manuale di stile appropriato o altre fonti.</p>

                    <label for="citation-style">Seleziona formato di citazione:</label>
                    <select id="citation-style" onchange="generateCitation()">
                        <option value="APA">APA 7th edition</option>
                        <option value="MLA">MLA 8th edition</option>
                        <option value="Wikipedia">Wikipedia</option>
                    </select>

                    <div class="citation">
                        <button class="copy-button" onclick="copyCitationToClipboard()"> <i class="fas fa-copy"></i> </button>
                        <span id="citation-text"></span>
                    </div>
                    <div id="citation-copy-message" style="display: none;"></div>
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

            <?php
                $tossica = get_post_meta(get_the_ID(), '_tossica', true);
            ?>

            <div class="post-content-module">
                <div class="post-tags">
                    <?php $post_tags = wp_get_post_tags(get_the_ID()); ?>
                    <?php if ($post_tags) : ?>
                        <ul class="post-tags-list">
                        <h3>Proprietà</h3>
                        <?php foreach ($post_tags as $tag) : ?>
                            <li>
                            <?php $tag_link = get_term_link($tag); ?>
                            <?php printf('<a href="%s">%s</a>', esc_url($tag_link), esc_html(ucfirst($tag->name))); ?>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <?php if ($tossica) : ?>
                    <a href="#section-10" class="alert alert-danger" role="alert">
                        Pianta tossica
                    </a>
                <?php endif; ?>
            </div>



            <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-nome-scientifico', true ); ?>
            <?php if (!empty($meta_box_value)) : ?>
                <div class="meta-box-nome-scientifico"><h3>Nome scientifico</h3> <?php echo esc_html( $meta_box_value ); ?></div>
            <?php endif; ?>
            <br>
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
                <div class="meta-box-costituenti"><h3>Fitochimica</h3> <?php echo esc_html( $meta_box_value ); ?></div>
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

<script>
  var authorName = "<?php echo get_the_author(); ?>";
  var displayAuthor = authorName === "WHAdmin" ? "Editors of WikiHerbalist" : authorName;

  var articleData = {
    author: displayAuthor,
    title: "<?php echo addslashes(get_the_title()); ?>",
    publishedDate: "<?php echo get_the_date('Y'); ?>",
    lastModifiedDate: "<?php echo get_the_modified_date('Y-m-d'); ?>",
    accessDate: "<?php echo date('Y-m-d'); ?>",
    url: "<?php echo get_permalink(); ?>"
  };
</script>

<?php get_footer(); ?>