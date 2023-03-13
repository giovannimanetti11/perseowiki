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
        </div>
    </main>

    <?php 
    get_footer(); 
}
?>