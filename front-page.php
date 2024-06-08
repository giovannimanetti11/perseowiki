<?php 
// Check if the user has performed a search
if (isset($_GET['keywords'])) {
    // Include the search file
    get_template_part('inc/search');
} else {
    get_header();

    $all_posts_data = [];
    foreach (range('A', 'Z') as $char) {
        $args = [
            'post_type' => 'post',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'start_char' => $char,
        ];
        $query = new WP_Query($args);
        while ($query->have_posts()) : $query->the_post();
            $all_posts_data[$char][] = [
                'title' => get_the_title(),
                'link' => esc_url(get_permalink()),
                'image' => get_the_post_thumbnail_url(null, 'medium'),
                'alt' => get_the_title(),
                'scientific_name' => esc_html(get_post_meta(get_the_ID(), 'meta-box-nome-scientifico', true)),
                'toxic' => get_post_meta(get_the_ID(), '_toxic', true),
            ];
        endwhile;
        wp_reset_postdata();
    }
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

        <div class="home-content">

            <?php
            $home_id = get_option('page_on_front');
            $home_content = get_post($home_id);

            // Count the total number of published posts
            $count_posts = wp_count_posts()->publish;

            // Replace [count_total] with the total number of posts
            $home_content->post_content = str_replace('[count_total]', $count_posts, $home_content->post_content);

            echo apply_filters('the_content', $home_content->post_content);
            ?>

        </div>
        <div id="filterType">
            <div class="filterButtons">
                <button class="btn btn-active" id="alphabeticOrder">Erbe medicinali</button>
                <button class="btn" id="propertiesList">Proprietà terapeutiche</button>
            </div>
        </div>
        <div class="alphabetic-container">
            <div id="alphabet">
                <?php foreach (range('A', 'Z') as $char): ?>
                    <a href="#" class="alphabet-link" data-letter="<?php echo esc_attr($char); ?>"><?php echo $char; ?></a>
                <?php endforeach; ?>
            </div>
            <div id="posts-info">
            </div>
            <?php foreach (range('A', 'Z') as $char): ?>
                <div id="posts-container-<?php echo $char; ?>" class="posts-container" style="display: <?php echo $char === 'A' ? 'block' : 'none'; ?>">
                    <div class="card-deck">
                        <?php if (isset($all_posts_data[$char])): ?>
                            <?php foreach ($all_posts_data[$char] as $data): ?>
                                <a href="<?php echo $data['link']; ?>" class="card-link">
                                    <div class="card">
                                        <img class="card-img-top" src="<?php echo $data['image']; ?>" alt="<?php echo $data['alt']; ?>">
                                        <div class="card-body">
                                            <h2 class="card-title"><?php echo $data['title']; ?></h2>
                                            <h3 class="card-scientific-name"><?php echo $data['scientific_name']; ?></h3>
                                            <?php if (!empty($data['toxic'])) : ?>
                                                <i class="fa-solid fa-skull-crossbones" id="icon-skull" title="Toxic Plant"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        $properties_and_herbs = get_therapeutic_properties_and_herbs(); // Query in functions.php:1205
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
