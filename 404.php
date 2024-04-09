<?php
// Verifica se l'utente ha eseguito una ricerca
if (isset($_GET['keywords'])) {
    // Includi il file di ricerca
    get_template_part('inc/search');
} else {
    get_header();
    ?>

    <main>
    <div class="homepage-hero">
            <div class="search">
                <input type="search" class="searchBar" placeholder="Cerca in WikiHerbalist..">
                <div id="clearSearch" style="display: none;">
                    <i class="fas fa-times"></i>
                </div>
                <i class="fas fa-search" id="iconSearch"></i>
            </div>
            <ul id="searchResults"></ul>
        </div>
        <div class="error404-wrapper">
            <h1 class="error404-title"><?php esc_html_e('404', 'perseowiki'); ?></h1>
            <p class="error404-message"><?php esc_html_e("La pagina che volevi visitare probabilmente non esiste più o ha cambiato url, cercala usando il form di ricerca oppure torna alla homepage", 'perseowiki'); ?>.</p>
            <div class="error404-buttons">
                <a id="error404-home-btn" class="btn btn-sm" href="<?php echo esc_url(home_url('/')); ?>">Homepage</a>
            </div>
        </div>
    </main>

    <?php
    get_footer();
}
?>
