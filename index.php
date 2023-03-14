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
                <input type="search" class="searchBar" placeholder="Search WikiHerbalist..">
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
    </main>

    <?php 
    get_footer(); 
}
?>