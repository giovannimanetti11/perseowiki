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
            <div id="posts-info"></div>
            <div id="posts-container"></div>
        </div>
        <div class="properties-container">
            <div class="properties-header">
            </div>
            <div class="properties-content">
            </div>
        </div>

    </main>

    <?php 
    get_footer(); 
}
?>
