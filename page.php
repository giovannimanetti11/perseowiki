<?php get_header(); ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <main id="main" class="site-main" role="main">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <div class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </div>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>


                    </article>
                <?php endwhile; ?>
            </main>
        </div>
    </div>
</div>

<?php get_footer(); ?>
