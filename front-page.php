<?php 
// Verifica se l'utente ha eseguito una ricerca
if (isset($_GET['keywords'])) {
    // Includi il file di ricerca
    get_template_part( 'inc/search' );
} else {
    get_header(); 
    ?>

    <main>
        <div class="homepage-hero">
            <div class="search">
                <input type="search" class="searchBar" placeholder="Cerca in WikiHerbalist..">
                <i class="fas fa-times" id="clearSearch"></i>
                <i class="fas fa-search" id="iconSearch"></i>
            </div>
            <ul id="searchResults"></ul>
            <div class="category-menu">
                <?php wp_nav_menu( array( 'theme_location' => 'perseowiki-category-menu' ) ); ?>
                <div class="" id="iconLeftArrow">
                    <i class="fa-solid fa-arrow-left"></i>
                </div>
                <div class="" id="iconRightArrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <div class="home-content">

            <?php
            $home_id = get_option('page_on_front');
            $home_content = get_post($home_id);

            // Conta il numero totale di post pubblicati
            $count_posts = wp_count_posts()->publish;

            // Sostituisci [count_total] con il numero totale di post
            $home_content->post_content = str_replace('[count_total]', $count_posts, $home_content->post_content);

            echo apply_filters('the_content', $home_content->post_content);
            ?>

        </div>
        <div id="filterType">
            <div class="filterButtons">
                <button class="btn btn-active" id="alphabeticOrder">Ordine alfabetico</button>
                <button class="btn" id="propertiesList">Proprietà terapeutiche</button>
            </div>
        </div>
        <div class="alphabetic-container">
            <div id="alphabet">
                <?php foreach(range('A', 'Z') as $char): ?>
                    <a href="#" class="alphabet-link" data-letter="<?php echo $char; ?>"><?php echo $char; ?></a>
                <?php endforeach; ?>
            </div>
            <div id="posts-info">
            </div>
            <?php foreach(range('A', 'Z') as $char): ?>
                
                <div id="posts-container-<?php echo $char; ?>" class="posts-container" style="display: <?php echo $char === 'A' ? 'block' : 'none'; ?>">
                    <div class="card-deck">
                        <?php
                            // Esegui la query per ciascuna lettera
                            $args = array(
                                'post_type' => 'post',
                                'posts_per_page' => -1,
                                'orderby' => 'title',
                                'order' => 'ASC',
                                'start_char' => $char,
                            );
                            $query = new WP_Query($args);

                            // Fornisci l'output per ciascun post
                            while($query->have_posts()) : $query->the_post();
                                $title = get_the_title();
                                $link = get_permalink();
                                $image = get_the_post_thumbnail_url(null, 'medium');
                                $alt = get_the_title();
                                $nome_scientifico = get_post_meta(get_the_ID(), 'meta-box-nome-scientifico', true);
                                $tossica = get_post_meta(get_the_ID(), '_tossica', true);
                        ?>
                        <a href="<?php echo $link; ?>" class="card-link">
                            <div class="card">
                                <img class="card-img-top" src="<?php echo $image; ?>" alt="<?php echo $alt; ?>">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo $title; ?></h3>
                                    <h4 class="card-scientific-name"><?php echo $nome_scientifico; ?></h4>
                                    <?php if (!empty($tossica)) : ?>
                                        <i class="fa-solid fa-skull-crossbones" id="icon-skull" title="Pianta tossica"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        $properties_and_herbs = get_therapeutic_properties_and_herbs(); // Query in functions.php:829
        ?>
        <div class="properties-container">
            <div class="properties-header">
            </div>
            <div class="properties-content">
                <?php foreach ($properties_and_herbs as $index => $item): ?>
                <div class="property-herbs-row <?php echo $index % 2 === 0 ? 'even-row' : 'odd-row'; ?>">
                    <a class="property-name" href="/tag/<?php echo $item->property; ?>">
                        <?php echo $item->property; ?>
                    </a>
                    <div class="herbs-list">
                        <?php
                        $herbs = explode(', ', $item->herbs);
                        foreach ($herbs as $i => $herb):
                            $herb = trim($herb);
                            $link = sanitize_title($herb);
                        ?>
                        <a href="/<?php echo $link; ?>">
                            <?php echo $herb; ?>
                        </a>
                        <?php if ($i < count($herbs) - 1) echo ',&nbsp;'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>



    </main>

    <?php 
    get_footer(); 
}
?>
