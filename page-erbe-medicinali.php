<?php

/** 
* Template Name: Erbe medicinali 
*/ 

 get_header(); ?>


<div id="content" class="container">
    <h1 class="page-title">Piante medicinali</h1>

    <?php
    $terms_alphabetical = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));

    if ($terms_alphabetical->have_posts()) :
        $current_letter = '';
        echo '<div class="alphabetical-list">';
        while ($terms_alphabetical->have_posts()) : $terms_alphabetical->the_post();
            $first_letter = strtoupper(mb_substr(get_the_title(), 0, 1));
            if ($first_letter != $current_letter) {
                if ($current_letter != '') {
                    echo '</div>'; 
                }
                $current_letter = $first_letter;
                echo '<div class="letter-group">';
                echo '<div class="letter-title">' . $current_letter . '</div>';
            }
            $nome_scientifico = get_post_meta(get_the_ID(), 'meta-box-nome-scientifico', true);
            echo '<div class="name-scientific-name"><h3 class="post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            echo '<h4 class="card-scientific-name">(' . $nome_scientifico . ')</h4></div>';
        endwhile;
        echo '</div>'; 
        echo '</div>'; 
    else :
        echo '<p>Nessun termine trovato.</p>';
    endif;
    

    wp_reset_postdata();
    ?>

</div>


<?php get_footer(); ?>