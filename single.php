<?php get_header(); ?>

<main class="post-container">
    <?php 
    
    if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article class="post-content">

        <h1><?php the_title(); ?></h1>

        <?php $meta_box_value = get_post_meta( get_the_ID(), 'meta-box-nome-scientifico', true ); ?>
        <span class="meta-box-nome-scientifico"><?php echo esc_html( $meta_box_value ); ?></span>
        <div class="post-buttons">
            <button class="button print-button"><i class="fa fa-print" aria-hidden="true"></i> Stampa</button>
            <button class="button share-button"><i class="fa fa-share-alt" aria-hidden="true"></i> Condividi</button>
            <button class="button edit-button"><i class="fa-solid fa-pen-to-square"></i> Modifica</button>
        </div>

        <div class="post-content-text">
            <div class="post-content-module">
                <div class="post-featured-image">
                    <?php if ( has_post_thumbnail() ) { 
                        the_post_thumbnail( 'thumbnail' ); 
                    } ?>
                </div>
                <span></span>
                <div class="post-tags">
                    <?php $post_tags = wp_get_post_tags( get_the_ID() ); ?>
                    <?php if ( $post_tags ) : ?>
                        <ul class="post-tags-list">
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