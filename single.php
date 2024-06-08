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
            <h1><?php the_title(); ?></h1> 
            <?php
            $revision_data = get_post_meta(get_the_ID(), '_revision_data', true);
            $revision_data = $revision_data ? json_decode($revision_data, true) : [];
            $formatted_date = '';

            if (!empty($revision_data)) {
                foreach ($revision_data as $revision) {
                    $reviewer_post = get_post($revision['reviewer_id']);
                    $formatted_date = date_i18n('j F Y', strtotime($revision['date']));
                    break; 
                }
            }
            ?>
            <?php if (!empty($formatted_date)): ?>
                <div class="reviewed">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/icon_verified.svg" class="reviewed_icon" />
                    <span class="tooltip">Scheda revisionata il <?php echo esc_html($formatted_date); ?></span>
                </div>
            <?php endif; ?>
            <span class="voice-icon" data-text="<?php echo esc_attr(get_the_title()); ?>" data-language="it">
                <i class="fa-solid fa-volume-high"></i>
            </span>
        </div>


        <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-nome-scientifico', true ); ?>
        <?php if (!empty($meta_box_value)) : ?>
            <div class="meta-box-nome-scientifico"><?php echo esc_html( $meta_box_value ); ?></div>
        <?php endif; ?>


        <div id="sinonimi"></div>
        

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
                        <a href="#" class="social-icon twitter" onclick="shareUrl('https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>')"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="social-icon linkedin" onclick="shareUrl('https://www.linkedin.com/shareArticle?url=<?php echo urlencode(get_permalink()); ?>')"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
                        <a href="#" class="social-icon whatsapp" onclick="shareUrlWhatsApp(this); return false;" data-url="<?php echo urlencode(get_permalink()); ?>"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                        <a href="#" class="social-icon telegram" onclick="shareUrl('https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>')"><i class="fab fa-telegram" aria-hidden="true"></i></a>
                    </div>


                    <span>URL</span>
                    <div class="copy-link">
                        <button class="copy-button" onclick="copyToClipboard()"> <i class="fas fa-copy"></i> </button>
                        <?php the_permalink(); ?>
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
                global $post;
                $data_pubblicazione = get_the_date();
                $data_ultimo_aggiornamento = get_the_modified_date();
                if ($data_pubblicazione != $data_ultimo_aggiornamento) {
                echo "Scheda pubblicata il $data_pubblicazione e aggiornata il $data_ultimo_aggiornamento";
                } else {
                echo "Scheda pubblicata il $data_pubblicazione";
                }

                $author_id = get_post_meta(get_the_ID(), "meta-box-author-dropdown", true);
                if ($author_id) {
                    $author_post = get_post($author_id);
                    $author_name = $author_post->post_title;
                    echo "<div class='post-author'>Di: " . esc_html($author_name) . "</div>";
                } else {
                    echo "<div class='post-author'>Di: Redazione di Wikiherbalist</div>";
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

            <?php
                $scientific_name = get_post_meta(get_the_ID(), "meta-box-nome-scientifico", true);
                $publication_count = fetch_pubmed_publications_count($scientific_name);
                $parts = explode(' ', $scientific_name);
                $binomial_name = $parts[0] . ' ' . $parts[1];

                $pubmed_query_url = "https://pubmed.ncbi.nlm.nih.gov/?term=" . urlencode($binomial_name);

                echo '<div class="pubmed-logo">
                        <a href="' . esc_url($pubmed_query_url) . '" target="_blank">
                            <img src="' . get_stylesheet_directory_uri() . '/img/pubmed_logo.png" alt="PubMed logo" />
                        </a>
                        <span><a href="' . esc_url($pubmed_query_url) . '" target="_blank">' . esc_html(number_format($publication_count, 0, '.', '.')) . '</a> pubblicazioni</span>
                    </div>';
            ?>

            <div class="plant-details">
                <div id="classification-container">
                </div>
                
                <div class="map-container">
                    <div id="map" class="map">
                        <img id="loading" src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif"/>
                    </div>
                    <div id="observation-info">
                        Numero di osservazioni umane nel mondo di
                        <span id="plant-name"></span> nel 2023.
                        <em>Credits:</em> <a href="https://www.gbif.org/">GBIF</a> | <a href="https://www.openstreetmap.org/" rel="nofollow">OpenStreetMap</a>.
                    </div>
                </div>

                
            </div>
            
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
                        <h3>Proprietà terapeutiche</h3>
                        <div class="tags-container">
                        <?php foreach ($post_tags as $tag) : ?>
                            <li>
                            <?php $tag_link = get_term_link($tag); ?>
                            <?php printf('<a href="%s">%s</a>', esc_url($tag_link), esc_html(ucfirst($tag->name))); ?>
                            </li>
                        <?php endforeach; ?>
                        </div>
                        </ul>
                    <?php endif; ?>
                </div>
                <?php if ($tossica) : ?>
                    <div class="alert-container">
                        <a href="#section-11" class="alert alert-danger" role="alert">
                            Pianta tossica
                        </a>
                    </div>
                <?php endif; ?>
            </div>



            <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-nome-scientifico', true ); ?>
            <?php if (!empty($meta_box_value)) : ?>
                <div id="scientificName" class="meta-box-nome-scientifico" data-scientific-name="<?php echo esc_html( $meta_box_value ); ?>">
                    <h3>Nome scientifico</h3> <?php echo esc_html( $meta_box_value ); ?>
                </div>
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

<?php

$author_id = get_post_meta(get_the_ID(), "meta-box-author-dropdown", true);
$displayAuthor = "WHAdmin"; 

if ($author_id) {
    $author_post = get_post($author_id);
    if ($author_post) {
        $displayAuthor = $author_post->post_title;
    }
} else {
    $displayAuthor = get_the_author();
}

$displayAuthorJs = esc_js($displayAuthor);
?>

<script>
  var displayAuthor = "<?php echo $displayAuthorJs; ?>";
  displayAuthor = displayAuthor === "WHAdmin" ? "Editors of WikiHerbalist" : displayAuthor;

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